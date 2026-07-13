# Meta Description Repair — Orzech Heating & Cooling

Task: task_mrjt9sllf7zxc1zfww — Missing meta descriptions on key commercial pages.

## Why the last QA failed
The three affected pages existed only as **skipped drafts** with no rendered title/meta/H1 evidence, so QA could not verify the acceptance criterion. Repository files alone cannot make an unpublished draft web-addressable.

## Required human action before staging QA can pass
1. Publish (or make staging-routable) the three pages so they return HTTP 200 with rendered HTML:
   - `/essential-furnace-buying-guide`
   - `/ultimate-ac-buying-guide`
   - `/lennox-ultimate-comfort-system`
2. Preferred: enter the drafted **title** and **meta description** below into the site SEO plugin (Yoast/RankMath) fields for each page, and confirm each page has exactly one H1.
3. Alternative/temporary: the included `mu-plugins/orzech-meta-fallback.php` emits an escaped meta description on these paths so staging renders a verifiable tag even before plugin fields are set. Remove it once the SEO plugin fields are populated to avoid duplicate meta tags.

## Drafted copy
| Page | Title | Meta description |
| --- | --- | --- |
| Essential Furnace Buying Guide | Essential Furnace Buying Guide \| Orzech Heating & Cooling London ON | Compare furnace types, efficiency ratings and sizing before you buy. Orzech Heating & Cooling's furnace buying guide for London, ON homeowners. |
| Ultimate AC Buying Guide | Ultimate AC Buying Guide \| Orzech Heating & Cooling London ON | Choosing a new air conditioner? Learn about SEER ratings, sizing and costs in Orzech Heating & Cooling's AC buying guide for London, ON homes. |
| Lennox Ultimate Comfort System | Lennox Ultimate Comfort System \| Orzech Heating & Cooling London ON | Discover the Lennox Ultimate Comfort System for quiet, efficient home comfort. Installed and serviced by Orzech Heating & Cooling in London, ON. |

All meta descriptions are within ~160 characters. No fabricated claims, ratings, or certifications are included.
