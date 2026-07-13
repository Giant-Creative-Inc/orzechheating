# Execution plan — Fix pages with multiple H1s

Task: task_mrjlnl6iboinn0fyva · Category: technical_seo · Risk: medium

## Summary
The multiple-H1 fixes require identifying how each page's headings are rendered (page builder blocks, hero widgets, or template parts) before I can safely demote extra H1s to H2/H3 while preserving visual style. Without repository/template access or the rendered DOM markup for each URL, I cannot author correct, non-destructive template or CSS changes, and the prior QA failure was specifically about missing evidence (H1 counts, hierarchy inspection, content diffs).

## Implementation plan
1. Capture the rendered DOM of each target URL on staging and record the exact source of every H1 (theme template part, page-builder heading module, or in-page content block) — this is the missing baseline QA flagged.
2. For each extra H1, determine whether it originates from an editable page-builder heading widget (change its HTML tag from H1 to H2/H3 in the page editor) or from a template part (child-theme override only).
3. Where the H1 is a page-builder module, the fix is a content/editor setting change (tag level) that keeps identical styling — document the exact block and new tag per page.
4. Where the H1 comes from a template (e.g., a hero title outputting <h1> on non-front pages), create a child-theme template override that emits <h1> only for the true page title and <h2> for secondary titles, preserving CSS classes for identical appearance.
5. Take before screenshots (desktop + mobile) of each page, apply changes on the task branch, deploy to staging, clear caches, and take after screenshots.
6. Produce the before/after page list with H1 counts (before N → after 1) as QA evidence.

## Affected areas
- /faqs/ headings
- /get-a-quote/ headings
- /maintenance-plan/ headings
- /financing/ headings
- Possibly child theme template parts or page-builder heading modules (source TBD)

## QA checklist
- [ ] Crawl each affected URL and assert exactly one <h1> in the rendered DOM
- [ ] Compare before/after H1 counts against the recorded baseline (faqs 3→1, get-a-quote 2→1, maintenance-plan 3→1, financing 2→1)
- [ ] Verify H2/H3 hierarchy remains logical and sequential after demotion
- [ ] Desktop + mobile screenshots confirm visual heading styling is unchanged
- [ ] Diff page content to confirm no text/sections were removed — only heading tag levels changed

## Notes
BLOCKER: I cannot write correct, non-destructive deliverable files without knowing where each H1 is generated. Required human/tooling action before implementation: (1) provide the rendered HTML (or a DOM inspection) of each of the four URLs identifying each H1 element and its selector/class; (2) state which page-building system is in use (Elementor/Divi/Gutenberg/custom theme) and the child-theme path in the repo; (3) confirm the intended PRIMARY H1 per page (likely: page title for /faqs/, /get-a-quote/, /maintenance-plan/, /financing/). Most of these fixes are page-editor tag-level changes (H1→H2/H3 per heading widget) that live in the WordPress database, not repo files, and therefore need a human to apply them on staging with the mapping I document once the DOM source is provided. Empty proposed_files is intentional per the missing-information rule.