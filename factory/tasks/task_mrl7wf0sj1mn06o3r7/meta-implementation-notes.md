# Missing Meta Descriptions — Implementation Notes

Task: task_mrl7wf0sj1mn06o3r7

## What changed (deployable)
- `wp-content/mu-plugins/orzech-page-meta.php` is now the SINGLE source of truth for titles + meta descriptions on the three affected pages. It escapes output, matches by queried-object slug and request path, and defers to Yoast/RankMath when active (feeding descriptions only when the plugin has none).
- `mu-plugins/orzech-meta-fallback.php` (repo-root, non-deployable) neutralized to an inert stub to prevent duplicate meta tags.
- `snippets/seo/meta-descriptions.php` marked reference-only / inert.

## Affected pages
| Slug | Title | Meta description (<=160 chars) |
| --- | --- | --- |
| essential-furnace-buying-guide | Furnace Buying Guide (London, ON) \| Orzech Heating & Cooling | Choosing a new furnace in London, ON? Compare furnace types, efficiency ratings, sizing and installation costs in our essential furnace buying guide from Orzech Heating & Cooling. |
| ultimate-ac-buying-guide | Air Conditioner Buying Guide \| Orzech Heating & Cooling London, ON | Plan your new AC with confidence. Our ultimate air conditioner buying guide covers SEER ratings, sizing, energy savings and installation for London, ON homeowners. |
| lennox-ultimate-comfort-system (paid LP) | Lennox Ultimate Comfort System \| Orzech Heating & Cooling | Discover the Lennox Ultimate Comfort System for whole-home comfort and efficiency. Expert installation across London, ON from Orzech Heating & Cooling. |

## Human prerequisites before production
1. Confirm the three pages are PUBLISHED / staging-routable (return HTTP 200). The prior attempt failed QA because these were skipped drafts.
2. Confirm exact WP slugs match the map keys.
3. Confirm whether Yoast/RankMath manages meta on these pages; if so, mirror the values in the plugin fields (the mu-plugin defers automatically).
4. Confirm each page has exactly one H1 (set in page content/builder, not code).
5. Approve the paid landing page copy for /lennox-ultimate-comfort-system.

No fabricated ratings, awards, or certifications are included. No credentials in any file.
