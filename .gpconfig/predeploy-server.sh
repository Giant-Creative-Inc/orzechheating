#!/usr/bin/env bash

# GridPane hybrid deployment: prepare and validate the release as root.

set -Eeuo pipefail
umask 022

readonly HOOK_NAME="predeploy-server"
THEME_DIR=""

log() {
  printf '[%s] [%s] %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')" "$HOOK_NAME" "$*"
}

fail() {
  log "ERROR: $*" >&2
  exit 1
}

cleanup() {
  if [[ -n "$THEME_DIR" && -d "$THEME_DIR/node_modules" ]]; then
    log "Removing build-only Node dependencies"
    rm -rf -- "$THEME_DIR/node_modules"
  fi
}

on_error() {
  local exit_code=$?
  log "ERROR: command failed at line ${BASH_LINENO[0]} with exit code ${exit_code}" >&2
  exit "$exit_code"
}

trap cleanup EXIT
trap on_error ERR

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)"
readonly SCRIPT_DIR
readonly DEPLOY_CONFIG="$SCRIPT_DIR/deployment.conf"
[[ -r "$DEPLOY_CONFIG" ]] || fail "Shared deployment config is unavailable: $DEPLOY_CONFIG"
# shellcheck disable=SC1090
source "$DEPLOY_CONFIG"

[[ -n "${GP_GIT_RELEASE_PATH:-}" ]] || fail "GP_GIT_RELEASE_PATH is not set"
[[ -d "$GP_GIT_RELEASE_PATH" ]] || fail "Release path does not exist: $GP_GIT_RELEASE_PATH"

RELEASE_PATH="$(cd "$GP_GIT_RELEASE_PATH" && pwd -P)"
readonly RELEASE_PATH
RELEASES_DIR="$(dirname "$RELEASE_PATH")"
readonly RELEASES_DIR
SITE_ROOT="$(dirname "$RELEASES_DIR")"
readonly SITE_ROOT
readonly LIVE_PATH="$SITE_ROOT/$GP_DEPLOY_LIVE_DIR_NAME"
SITE_DOMAIN="$(basename "$SITE_ROOT")"
readonly SITE_DOMAIN
THEME_DIR="$RELEASE_PATH/$GP_DEPLOY_THEME_PATH"
readonly THEME_DIR
RELEASE_ID="$(basename "$RELEASE_PATH")"
readonly RELEASE_ID
readonly RELEASE_MARKER="$THEME_DIR/$GP_DEPLOY_MARKER_FILE"

[[ "$RELEASE_PATH" == "$SITE_ROOT/$GP_DEPLOY_RELEASES_DIR_NAME/"* ]] || fail "Unexpected GridPane release path: $RELEASE_PATH"
[[ -d "$LIVE_PATH" ]] || fail "Live WordPress path does not exist: $LIVE_PATH"
[[ -f "$THEME_DIR/$GP_DEPLOY_PACKAGE_FILE" ]] || fail "Theme package file not found: $THEME_DIR/$GP_DEPLOY_PACKAGE_FILE"
[[ -f "$THEME_DIR/$GP_DEPLOY_LOCK_FILE" ]] || fail "Theme lock file not found: $THEME_DIR/$GP_DEPLOY_LOCK_FILE"
[[ -n "$SITE_DOMAIN" && "$SITE_DOMAIN" != "." && "$SITE_DOMAIN" != "/" ]] || fail "Could not derive the site domain"

for required_command in "${GP_DEPLOY_BUILD_REQUIRED_COMMANDS[@]}"; do
  command -v "$required_command" >/dev/null 2>&1 || fail "Required command is unavailable: $required_command"
done

SITE_USER="$(stat -c '%U' "$LIVE_PATH")"
readonly SITE_USER
SITE_GROUP="$(stat -c '%G' "$LIVE_PATH")"
readonly SITE_GROUP
[[ -n "$SITE_USER" && "$SITE_USER" != "UNKNOWN" ]] || fail "Could not derive the site system user"
[[ -n "$SITE_GROUP" && "$SITE_GROUP" != "UNKNOWN" ]] || fail "Could not derive the site system group"

log "Preparing $SITE_DOMAIN release $RELEASE_ID as $SITE_USER:$SITE_GROUP"
log "Using Node $(node --version) and npm $(npm --version)"

cd "$THEME_DIR"
log "Installing locked theme dependencies"
timeout "$GP_DEPLOY_INSTALL_TIMEOUT" "${GP_DEPLOY_INSTALL_COMMAND[@]}"

log "Building production theme assets"
timeout "$GP_DEPLOY_BUILD_TIMEOUT" "${GP_DEPLOY_BUILD_COMMAND[@]}"

shopt -s nullglob
css_assets=("$THEME_DIR/$GP_DEPLOY_CSS_OUTPUT_DIR"/*.css)
js_assets=("$THEME_DIR/$GP_DEPLOY_JS_OUTPUT_DIR"/*.js)
(( ${#css_assets[@]} > 0 )) || fail "The build produced no CSS assets"
(( ${#js_assets[@]} > 0 )) || fail "The build produced no JavaScript assets"

for asset in "${css_assets[@]}" "${js_assets[@]}"; do
  [[ -s "$asset" ]] || fail "Build output is empty: $asset"
done

printf '%s\n' "$RELEASE_ID" > "$RELEASE_MARKER"
chown -R "$SITE_USER:$SITE_GROUP" "$RELEASE_PATH"

log "Validated ${#css_assets[@]} CSS and ${#js_assets[@]} JavaScript assets"
log "Release preparation completed successfully"
