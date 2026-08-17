#!/usr/bin/env bash

# GridPane hybrid deployment: validate the live WordPress installation as the site user.

set -Eeuo pipefail
umask 022

readonly HOOK_NAME="predeploy"

log() {
  printf '[%s] [%s] %s\n' "$(date -u '+%Y-%m-%dT%H:%M:%SZ')" "$HOOK_NAME" "$*"
}

fail() {
  log "ERROR: $*" >&2
  exit 1
}

on_error() {
  local exit_code=$?
  log "ERROR: command failed at line ${BASH_LINENO[0]} with exit code ${exit_code}" >&2
  exit "$exit_code"
}

trap on_error ERR

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd -P)"
readonly SCRIPT_DIR
readonly DEPLOY_CONFIG="$SCRIPT_DIR/deployment.conf"
[[ -r "$DEPLOY_CONFIG" ]] || fail "Shared deployment config is unavailable: $DEPLOY_CONFIG"
# shellcheck disable=SC1090
source "$DEPLOY_CONFIG"

[[ -n "${GP_GIT_RELEASE_PATH:-}" ]] || fail "GP_GIT_RELEASE_PATH is not set"
RELEASE_PATH="$(cd "$GP_GIT_RELEASE_PATH" && pwd -P)"
readonly RELEASE_PATH
SITE_ROOT="$(dirname "$(dirname "$RELEASE_PATH")")"
readonly SITE_ROOT
readonly WP_PATH="$SITE_ROOT/$GP_DEPLOY_LIVE_DIR_NAME"

[[ "$RELEASE_PATH" == "$SITE_ROOT/$GP_DEPLOY_RELEASES_DIR_NAME/"* ]] || fail "Unexpected GridPane release path: $RELEASE_PATH"
[[ -d "$WP_PATH" ]] || fail "Live WordPress path does not exist: $WP_PATH"
for required_command in "${GP_DEPLOY_WP_REQUIRED_COMMANDS[@]}"; do
  command -v "$required_command" >/dev/null 2>&1 || fail "Required command is unavailable: $required_command"
done

log "Validating WordPress at $WP_PATH"
timeout "$GP_DEPLOY_WP_CHECK_TIMEOUT" wp core is-installed --path="$WP_PATH"
timeout "$GP_DEPLOY_WP_CHECK_TIMEOUT" wp db check --path="$WP_PATH" --quiet
log "WordPress installation and database validation completed successfully"
