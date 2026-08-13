#!/usr/bin/env bash

# GridPane hybrid deployment: schedule post-rsync permissions, cache, and health tasks as root.

set -Eeuo pipefail
umask 022

readonly HOOK_NAME="postdeploy-server"

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
readonly LIVE_PATH="$SITE_ROOT/$GP_DEPLOY_LIVE_DIR_NAME"
SITE_DOMAIN="$(basename "$SITE_ROOT")"
readonly SITE_DOMAIN
RELEASE_ID="$(basename "$RELEASE_PATH")"
readonly RELEASE_ID
readonly SOURCE_MARKER="$RELEASE_PATH/$GP_DEPLOY_THEME_PATH/$GP_DEPLOY_MARKER_FILE"
readonly LIVE_MARKER="$LIVE_PATH/$GP_DEPLOY_THEME_PATH/$GP_DEPLOY_MARKER_FILE"
readonly LOG_DIR="$SITE_ROOT/$GP_DEPLOY_LOG_DIR_NAME"
readonly WORKER_LOG="$LOG_DIR/$GP_DEPLOY_LOG_PREFIX-$RELEASE_ID.log"

[[ "$RELEASE_PATH" == "$SITE_ROOT/$GP_DEPLOY_RELEASES_DIR_NAME/"* ]] || fail "Unexpected GridPane release path: $RELEASE_PATH"
[[ -d "$LIVE_PATH" ]] || fail "Live WordPress path does not exist: $LIVE_PATH"
[[ -f "$SOURCE_MARKER" ]] || fail "Release marker is missing: $SOURCE_MARKER"
[[ -d "$LOG_DIR" ]] || fail "GridPane log directory does not exist: $LOG_DIR"
[[ -n "$SITE_DOMAIN" && "$SITE_DOMAIN" != "." && "$SITE_DOMAIN" != "/" ]] || fail "Could not derive the site domain"

for required_command in "${GP_DEPLOY_POST_REQUIRED_COMMANDS[@]}"; do
  command -v "$required_command" >/dev/null 2>&1 || fail "Required command is unavailable: $required_command"
done

log "Scheduling post-rsync tasks for $SITE_DOMAIN release $RELEASE_ID"

# The variables in this single-quoted program intentionally expand in the worker process.
# shellcheck disable=SC2016
nohup bash -c '
  set -u

  release_id=$1
  live_marker=$2
  site_domain=$3
  worker_log=$4
  wait_attempts=$5
  wait_interval=$6
  post_rsync_delay=$7
  permissions_timeout=$8
  permissions_attempts=$9
  permissions_retry_interval=${10}
  cache_purge_timeout=${11}
  health_scheme=${12}
  health_path=${13}
  health_attempts=${14}
  health_retry_interval=${15}
  health_max_redirects=${16}
  health_connect_timeout=${17}
  health_max_time=${18}

  exec >>"$worker_log" 2>&1

  worker_log_message() {
    printf "[%s] [postdeploy-worker] %s\n" "$(date -u "+%Y-%m-%dT%H:%M:%SZ")" "$*"
  }

  worker_log_message "Waiting for GridPane hybrid rsync of $release_id"
  marker_found=false
  for ((attempt = 1; attempt <= wait_attempts; attempt++)); do
    active_release=""
    if [[ -f "$live_marker" ]]; then
      IFS= read -r active_release < "$live_marker" || true
    fi
    if [[ "$active_release" == "$release_id" ]]; then
      marker_found=true
      break
    fi
    sleep "$wait_interval"
  done

  if [[ "$marker_found" != true ]]; then
    worker_log_message "ERROR: release marker was not activated after $wait_attempts checks"
    exit 0
  fi

  worker_log_message "Release marker is live; waiting $post_rsync_delay seconds for filesystem activity to settle"
  sleep "$post_rsync_delay"

  permissions_fixed=false
  for ((attempt = 1; attempt <= permissions_attempts; attempt++)); do
    worker_log_message "Repairing GridPane file permissions for $site_domain (attempt $attempt)"
    if timeout "$permissions_timeout" gp fix perms "$site_domain"; then
      permissions_fixed=true
      worker_log_message "GridPane file permissions repaired successfully"
      break
    fi
    worker_log_message "ERROR: GridPane permission repair attempt $attempt failed"
    if (( attempt < permissions_attempts )); then
      sleep "$permissions_retry_interval"
    fi
  done

  if [[ "$permissions_fixed" != true ]]; then
    worker_log_message "ERROR: GridPane file permission repair failed after $permissions_attempts attempts"
  fi

  worker_log_message "Release marker is live; purging GridPane caches"
  if ! timeout "$cache_purge_timeout" gp fix cached "$site_domain"; then
    worker_log_message "ERROR: GridPane cache purge failed"
  fi

  health_url="$health_scheme://$site_domain$health_path"
  for ((attempt = 1; attempt <= health_attempts; attempt++)); do
    http_status="$(curl --silent --show-error --location --max-redirs "$health_max_redirects" --connect-timeout "$health_connect_timeout" --max-time "$health_max_time" --output /dev/null --write-out "%{http_code}" "$health_url" 2>>"$worker_log")"
    case "$http_status" in
      2??|3??)
        worker_log_message "Health check passed with HTTP $http_status: $health_url"
        exit 0
        ;;
      *)
        worker_log_message "Health check attempt $attempt returned HTTP ${http_status:-000}"
        sleep "$health_retry_interval"
        ;;
    esac
  done

  worker_log_message "ERROR: health check did not return HTTP 2xx or 3xx"
  exit 0
' postdeploy-worker \
  "$RELEASE_ID" \
  "$LIVE_MARKER" \
  "$SITE_DOMAIN" \
  "$WORKER_LOG" \
  "$GP_DEPLOY_RSYNC_WAIT_ATTEMPTS" \
  "$GP_DEPLOY_RSYNC_WAIT_INTERVAL" \
  "$GP_DEPLOY_POST_RSYNC_DELAY" \
  "$GP_DEPLOY_PERMISSIONS_TIMEOUT" \
  "$GP_DEPLOY_PERMISSIONS_ATTEMPTS" \
  "$GP_DEPLOY_PERMISSIONS_RETRY_INTERVAL" \
  "$GP_DEPLOY_CACHE_PURGE_TIMEOUT" \
  "$GP_DEPLOY_HEALTH_SCHEME" \
  "$GP_DEPLOY_HEALTH_PATH" \
  "$GP_DEPLOY_HEALTH_ATTEMPTS" \
  "$GP_DEPLOY_HEALTH_RETRY_INTERVAL" \
  "$GP_DEPLOY_HEALTH_MAX_REDIRECTS" \
  "$GP_DEPLOY_HEALTH_CONNECT_TIMEOUT" \
  "$GP_DEPLOY_HEALTH_MAX_TIME" \
  </dev/null >/dev/null 2>&1 &

log "Post-rsync worker started; results will be written to $WORKER_LOG"
