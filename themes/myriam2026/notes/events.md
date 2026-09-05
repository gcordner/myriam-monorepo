# Events — System Notes

Reference for the custom events system: the CPT, its fields, the homepage
"Upcoming" block plan, and what's deliberately deferred. Companion to
[[layout.md]] (heading-hierarchy rules that apply here) and
`redesign-2026/CLAUDE.md` (where the original "rebuild as a CPT, hide when
empty" decision was made).

---

## Why a custom CPT, not The Events Calendar

The Events Calendar plugin is installed and active, and does have real
historical data in it (28 published events, 2020–2024 — a book-tour
archive, all past). It's not being used going forward — it was judged
too heavy for what's actually needed here. A simpler, hand-rolled `event`
CPT already existed in `functions.php` (now extracted to `inc/events.php`)
and is the one being built out.

## Why plain meta, not ACF

ACF Pro is installed and used elsewhere in the theme (e.g. `book`), but
deliberately *not* used for events — the field set is a handful of flat
scalars, there's no admin-facing "let non-devs add fields" requirement,
and the planned block is hand-built (custom `render.php`) rather than using
ACF's block-bindings feature, so the usual ACF advantages don't apply
here. Plain `get_post_meta()` avoids a dependency for no real benefit.

---

## Data model

CPT slug: `event`. Registered in `inc/events.php`.

| Field | Storage | Notes |
|---|---|---|
| Title | native (`post_title`) | |
| Subtitle | `_event_subtitle` (meta) | plain text |
| Description | native (`post_content`) | WYSIWYG — CPT supports `editor`; real Gutenberg block markup, not plain text. Render with `apply_filters('the_content', ...)`, not a raw echo. |
| Featured image | native | CPT supports `thumbnail` |
| Date | `_event_date` (meta) | `YYYY-MM-DD` string |
| Time | `_event_time` (meta) | `HH:MM` string |
| Location | `_event_location` (meta) | free text |
| URL | `_event_url` (meta) | link to the event/venue/ticket page — falls back to the event's own permalink if empty |

Helper: `get_event_details( $post_id )` in `inc/events.php` returns
subtitle/date/time/location/url as an array. Deliberately doesn't include
description or image — those already have standard WP accessors
(`get_the_content()`, `get_the_post_thumbnail()`), no need to duplicate.

**Validated 2026-09-02** against a real entry: post 4287, "Story Garden"
(2026-09-19, Campo De Cahuenga, Eventbrite URL, two-paragraph WYSIWYG
description, featured image). All fields round-tripped correctly through
the meta box.

---

## Homepage "Upcoming" block — plan (not yet built)

**Visibility rule:** an event stays visible through the day *after* its
date, and drops starting the day after that — i.e. query condition is
`_event_date >= (today − 1 day)`, date-only comparison, using
`wp_timezone()` (site-configured timezone, not server UTC):

```php
$tz     = wp_timezone();
$cutoff = ( new DateTime( 'now', $tz ) )->modify( '-1 day' )->format( 'Y-m-d' );

new WP_Query([
    'post_type'      => 'event',
    'post_status'    => 'publish',
    'posts_per_page' => 3,
    'no_found_rows'  => true,
    'orderby'        => 'meta_value',
    'meta_key'       => '_event_date',
    'order'          => 'ASC',
    'meta_query'     => [[
        'key' => '_event_date', 'value' => $cutoff,
        'compare' => '>=', 'type' => 'DATE',
    ]],
]);
```

Events with no `_event_date` are naturally excluded (no special-casing
needed). Sorted soonest-first. **Capped at 3** — confirmed 2026-09-02: if
a 4th upcoming event exists, the block just shows the soonest 3 and drops
the rest silently. No "see all" link/behavior for now.

**Layout selection** — purely `count($query->posts)`, re-evaluated on
every page load (dynamic block, nothing baked in at save time — this is
what makes the layout switch automatically as events pass):

| count | layout |
|---|---|
| 0 | section omitted entirely |
| 1 | single "flyer" layout — image/text side-by-side, description, button |
| 2 | two stacked flyer blocks, second mirrored (text/image swapped) on desktop, both collapse to image-on-top on mobile |
| 3 | 3-up card grid — image, date/location, title, subtitle; no description/button; whole card links out |

Visual reference for all three: `redesign-2026/homepage-direction-mockup.html`
("Upcoming" section, `teal-deep-ground`).

**Architecture:** a server-rendered block, no JS/build step — `block.json`
(apiVersion 3) with `"render": "file:./render.php"`, so the same PHP
renders both the editor preview and the frontend. Zero editable
attributes; you place it once and it's fully automatic from then on.

- `blocks/upcoming-events/block.json` + `render.php` — markup only
- `inc/events.php` — gains `myriam2026_get_upcoming_events()` (the query
  above), colocated with `get_event_details()`
- `"supports": { "multiple": false }`

**CSS collision to watch for:** the *live* `css/src/base/_events.scss`
already defines `.event-card`/`.events-grid` for the `/events/` archive
page (different purpose, see below). The homepage block's CSS must be
scoped under its own wrapper (e.g. everything nested under
`.upcoming-events { }`) in a new partial, not just ported into
`_events.scss` verbatim, or the two systems' class names will collide.

Heading hierarchy: section title "Upcoming" → real `<h2>`; each event's
title → `<h3>` — per [[layout.md]].

---

## Known issues — to fix

- **Doesn't fit in the block editor canvas.** The single-event layout's
  full-bleed CSS (`.upcoming-events` breaks out to `100vw` via negative
  margins — see "Homepage 'Upcoming' block" above) works correctly on the
  front end, but in wp-admin the editor canvas is narrower than the real
  viewport (more so with the block-library panel open on the left and/or
  the page/block settings panel open on the right), so the `100vw`
  breakout overshoots the canvas and the block visibly exceeds its
  container in the editor. Confirmed 2026-09-03: front end is correct,
  editor is not. Low priority for now — this is partly WordPress's own
  editor UI/UX making the canvas width unreliable to breakout against —
  but needs a real fix (e.g. detecting the editor context and using a
  different breakout strategy, or a CSS approach that isn't naive `100vw`)
  before this is done.

---

## Deliberately deferred — not designed yet

- **`/events/` archive page.** Will get its own, different layout and its
  own template/template part — not the homepage block's design. The
  existing `archive-event.php` + `template-parts/content-archive-event.php`
  + the live `.events-grid`/`.event-card` CSS are the *old* implementation
  and will likely be reworked when this is tackled. Not in scope yet.
- **Book-tour scenario.** If/when there's a tour (many events clustered in
  time, like the old TEC data), the 1/2/3-count homepage block isn't the
  right presentation — plan is a separate "tour block" with its own
  layout, to be designed when it's actually needed. Until then, the
  cap-at-3 rule above is the whole story.
