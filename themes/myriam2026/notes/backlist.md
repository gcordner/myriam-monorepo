# Backlist — System Notes

Plan for the homepage "Backlist" block — a grid of Myriam's earlier books,
each with a one-line accolade underneath. Not yet built; this is the spec
to build from tomorrow. Companion to [[books.md]] (the `book` CPT itself)
and `redesign-2026/homepage-direction-mockup.html` (visual source).

---

## Where it sits on the homepage

Confirmed homepage order (see [[CASE-STUDY]] discussion, 2026-09-09):

Hero → Pull-band → Featured Book → Upcoming Events → **Backlist** → Press →
Bio.

Backlist comes right after Events — by that point a visitor has seen the
new release and how to catch her live, so "what else has she written" is
the natural next beat. Bio closes the page.

## Why it pulls from the `book` CPT, unlike Press

Backlist is inherently CPT-driven — it's a grid of actual book posts, not
curated freeform content. This is the opposite shape from [[press.md]],
which is deliberately NOT tied to any CPT.

## Data model

Existing `book` CPT fields already used elsewhere in the theme
(`single-book.php`, the `featured-book` block): `featured_text`, `price`,
`publisher`, `available_in`, `isbn`, `publication_year`, `button_1`/
`button_2` (group: url + text), `awards` (repeater, `award_text` sub-field
— added 2026-09-09 for the Featured Book block).

**New field needed for Backlist**, added 2026-09-09 via wp-admin (same
DB/admin-managed pattern as the rest of `book`'s fields — this CPT's field
group has no local JSON/PHP export, see [[books.md]]):

| Field | Type | Label | Name (slug) |
|---|---|---|---|
| Backlist accolade line | Text | Backlist Blurb | `backlist_blurb` |

One line per book, shown under its cover in the grid. Combines multiple
honors into a single string rather than being a repeater — confirmed
2026-09-09 against real examples:

- Creep: "National Book Critics Circle Award finalist · Lambda Literary
  Award winner"
- Mean: "Ranked one of the best LGBTQ books of all time — O, The Oprah
  Magazine"
- Dahlia Season: "Winner of the Edmund White Award for debut fiction"

This field lives on the `book` CPT's own field group regardless of
whether `single-book.php` ever renders it — same "set up on the book,
even if it's not used there" logic as `awards`.

## Visual reference (mockup)

`redesign-2026/homepage-direction-mockup.html`:

- `.backlist` (`ink-ground`) — section wrapper, same padding scale as
  `.feature`/`.bio` (`clamp(3rem,7vw,6rem) clamp(1.25rem,4vw,3rem)`)
- `.backlist-inner` — `max-width: 72rem`, centered
- `.book-grid` — 4-column grid (`repeat(4,1fr)`, gap
  `clamp(1.25rem,2.6vw,2.25rem)`) — needs a mobile breakpoint (not yet
  specified in the mockup's own media queries; likely 2-up or 1-up under
  ~600px, to decide when building)
- `.book-card img` — `aspect-ratio: 2/3`, `object-fit: cover`, drop shadow
- `.book-card h3` — tracked uppercase sans, `.82rem`, paper-colored
- `.book-card p` — the accolade line (`backlist_blurb`), sans, `.78rem`,
  muted (`#a99f8f` in the mockup — not a real theme.json color slug, will
  need a real value or a palette addition when building)

Mockup's own sample grid: Creep, Mean, Dahlia Season, Painting Their
Portraits in Winter (the last one shown with an empty accolade — fine,
matches "not every book needs one").

## Architecture — to build tomorrow

Same shape as `upcoming-events`/`featured-book`: a block in `blocks/backlist/`
with `render.php`, registered from `inc/backlist.php`.

**Open decision — which books appear, and how:**

- **Automatic query** (all published `book` posts, ordered by
  `publication_year` or menu order, possibly excluding whichever book is
  currently selected in the Featured Book block) — zero admin config,
  matches the `upcoming-events` "place it once" philosophy.
- **Manual selection** (a relationship/repeater field picking specific
  books) — more control if you don't want every single book to always
  appear (e.g. once there are many titles).

Not decided yet — settle this before writing `render.php`.

**Other opens:**

- Mobile grid breakpoint (mockup doesn't specify one for `.book-grid`).
- Whether `.book-card`'s muted gray text color becomes a real theme.json
  slug or stays a one-off hex value (same category of call already made
  for `.blurbs`/`.blurbs cite` in `_featured-book.scss`).
