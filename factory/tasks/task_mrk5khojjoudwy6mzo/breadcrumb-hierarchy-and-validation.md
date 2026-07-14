# BreadcrumbList — URL-to-Hierarchy Mapping & Validation Notes

Task: task_mrk5khojjoudwy6mzo — Add BreadcrumbList schema
Source of truth: `wp-content/themes/salient-child/includes/schema.php` (single wp_head hook)

## What this task changes
- Adds ONLY BreadcrumbList JSON-LD, emitted site-wide on singular pages/posts.
- Does NOT add or modify AggregateRating/Review data (separate task; reviews do not render on these pages).
- Also removes three broken/unverifiable links from the pre-existing HVACBusiness block in the same file, which failed the deterministic link checker:
  - BBB profile URL (returned 403) — removed from `sameAs` and `memberOf`.
  - `http://www.bbb.org/` (returned 403) — removed.
  - `https://www.tssa.org/en/index.as` (returned 404) — removed.
  Organization names are retained without URLs so no claim is fabricated and no broken link remains.

## How the trail is built
1. Trail always starts with `Home` → `https://orzechheating.ca/`.
2. Each URL path segment becomes a ListItem, in order, with a cumulative trailing-slash URL.
3. Names come from a curated slug→label map (title-cased fallback for unmapped slugs).
4. Homepage and single-segment-only pages are skipped where the trail would be a lone Home node.
5. `@id` = page permalink + `#breadcrumb` (stable per page).

## URL → expected breadcrumb hierarchy
| URL | Emits breadcrumb? | Expected ListItems (position: name → item) |
| --- | --- | --- |
| https://orzechheating.ca/ | No (front page) | — |
| https://orzechheating.ca/heating/ | Yes | 1: Home → /  ·  2: Heating → /heating/ |
| https://orzechheating.ca/cooling/ | Yes | 1: Home → /  ·  2: Cooling → /cooling/ |
| https://orzechheating.ca/heating/furnaces/furnace-repair/ (representative nested) | Yes | 1: Home → /  ·  2: Heating → /heating/  ·  3: Furnaces → /heating/furnaces/  ·  4: Furnace Repair → /heating/furnaces/furnace-repair/ |
| https://orzechheating.ca/plumbing/drain-cleaning/ (representative nested) | Yes | 1: Home → /  ·  2: Plumbing → /plumbing/  ·  3: Drain Cleaning → /plumbing/drain-cleaning/ |

## Validation steps (per changed URL)
1. Open view-source and confirm exactly one BreadcrumbList `<script type="application/ld+json">` block where eligible.
2. Paste the block into the schema.org validator — expect valid types, no errors.
3. Run Google Rich Results Test — expect Breadcrumbs eligible.
4. Confirm each ListItem `name` and `item` matches the visible on-page breadcrumb.
5. Confirm `@id` ends with `#breadcrumb` and URLs are absolute + trailing-slash.
6. Re-run the deterministic link checker; confirm BBB/TSSA broken links are gone and no new broken links appear.
7. Confirm no AggregateRating/Review or other schema types changed.
8. Re-validate after any production deploy.

## Notes for reviewer
- If a page's visible breadcrumb label differs from the map value, update `orzech_breadcrumb_label_map()` to match the visible label before production approval.
- The homepage intentionally emits no BreadcrumbList (Google does not show single-node breadcrumbs).
