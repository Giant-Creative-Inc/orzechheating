# Multiple-H1 Remediation Plan — Orzech Heating & Cooling

## Goal
Each affected page must render exactly one H1 (the page's primary topic). All other headings currently emitted as H1 are demoted to H2/H3 while keeping identical text and visual appearance.

## Intended primary H1 per page
| URL | Keep as H1 (primary topic) |
| --- | --- |
| /faqs/ | Frequently Asked Questions |
| /get-a-quote/ | Get a Quote |
| /maintenance-plan/ | Maintenance Plan |
| /financing/ | Financing |

## Before / After H1 count
| URL | Before | After (target) |
| --- | --- | --- |
| /faqs/ | 3 H1 | 1 H1 |
| /get-a-quote/ | 2 H1 | 1 H1 |
| /maintenance-plan/ | 3 H1 | 1 H1 |
| /financing/ | 2 H1 | 1 H1 |

## Remediation rules
1. Do NOT delete any heading — only change its level (H1 -> H2, or H1 -> H3 for nested sub-sections).
2. Keep the heading text verbatim.
3. Preserve visual size/weight. If the theme sizes H2 smaller than the old H1, add the `h1-visual` class (see `assets/css/heading-hierarchy-fix.css`) so appearance is unchanged.
4. Fix the H1 at its true source: page-builder heading module heading-level setting, template part, or in-content markup — not via CSS-only hacks that leave duplicate H1s in the DOM.
5. After edits, the demoted headings should follow logical order under the single H1.

## Source identification (to complete on staging)
For each URL, record where each current H1 originates:
- Page title / theme template header
- Page-builder heading module (record module + heading level setting)
- Hardcoded content markup in the WYSIWYG/block editor

Then apply the level change at that source only, being careful not to affect shared templates used by other pages.

## QA evidence to attach
- Crawl output showing 1 H1 per page (before/after)
- Desktop + mobile screenshots before/after for each page
- Heading outline (H1/H2/H3) confirming logical nesting