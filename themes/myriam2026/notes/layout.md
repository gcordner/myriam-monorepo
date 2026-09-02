# Layout & Page-Structure Notes

Decisions about document structure — heading hierarchy, landmark/section
semantics, grid/layout choices that aren't purely visual. Companion to
`typography.md` (which tracks font/size *sources*) and `COLOR-SOURCES.md`;
this one tracks structural/semantic decisions instead.

---

## Heading hierarchy — homepage

**Date:** 2026-09-02

GeneratePress puts an `<h1>` on the site wordmark specifically on the front
page — `generatepress/inc/structure/header.php:199`:
`( is_front_page() && is_home() ) ? 'h1' : 'p'`. Everywhere else the
wordmark renders as a plain `<p>`. So on the homepage, "MYRIAM GURBA" is
the page's one and only `h1` — every content section below it is a
sibling, and its title should be an `h2`.

**Rule going forward:** each homepage section's own title is an `h2`.
Individual items inside a section (a book, an event) are `h3`. A
*decorative* kicker/eyebrow label above a section's real title (e.g. "New
book" sitting above the `h2` "Poppy State") is correctly **not** a
heading — it's not the section's title, the `h2` right below it is.

Applied in `redesign-2026/homepage-direction-mockup.html`:
- `Upcoming`, `Backlist`, `Bio` — were `<div class="section-label">`
  (no heading semantics at all), now `<h2 class="section-label">`. Pure
  tag swap, no visual change — the styling is class-based, not tag-based.
- `Poppy State` (Feature/"New book" section) was already a real `<h2>` —
  no change needed there.
- Individual event titles (`event-solo`/`event-card` `h3`) and backlist
  book titles (`book-card` `h3`) were already correctly one level below
  their section's heading — no change needed.

**Why this matters beyond the mockup:** every future homepage section
needs the same treatment — a real `<h2>` for the section's own title, not
a styled `div` — or the page's heading outline breaks (screen readers and
SEO crawlers both rely on it to navigate/parse the page, and a skipped or
missing level reads as a malformed document even though it looks fine
visually). Keep this in mind when building the real block/template output,
not just the static mockup.
