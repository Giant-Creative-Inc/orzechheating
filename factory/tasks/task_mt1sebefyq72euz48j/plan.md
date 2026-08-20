# Execution plan — [Incident] https://orzechheating.ca returned HTTP 503

Task: task_mt1sebefyq72euz48j · Category: monitoring · Risk: high

## Summary
This is a security-relevant monitoring incident: orzechheating.ca returned HTTP 503 during the window while Cloudflare telemetry shows a large burst of 526 (invalid SSL cert at origin) responses and 504 gateway timeouts, driven mostly by automated attack/scanner traffic probing for PHP webshells. I will provide an incident timeline, a confidence-rated probable cause with evidence gaps, and safe recovery/prevention recommendations that require human approval before any production change.

## Implementation plan
1. Build incident timeline from evidence: detection at 2026-08-20T16:50:57Z (probe window start), external probe returns HTTP 503 (Cloudflare edge) at 261ms latency, incident window ends 2026-08-20T17:20:57Z; no resolved timestamp recorded (preflight shows incident still open).
2. Correlate Cloudflare HTTP status distribution: of 1309 requests, 338 were 526 (SSL handshake failed between Cloudflare and origin — invalid/expired origin certificate) and 7 were 504 (origin/gateway timeout). This indicates an origin-side TLS or availability problem, not a healthy origin.
3. Note that GridPane origin log analysis shows severity=none / total=0, meaning either the origin was not serving requests (consistent with 526) or origin logs did not capture the failing edge handshakes — this is an evidence gap to flag.
4. Classify the request pattern: the overwhelming majority of 526 paths are automated malicious scans for PHP webshells (/wso.php, /xxw.php, /wp-*.php variants, /wp-content/plugins/hellopress/wp_filemanager.php). Cloudflare managed firewall blocked only 2 events, so most scanner traffic reached the origin edge path.
5. State probable cause with confidence: PRIMARY (medium-high confidence) origin TLS/availability failure — Cloudflare 526 responses show the origin certificate was invalid or origin was unreachable during the window, producing the 503/5xx seen externally. CONTRIBUTING (medium) automated bot/scanner load probing for webshells amplified error volume and may have exhausted origin resources causing 504s.
6. Recommend safe recovery steps for human execution on staging first, then production only after approval: (1) verify origin certificate validity/expiry and renew if expired; (2) confirm origin web/PHP service (GridPane) is running and reachable on the Cloudflare-configured origin port; (3) verify Cloudflare SSL/TLS mode matches origin cert config (Full/Full-strict vs origin cert); (4) re-run external probe to confirm 200 recovery and record resolved timestamp.
7. Recommend prevention steps (all human-approved): enable/renew Cloudflare Origin CA certificate with auto-renew monitoring; add origin certificate-expiry alerting; tighten Cloudflare WAF managed rules and add rate-limiting plus rules blocking common webshell/PHP scan paths; add uptime + 5xx/526 alerting thresholds; ensure GridPane origin logging captures TLS handshake failures to close the observability gap.
8. Flag for human review: no source-code or config diff is in scope; all remediation touches production infrastructure (certs, WAF, origin services) and must go through explicit human approval — this task is investigative/advisory, not a file change.

## Affected areas
- Cloudflare zone 18c07e8cce178fcd52143150d05cf4a0 (SSL/TLS mode, Origin CA cert, WAF/rate-limit rules)
- GridPane origin server for site_orzech (origin TLS certificate, web/PHP service health, logging config)
- orzechheating.ca production availability and monitoring/alerting configuration

## QA checklist
- [ ] Confirm the incident timeline documents detection (16:50Z probe), the 526/504 evidence window, absence of recorded deployments, and the missing recovery timestamp.
- [ ] Confirm probable cause is stated with confidence level (origin TLS/526 primary, scanner load contributing) and explicitly lists evidence gaps (empty GridPane logs, no resolved time).
- [ ] Verify no recommended recovery/prevention action deploys to production without human approval.
- [ ] Verify the webshell-scan traffic is called out and escalated for a separate compromise/malware assessment.
- [ ] Confirm external re-probe returns HTTP 200 and a resolved timestamp is captured before closing the incident.

## Notes
Investigation/advisory task with no code diff to produce, so PLANNING mode with no proposed_files. Key open questions for the human operator: (1) Is the origin TLS certificate expired/invalid (root cause of the 526 storm)? (2) Was the origin service down or resource-exhausted (504s)? (3) Do the scanned webshell paths exist, indicating possible compromise requiring a full security review? Recovery has not been recorded — incident should be treated as still open.