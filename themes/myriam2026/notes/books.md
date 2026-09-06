# Single Book Template — Rebuild Proposal

**Date:** 2026-09-06
**File in question:** `single-book.php` (+ `css/src/base/_book.scss`)

Proposed while working through header/title-spacing consistency across
templates (see [[layout.md]]) — Books was one of the templates flagged
there as needing restructuring rather than just a CSS patch, since it's
fully custom, owned code that already needed a cleanup pass.

**Status:** Phase 1 (below) is done. Phase 2 (container unification) is
the current work — see that section further down.

---

## Confirmed problems

1. **Scaffold placeholders** — already flagged in [[THEME-AUDIT-REPORT]]
   as finding N1: the text domain is `'your-textdomain'` throughout (8
   occurrences) instead of `'myriam2026'`, and the file header comment
   still reads `File: /wp-content/themes/your-gp-child/single-book.php`.

2. **Bypasses GeneratePress's entry-header system entirely.** Hand-rolled
   `<header class="book-header"><h1 class="book-title">` instead of
   `generate_do_attr('entry-header')` + `the_title()`. That's why it
   needed its own hardcoded `padding-top: 5rem` in `_book.scss` instead
   of picking up the shared `clear-fixed-header()` mixin (see
   [[layout.md]]) that every other singular template now uses.

3. **Dead code** — a commented-out reference to a `book_category`
   taxonomy. Confirmed via `wp taxonomy list`: no such taxonomy exists
   anywhere on this install, never did.

4. **A real content gap, not just cleanup.** The `book` ACF field group
   has a "Button 2" field (`button_2` → `button_2_text`/`button_2_url`)
   with genuinely populated secondary purchase links for 6 of the 7
   books (Barnes & Noble, secondary Amazon links, etc. — confirmed via
   direct DB query). Fully filled out in the CMS, completely invisible
   on the site, because the template only ever wires up "Button 1".

5. **Forward-looking namespace collision.** `.book-grid` (this page's
   2-column content layout: main content + meta aside) will collide
   with the approved homepage redesign's Backlist section
   (`redesign-2026/homepage-direction-mockup.html`), which uses the
   identical class name for something unrelated — a grid of book-cover
   cards. Not live yet, but worth renaming now while this file is
   already open, rather than hitting the collision later when the
   Backlist section gets built for real.

### Checked and ruled out

"Button 1" is accessed as `get_field('button_1_button_1_text')`, which
looks like a mismatched/double-prefixed field name at a glance (as if
someone incorrectly assumed ACF auto-prefixes Group-field sub-fields).
Verified against the actual ACF field config and live database — the
sub-field's real configured name genuinely is `button_1_button_1_text`,
the code matches it correctly, and it renders fine live (confirmed
"Purchase" button linking correctly on the Mean book page). Not a bug,
just an unusually redundant field name someone chose when building it.

---

## Phase 1 — done (2026-09-06)

- **A.** Switched to GP's real entry-header markup — dropped the custom
  `.book-header`/`.book-title` CSS and the hardcoded `5rem` padding;
  picks up the shared `clear-fixed-header()` mixin automatically.
  Confirmed live: `.entry-header` margin-top computes to `85px`, same as
  Media/Writing.
- **B.** Subheading changed from `<h2>` to `<p class="book-subheading">`
  — it's a tagline, not a second content heading (matches how events'
  subtitle is already handled).
- **C.** Renamed `.book-grid`/`.book-main`/`.book-aside` →
  `.book-detail-grid`/`.book-detail-main`/`.book-detail-aside`, resolving
  the future collision with the Backlist section now, ahead of it being
  built.
- **D.** Fixed the textdomain and file-header placeholders (`myriam2026`
  throughout).
- **E.** Deleted the dead taxonomy comment.
- **F. Button 2, wired up** (resolved the open question below): added as
  a second CTA using the same pattern as Button 1
  (`get_field('button_2_button_2_text')` /
  `get_field('button_2_button_2_url')`), styled `button-secondary` (a
  GP core utility class, no new CSS needed) to visually distinguish it
  from Button 1's `button-primary`. Verified live on Creep, Poppy State,
  and Letters to a Writer of Color (the three books with real Button 2
  data) — both links render and point correctly. Books without Button 2
  data show just the one button, unchanged.

Commit message for this phase covered `single-book.php` +
`css/src/base/_book.scss` + this file.

---

## Phase 2 — container/wrapper unification (in progress)

**Date raised:** 2026-09-06, after Phase 1 shipped.

Phase 1 only unified the *title*. Direct comparison against
`generatepress/page.php` + `content-page.php` showed `single-book.php`
skips GP's entire outer skeleton — no `#content`/`.site-main` wrapper,
no `.inside-article`, no `.entry-content`, none of the
`generate_before_main_content` / `generate_after_main_content` /
`generate_before_content` / `generate_after_content` hook points, no
`generate_do_microdata('article')`, and `generate_construct_sidebars()`
is never called (meaning sidebars are structurally impossible on book
pages regardless of any layout setting — confirmed not a concern here).

**The actual goal, stated directly:** every page should share one
identical outer shell — same top clearance, same bottom spacing, same
everything about the frame — and only the content in the middle
changes per template. ("Chest of drawers" model: a narrow top drawer, a
bottom drawer, and one large cabinet space in between where content
goes — the cabinet is the *only* thing that changes.) This is a
deliberate, general principle for the theme going forward, not a
Books-specific fix.

### The one real risk, checked and resolved

Wiring up `generate_before_content` would also newly fire GP core's own
`generate_featured_page_header_inside_single()`, which renders a
full-width `the_post_thumbnail('full')` banner whenever
`has_post_thumbnail()` is true. Books *do* have a featured image set —
it's the cover art already shown deliberately in the aside — so left
alone, this would render a second, duplicate copy of the cover above
the title.

`functions.php` already has a `remove_featured_image_from_pages()`
function that disables this exact GP hook, but only `if ( is_page() )`
— i.e. only for regular WordPress Pages, not custom post types.

**Resolution:** broaden that function to run unconditionally, for every
post type, not just pages. This isn't a Books-specific workaround — it
matches the shell/content principle directly: no template should get an
automatic image inserted by the shell itself; any thumbnail display
(book covers, event flyers, Writing archive thumbnails) is the
content's own deliberate choice, made in that template's own code, the
same way it already works everywhere on this site today.

### Plan

1. **`functions.php`** — remove the `is_page()` condition from
   `remove_featured_image_from_pages()` so all three removals
   (`generate_before_content`/`generate_featured_page_header_inside_single`,
   `generate_after_header`/`generate_featured_page_header`,
   `generate_show_featured_image` filter) apply site-wide.
2. **`single-book.php`** — wrap the existing content in GP's real
   skeleton: `generate_do_attr('content')` / `generate_do_attr('main')`
   divs, `generate_before_main_content`/`generate_after_main_content`,
   `.inside-article`, `generate_before_content`/`generate_after_content`,
   `generate_do_microdata('article')`, and `generate_construct_sidebars()`
   at the end. The entry-header from Phase 1 is unchanged.
   `.book-detail-grid` (main + aside, unchanged) becomes the contents of
   `.entry-content` — same shell, only the middle changes.
3. **Verify live** — confirm no duplicate cover image appears anywhere
   on the site (not just Books — this check now applies globally since
   the fix is global), and check whether GP's "Separate Containers"
   `.site-main > *` margin (`20px`) or any other newly-inherited default
   shifts spacing anywhere unexpectedly.
