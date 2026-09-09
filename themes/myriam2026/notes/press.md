# Press — System Notes

Plan for the homepage "Press" block — a curated set of standout review
quotes spanning her whole career. Not yet built; this is the spec to build
from tomorrow. Companion to [[backlist.md]] (the CPT-driven block it sits
next to) and [[CASE-STUDY]] (the SEO reasoning behind adding this section
at all — see 2026-09-09 discussion).

---

## Where it sits on the homepage

Confirmed order: Hero → Pull-band → Featured Book → Upcoming Events →
Backlist → **Press** → Bio.

Deliberately placed after Backlist, not right under the hero's Pull-band
quote — two social-proof moments back-to-back would dilute each other.
This section exists specifically because a single pull-quote and one-line
Backlist accolades undersell how strong her press actually is (real
material reviewed 2026-09-09: dozens of blurbs across Poppy State, Creep,
and Mean alone — LA Times, NYT, Cosmopolitan, Kirkus starred, NBCC,
Lambda, etc.), and because full indexed review text has real SEO value
that a single homepage pull-quote doesn't provide on its own.

## Why this is NOT CPT-driven

Unlike [[backlist.md]], Press has no relationship to the `book` CPT at
all — confirmed explicitly 2026-09-09. It's a freestanding, editorially
curated list: a handful of the single best lines across her whole body of
work, not organized per-book. Quote and citation are typed directly into
the block, same as the `blurbs` field already built for the Featured Book
block — just not scoped to any one book.

**Content note:** the full press dump reviewed while planning this was
supplied only to demonstrate the quality/volume available, not as a
ready-to-use list. The actual 4–6 quotes that go in this block still need
to be hand-picked — not done yet.

## Data model

New block, own field group (same repeater pattern as `blurbs` on
`featured-book`):

| Field | Type | Name (slug) |
|---|---|---|
| Quotes | Repeater | `quotes` |
| — Quote | Text | `quote` |
| — Citation | Text | `citation` |

Layout: Block (not Table) — two sub-fields per row, same reasoning as
`blurbs`. Rows are freely reorderable via ACF's own repeater drag handles
— that's the "can be reordered" requirement from the 2026-09-09
conversation, and it's native repeater behavior, no extra work needed.

## Architecture — to build tomorrow

Same shape as `featured-book`: ACF block registered via
`acf_register_block_type()` in a new `inc/press.php`, template in
`blocks/press/render.php`. Much simpler than Backlist — no query logic,
just a loop over the `quotes` repeater.

## Visual reference

The original mockup (`redesign-2026/homepage-direction-mockup.html`) does
**not** have a dedicated Press section — its own homepage order is Hero →
Pull-band → Feature → Events → Backlist → Bio. This block is a deliberate
addition beyond the mockup's original spec (per the 2026-09-09 SEO
discussion), so there's no existing CSS to copy verbatim. Reasonable
starting points already in the codebase: `.blurbs`/`.blurbs blockquote`/
`.blurbs cite` in `_featured-book.scss` (italic serif quote, uppercase
tracked citation) — likely adapt that same typographic pairing rather
than inventing a new one, for visual consistency with the Featured Book
section's own blurbs.

## Open decisions

- Exact quotes/citations to feature (not selected yet).
- Layout: single column stacked list vs. a multi-column grid, given this
  section may hold more items than the Featured Book's 1–2 blurbs.
- Section heading label — "Press," "Praise," something else.
