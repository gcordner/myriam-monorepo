# Typography Sources — Full Inventory

**Date:** 2026-08-24
**Theme:** Myriam2026 (GeneratePress child theme)
**Scope:** `/wp-content/themes/myriam2026` + the `myriamgurba` database (docksal env)

Analysis only — nothing here was changed. Companion to `COLOR-SOURCES.md`,
same method: trace every place a font/size decision actually lives before
the redesign touches typography.

---

## Executive summary

Where the color system was split across four independently-editable
sources, typography is simpler but more fragile: theme.json's type system
is almost entirely decorative. Only one line in the whole compiled SCSS
actually reads from it live; everything else — every heading, every
component — hardcodes its own literal font-family string and font-size
value by hand. There's also one already-latent break (a font-family name
mismatch) sitting unused in source, and one dead font family shipped but
never wired up.

---

## 1. `theme.json` — the declared type system

Three font families (`settings.typography.fontFamilies`):

| slug | fontFamily string |
|---|---|
| montserrat | `"Montserrat", sans-serif` |
| bebas-neue | `"Bebas Neue", cursive` |
| big-shoulders | `"Big Shoulders", serif` |

Six font sizes (`customFontSize: false`, `defaultFontSizes: false` — same
lock-down pattern as `defaultPalette: false` for color): small (0.875rem),
my-medium (1rem), large (1.25rem), extra-large (`clamp(1.5rem, 4vw, 2rem)`),
triple-xl (3rem), Huge (4rem — capitalized differently than the other five
slugs, cosmetic only).

`styles.elements` sets `h1`–`h5` to `var(--wp--preset--font-family--bebas-neue)`.
Base `styles.typography.fontFamily` is Montserrat. No font-size styles are
set at the theme.json level.

---

## 2. The compilation gap — theme.json's fonts never reach SCSS

