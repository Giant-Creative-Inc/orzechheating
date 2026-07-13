# Multiple H1 Remediation — Before/After Tracking

Client: Orzech Heating & Cooling (orzechheating.ca)
Task: task_mrjlnl6iboinn0fyva — Fix pages with multiple H1s
Environment: staging (production requires human approval)

## Goal
Each affected page must have exactly ONE `<h1>`. Additional headings currently
marked as H1 must be demoted to the correct semantic level (H2/H3) based on their
role in the page, WITHOUT changing visible text, removing content, or altering the
visual heading hierarchy.

## Method (per page)
1. On staging, view rendered source and count every `<h1>`.
2. Identify which single heading is the true primary topic -> keep as H1.
3. For each remaining H1, decide correct level:
   - Section title under the page topic -> H2
   - Sub-point under a section -> H3
4. Change ONLY the tag/level in the page builder or Gutenberg block. Do NOT edit text.
5. If demoting changes visual size, apply the parity CSS helper so it still looks the same.

## Page log (to be completed on staging with real captured values)

### /faqs/  (evidence: 3 H1s)
| Heading text (verbatim) | Before tag | After tag | Role |
|-------------------------|-----------|-----------|------|
| <PRIMARY PAGE TITLE>    | H1        | H1 (keep) | Primary topic |
| <2nd heading>           | H1        | H2        | Section |
| <3rd heading>           | H1        | H2        | Section |
Resulting H1 count: 1  | Content removed: none

### /get-a-quote/  (evidence: 2 H1s)
| Heading text (verbatim) | Before tag | After tag | Role |
|-------------------------|-----------|-----------|------|
| <PRIMARY PAGE TITLE>    | H1        | H1 (keep) | Primary topic |
| <2nd heading>           | H1        | H2        | Section |
Resulting H1 count: 1  | Content removed: none

### /maintenance-plan/  (evidence: 3 H1s)
| Heading text (verbatim) | Before tag | After tag | Role |
|-------------------------|-----------|-----------|------|
| <PRIMARY PAGE TITLE>    | H1        | H1 (keep) | Primary topic |
| <2nd heading>           | H1        | H2        | Section |
| <3rd heading>           | H1        | H2        | Section |
Resulting H1 count: 1  | Content removed: none

### /financing/  (evidence: 2 H1s)
| Heading text (verbatim) | Before tag | After tag | Role |
|-------------------------|-----------|-----------|------|
| <PRIMARY PAGE TITLE>    | H1        | H1 (keep) | Primary topic |
| <2nd heading>           | H1        | H2        | Section |
Resulting H1 count: 1  | Content removed: none

## Evidence to attach for QA (was missing in prior run)
- [ ] Post-edit crawl output showing exactly one H1 per page
- [ ] Before/after desktop screenshots (all 4 pages)
- [ ] Before/after mobile screenshots (all 4 pages)
- [ ] Confirmation of no PHP notices/fatals in staging logs
- [ ] Note confirming no visible heading text changed or content removed
