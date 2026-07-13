# Execution plan — Fix pages with multiple H1s

Task: task_mrjlnl6iboinn0fyva · Category: technical_seo · Risk: medium

## Summary
The multiple-H1 issue lives in WordPress page/template markup on a live site (orzechheating.ca) where I do not have the repository's actual template paths, page-builder markup, or the rendered DOM that determines which elements are H1s. The previous run failed QA because no rendered-page evidence (H1 counts, heading outlines, screenshots) was captured. I cannot produce correct template/page edits without knowing where each extra H1 originates (theme header, page-builder module, or page content), so this should be blocked pending that source information.

## Implementation plan
1. Identify, per target URL, the DOM source of every H1 element by fetching the rendered staging HTML and extracting each <h1> with its surrounding template/block context (theme header logo wrap, page title module, hero heading, section headings).
2. Classify each extra H1: (a) sitewide theme/header source, (b) page-builder/module default heading level, (c) hardcoded page content.
3. For theme/header-sourced extra H1s, patch the child theme template part only (never parent theme/core); demote non-primary headings to the appropriate H2/H3 while preserving visual size via existing CSS classes.
4. For page-builder/content-sourced extra H1s, adjust the heading-tag setting on the specific module or edit the page content to demote to H2/H3, keeping the single primary H1 that names the page topic.
5. Preserve visual hierarchy: any tag change must retain the original font-size/weight via CSS class, not by leaving the H1 tag.
6. Deploy the task branch to staging via gridpane.deploy_staging, clear all caches, and confirm 200 + no PHP notices in logs.
7. Capture rendered evidence for QA: per-URL H1 count (must equal 1), full heading outline (H1→H2→H3), and before/after desktop + mobile screenshots.
8. Produce the before/after page list deliverable from the captured evidence.

## Affected areas
- WordPress child theme template parts (header/title area) — exact paths unknown
- Page-builder module heading settings for /faqs/, /get-a-quote/, /maintenance-plan/, /financing/
- Page content for the four target pages
- child theme CSS (to retain visual sizing when demoting tags)

## QA checklist
- [ ] Crawl each of the four target pages on staging and assert exactly one <h1> per page
- [ ] Provide the full heading outline (H1/H2/H3 order) per page and confirm it remains logical
- [ ] Capture desktop + mobile screenshots before and after for visual parity
- [ ] Diff page text content before/after to confirm no content was removed
- [ ] Confirm staging returns HTTP 200 and no new PHP notices/fatals in logs after deploy

## Notes
Blocked pending human/source input. To proceed I need: (1) the repository path(s) for the active child theme and the template part(s) that render page headers/titles; (2) confirmation of which page builder (if any) renders these pages, since builder-stored H1s live in the DB and cannot be changed via PR files — those need staging in-builder edits; (3) the rendered staging HTML for each of the four URLs so I can pinpoint each H1's origin. Producing template/content files without this would repeat the prior failure (unverifiable, possibly wrong file). Once the H1 sources are identified I will return complete, targeted file edits plus the H1-count/screenshot evidence QA requires.