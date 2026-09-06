# Single Book Template — Rebuild Proposal

**Date:** 2026-09-06
**File in question:** `single-book.php` (+ `css/src/base/_book.scss`)

Proposed while working through header/title-spacing consistency across
templates (see [[layout.md]]) — Books was one of the templates flagged
there as needing restructuring rather than just a CSS patch, since it's
fully custom, owned code that already needed a cleanup pass. Not yet
implemented; this is the plan, pending sign-off.

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

## Proposed changes

- **A.** Switch to GP's real entry-header markup — drops the custom
  `.book-header`/`.book-title` CSS and the hardcoded `5rem` padding;
  picks up the shared `clear-fixed-header()` mixin automatically.
- **B.** Change the subheading from `<h2>` to `<p class="book-subheading">`
  — it's a tagline, not a second content heading (matches how events'
  subtitle is already handled).
- **C.** Rename `.book-grid`/`.book-main`/`.book-aside` →
  `.book-detail-grid`/`.book-detail-main`/`.book-detail-aside`, resolving
  the future collision with the Backlist section now.
- **D.** Fix the textdomain and file-header placeholders.
- **E.** Delete the dead taxonomy comment.

## Open question — needs a product/content decision, not a technical one

Wire up Button 2 as a real second CTA (the data's already there, same
pattern as Button 1 once fixed), leave it configured-but-unused, or
remove the unused field entirely? Not decided yet.
