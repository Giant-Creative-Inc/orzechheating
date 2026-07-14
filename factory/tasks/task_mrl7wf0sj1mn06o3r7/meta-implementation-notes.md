# Missing Meta Descriptions — Implementation Notes (Repair v3)

Task: task_mrl7wf0sj1mn06o3r7

## Why the previous attempt failed QA
The mu-plugin (`wp-content/mu-plugins/orzech-page-meta.php` v2.0.0) hard-deferred to any active SEO plugin via `orzech_seo_plugin_active()`. When Yoast/RankMath was active but had **no description set** for these three pages, the plugin filters returned empty AND our own `wp_head` printer bailed out entirely — so **no `<meta name="description">` rendered at all**. QA correctly failed 3 of 4 pages.

## What changed in v3.0.0 (deployable)
- Removed the blanket "skip when any SEO plugin is present" behaviour.
- Still feeds `wpseo_metadesc` and `rank_math/frontend/description` ONLY when the plugin value is empty (no duplicate when the plugin already outputs one).
- Added a **guaranteed output-buffered fallback**: at `wp_head` priority 0 we start buffering; at `PHP_INT_MAX` we flush. The buffer callback scans for an existing NON-EMPTY `<meta name="description">` and injects our escaped description only if none is present. This both fixes the missing tag and prevents duplicates.
- Title override now only fires when nothing else set a title (no hard defer).

## Affected pages
| Slug | Title | Meta description (<=160 chars) |
| --- | --- | --- |
| essential-furnace-buying-guide | Furnace Buying Guide (London, ON) \| Orzech Heating & Cooling | Choosing a new furnace in London, ON? Compare furnace types, efficiency ratings, sizing and installation costs in our essential furnace buying guide from Orzech Heating & Cooling. |
| ultimate-ac-buying-guide | Air Conditioner Buying Guide \| Orzech Heating & Cooling London, ON | Plan your new AC with confidence. Our ultimate air conditioner buying guide covers SEER ratings, sizing, energy savings and installation for London, ON homeowners. |
| lennox-ultimate-comfort-system (paid LP) | Lennox Ultimate Comfort System \| Orzech Heating & Cooling | Discover the Lennox Ultimate Comfort System for whole-home comfort and efficiency. Expert installation across London, ON from Orzech Heating & Cooling. |

## Human prerequisites / checks before production
1. Confirm the three pages return HTTP 200 on staging (published / routable). If any are unpublished drafts, no code can render a meta tag — that would block the task.
2. Confirm exact WP slugs match the map keys.
3. Confirm the fallback did not create a second meta tag on any page (view-source).
4. Confirm each page has exactly one H1 (set in page content/builder, not code).
5. Approve the paid landing page copy for /lennox-ultimate-comfort-system.

No fabricated ratings, awards, or certifications. No credentials in any file.