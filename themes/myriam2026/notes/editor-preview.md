# Editor Preview vs. Frontend — Known Gap

Deliberately deferred, not a bug to fix right now. Noted 2026-09-09 while
building the shared `.ink-ground`/`.paper-ground`/`.teal-deep-ground`
classes (see `_grounds.scss`).

## What's happening

Full-bleed custom blocks (`upcoming-events`, `featured-book`, and now
`backlist`) don't show their real ink/paper/teal-deep section background in
the block editor canvas — they render against plain light grey there,
even though the frontend is correct. This isn't a missing-stylesheet
problem: `functions.php` (`myriam2026_add_editor_styles()`) correctly wires
up `add_theme_support('editor-styles')` + `add_editor_style()`, and the
compiled theme stylesheet does load inside the editor iframe. Root cause
not diagnosed yet — candidates are the `100vw` full-bleed breakout
calculating differently inside the editor's iframe context, and/or ACF's
own block-preview wrapper markup interfering with the rendered background.
Needs a real look with dev tools open in the editor before attempting a
fix.

## Why this isn't a priority right now

Every block currently affected is heavily automated or field-driven, not
something an editor visually composes in the canvas: `upcoming-events` has
zero configuration at all, `featured-book`/`backlist` are edited entirely
through sidebar/Inspector fields (pick a book, type a blurb) rather than by
looking at and adjusting the canvas rendering. There's no real "see it,
tweak it" workflow happening on these blocks today, so an inaccurate
editor preview doesn't block actual editing work.

## Why it's still worth calling out

This is a genuinely bad general-purpose editor experience, and it will
matter more if a future block needs real WYSIWYG composition rather than
just filling in fields — at that point "the canvas shows grey instead of
the real design" stops being a shrug and becomes a real blocker. Worth
revisiting before building anything more canvas-editing-heavy on these
grounds.