`webpack.config.js` (see COLOR-SOURCES.md §"How theme.json compiles into
SCSS") only pulls `settings.color.palette` and `settings.layout` into
`_theme-values.scss`. **`fontFamilies` and `fontSizes` are never touched by
that generation step.** There's no typography equivalent of
`_theme-values.scss` — whatever the SCSS does with fonts, it does entirely
by hand, disconnected from theme.json from the start.

---

## 3. What the SCSS actually does — one live reference out of ~20

Swept every `font-family` declaration across all partials. Exactly one
reads from theme.json's live custom property:

```scss
// _header.scss:41 — the site title/logo only
.main-title {
    font-family: var(--wp--preset--font-family--bebas-neue);
```

Every other occurrence hardcodes a literal string instead, repeated by hand:

- `_typography.scss` — `font-family: 'Montserrat', sans-serif;` (body),
  `font-family: 'Bebas Neue', cursive;` (global `h1,h2,h3,h4,.wp-block-heading`
  rule)
- `_student-portal.scss` — 18 separate hardcoded occurrences
  (`'Bebas Neue', cursive` ×7, `'Montserrat', sans-serif` ×11)
- `_layouts.scss` — `"Big Shoulders Display", Arial, Helvetica, sans-serif;`
  (the `.magazine` byline label) and `"Montserrat", Arial, Helvetica, sans-serif;`
  (the date)

Compare to color, where roughly half the SCSS read live `var(--wp--preset--color--*)`
values (header, footer). For typography it's one line out of twenty — the
site title is the sole exception. Changing the display or body typeface
later means find-and-replace across the SCSS source plus a rebuild, not a
theme.json edit.

Font-size follows the same pattern: no component references theme.json's
six named sizes at all. Header, book, student-portal, layouts, and
typography partials each hardcode their own bespoke rem/clamp values —
25+ distinct font-size literals counted across the files, no shared scale
in code.

---

## 4. Finding: `"Big Shoulders"` vs `"Big Shoulders Display"` — a name mismatch

**Severity:** Medium (latent, not yet triggered)

The only `@font-face` actually registered (`_fonts.scss:26`) declares the
family name **`'Big Shoulders Display'`**. Theme.json's preset is named
plain **`"Big Shoulders"`** (no "Display"), falling back to `serif`.

Anything using `var(--wp--preset--font-family--big-shoulders)` — i.e.
picking "Big Shoulders" from the block editor's font-family dropdown — asks
the browser for a family that was never declared via `@font-face`, and
silently falls through to generic `serif`.

Checked whether this has actually happened: **zero** published posts use
`has-big-shoulders-font-family`. Dormant, not active — the direct
typography counterpart to the broken `--theme-palette-color-3` finding in
the color report.

Where the *correctly*-named `"Big Shoulders Display"` string is used
(`_layouts.scss`'s magazine byline label), it works — but only because it's
a hardcoded literal outside the theme.json system entirely, not because
anything was fixed.

---

## 5. The `cursive` fallback, for the record

Both theme.json (`"Bebas Neue", cursive`) and `_typography.scss`
(`'Bebas Neue', cursive`) hardcode `cursive` as Bebas Neue's fallback, in
the actual source — confirmed, not a snapshot artifact. Bebas Neue is
self-hosted and loads correctly in normal operation, so this fallback never
actually engages on the live site. Noted only because it's genuinely
present in source, in two places, in case it's worth tightening to
`sans-serif` while other typography work is happening.

---

## 6. Finding: Albert Sans is shipped but completely unused

**Severity:** Low (housekeeping)

Full variable + static weight font files exist at
`css/src/fonts/Albert_Sans/`, but there's no `@font-face` for it anywhere
in `_fonts.scss`, and no reference to it anywhere in the compiled SCSS.
Dead weight on disk, not part of the live type system.

This also settles the question raised during the homepage mockup work: the
theme does **not** already have a committed "utility sans" beyond the
system-UI stack used there — Albert Sans looked like a candidate given its
presence in the font folder, but it isn't wired up anywhere.

---

## 7. GeneratePress's own typography settings — checked, effectively empty

Unlike color (where GP's `generate_settings.global_colors` was fully
populated and actively driving nav colors), `generate_settings.typography`
is `[]` — GP's own Customizer typography module holds nothing.

**One real conflict found anyway:** `generate_settings.site_title_font_size`
is `25` (px) — a GP Customizer field independently claiming the site
title's size. `_header.scss`'s `.main-title` hardcodes `font-size: 3rem`
(48px) for the same element. The SCSS wins on the live page by cascade
specificity, but the database still holds a stale, contradicting value that
nobody reconciled — the typography version of the color report's `base-3`
orphan-white finding.

No Stackable typography option exists — its global-styles system only
covers color (see COLOR-SOURCES.md §3). WooCommerce has its own
`woocommerce_email_font_family`, scoped to transactional emails only, same
caveat as the email colors — out of scope unless emails are part of the
redesign.

---

## 8. Real-world usage check (published content)

| theme.json font-size slug | uses in published content |
|---|---|
| small | 0 |
| my-medium | 0 |
| large | 3 |
| extra-large | 1 |
| triple-xl | 2 |
| Huge | 0 |

| theme.json font-family slug | uses in published content |
|---|---|
| montserrat | 1 |
| bebas-neue | 2 |
| big-shoulders | 0 |

---

## Bottom line for the eventual type change

Swapping Bebas Neue for a different display face in theme.json would only
auto-update the site title in the header — every heading, student-portal
element, and layout label would need the SCSS edited by hand and rebuilt.
The font-size scale in theme.json is decorative relative to the actual
component CSS, which ignores it entirely. There's one already-latent break
(`Big Shoulders` vs `Big Shoulders Display`) sitting unused, one stale
GP Customizer value contradicting the real rendered site-title size, and
one fully unused font family (Albert Sans) shipped for no active reason.

---

## Redesign type system implemented (2026-08-30)

Three fonts wired up and applied, replacing the old system:

- **Bebas Neue** — display, unchanged (site title only, per role).
- **Archivo** — new utility sans (nav, captions, labels, buttons, form UI,
  footer). Self-hosted: `theme.json`, `_fonts.scss` `@font-face`,
  `css/src/fonts/Archivo/` (variable font only — the `static/` weight
  folder Google Fonts ships was deleted as unused dead weight; `OFL.txt`
  kept for license reference, `README.txt` is just descriptive metadata,
  not a license, kept for now).
- **Fraunces** — new body/serif default (`body` in `_typography.scss`,
  theme.json's root `styles.typography.fontFamily`). Same self-hosting
  pattern as Archivo, `css/src/fonts/Fraunces/`.
- **Montserrat** — fully retired (theme.json entry, `@font-face`, and
  build output all removed) after confirming zero remaining references,
  including one real published-content use on the homepage (a review
  blurb pinned to Montserrat via the block editor's font picker) that
  had to be manually re-picked to a different font first, since it was
  invisible as a mismatch before the base font changed away from
  Montserrat itself.

**Mapping note:** Montserrat wasn't a single role — splitting its old
usages required judgment, not a mechanical rename. Only the base `body`
rule became Fraunces; everything else that was Montserrat (writing-archive
byline date, and nine separate student-portal uses — badges, nav-style
buttons, form labels/fields, a login error message, small captions)
became Archivo instead, since those are all short UI chrome/labels, not
reading prose — matching the original plan's split between "serif body/
subtitles/quiet text" and "utility sans nav/labels/captions/footer."

## Block editor doesn't show custom fonts — partially addressed, not confirmed fixed

The block editor was rendering everything in system-ui instead of the
new fonts, unrelated to whether the fonts work on the front end (which
they do, confirmed). Root cause: `functions.php`'s
`enqueue_block_editor_assets` hook did a raw `wp_enqueue_style()` of the
compiled CSS into the editor, bypassing WordPress's real editor-styles
API. `add_editor_style()` (the supported mechanism) automatically
rewrites a `body` selector to `.editor-styles-wrapper` when injecting
into the editor's iframe — a plain enqueue doesn't get that rewrite, so
`body { font-family: Fraunces }` wasn't matching anything with enough
specificity inside the iframe.

**Fix applied:** switched to `add_theme_support('editor-styles')` +
`add_editor_style()`, resolving the current hashed build filename via
the same `glob()` pattern already used elsewhere in `functions.php`
(new function `myriam2026_add_editor_styles()`, hooked to
`after_setup_theme`). Confirmed server-side via `wp eval` that
`current_theme_supports('editor-styles')` is true and the correct
compiled CSS file (containing the real `body{font-family:Fraunces,serif}`
rule) is registered in `$editor_styles`.

**Not yet confirmed working.** After the fix, fonts still weren't
showing in the editor; a log-out/log-in was tried as a next step but
the result wasn't confirmed before moving on. This has no effect on the
front end either way (purely an editor-authoring-experience issue, not
a performance or visitor-facing concern) — deprioritized in favor of
front-end typography work, but circle back and verify with browser
DevTools (inspect the editor iframe's `<head>` for the injected
stylesheet, and its Network tab for whether the font files actually
load) if it's still broken next time this is picked up.
