# Color Sources — Full Inventory

**Date:** 2026-08-24
**Theme:** Myriam2026 (GeneratePress child theme)
**Scope:** `/wp-content/themes/myriam2026` + the `myriamgurba` database (docksal env)

Analysis only — nothing here was changed. Purpose: know every place a color
lives before the palette gets swapped for the redesign (see the theme's
`redesign-2026/CLAUDE.md`).

---

## Executive summary

Colors for this site live in **four independent places**, none of which are
mechanically linked to each other. Two are actively painting the live
homepage today, one is installed and capable but currently dormant, and one
SCSS reference is already silently broken. Editing the palette in only one
place (most likely `theme.json`) will not update the others — every source
below needs to be touched by hand.

---

## The four sources

### 1. `theme.json` (file) — the block-editor palette

`defaultPalette: false` (theme.json:6) disables WordPress's built-in core
palette in the picker. Eight custom colors defined:

| slug | hex | name |
|---|---|---|
| poppy-yellow | `#F2C60A` | Poppy Yellow |
| poppy-state-pink | `#ed008e` | Poppy State Pink |
| dark-teal | `#0b2225` | Dark Teal |
| black | `#000000` | Black |
| burnt-sienna | `#261404` | Burnt Sienna |
| dark-brick | `#732424` | Dark Brick |
| white | `#FAFBFC` | White |
| not-quite-white | `#F2F2F2` | Not Quite White |

No root-level `styles.color` is set in theme.json — no theme-wide default
text/background/link color. This palette drives the Gutenberg block editor's
color picker (heavily used — most `.has-*-color` classes throughout actual
page/post content come from here) and is what the child theme's own
header/footer SCSS chose to read from (see §5).

`defaultPalette: false` only hides the core palette from the picker *going
forward* — it doesn't strip already-applied core classes (e.g.
`.has-vivid-red-color`) from existing content, or from core's own
block-library CSS.

### 2. GeneratePress's Customizer settings — `wp_options.generate_settings` (database only)

Not in any file — confirmed via `wp option get generate_settings` inside the
docksal container. Contains its own `global_colors` array:

```json
"global_colors": [
  {"slug": "contrast",   "color": "#0b2225"},
  {"slug": "contrast-2", "color": "#261404"},
  {"slug": "contrast-3", "color": "#F2C60A"},
  {"slug": "base",       "color": "#F2F2F2"},
  {"slug": "base-2",     "color": "#FAFBFC"},
  {"slug": "base-3",     "color": "#ffffff"},
  {"slug": "accent",     "color": "#ed008e"},
  {"slug": "dark-brick", "color": "#732424"}
]
```

Mapped by hex: `contrast`=dark-teal, `contrast-2`=burnt-sienna,
`contrast-3`=poppy-yellow, `base`=not-quite-white, `base-2`=white,
`accent`=poppy-state-pink, `dark-brick`=dark-brick. Seven of eight are
hand-duplicated from theme.json. **`base-3` (`#ffffff`, pure white) has no
theme.json counterpart** — theme.json's "white" is `#FAFBFC`, a warm
off-white; GP has its own separate pure white nobody reconciled.

This same option also sets GP's own nav link colors directly, independent
of theme.json:
```
navigation_text_color: var(--contrast-3)     → poppy-yellow
navigation_text_hover_color: var(--base-3)   → pure white
navigation_text_current_color: var(--base-2) → off-white (theme.json white)
```
`header_background_color`, `content_background_color`, and
`footer_background_color` are all explicitly neutralized to `rgba(...,0)` —
GP's own chrome backgrounds are switched off, ceding that territory to the
child theme's SCSS header/footer partials instead.

These values are compiled into actual `:root` CSS by GeneratePress and
cached in `wp_options.generate_dynamic_css_output` — confirmed the rendered
homepage really does emit `--contrast`, `--contrast-2`, `--contrast-3`,
`--base`, `--base-2`, `--base-3`, `--accent`, `--dark-brick` as live custom
properties.

### 3. Stackable plugin's global color scheme — `wp_options.stackable_global_color_schemes` (database, plugin-specific)

The Stackable Gutenberg blocks plugin (`stackable-ultimate-gutenberg-blocks`,
active) has its own independent "Alternate Scheme":

```json
{
  "name": "Alternate Scheme",
  "colorScheme": {
    "backgroundColor": "#0f0e17",
    "headingColor": "#fffffe",
    "textColor": "#fffffe",
    "linkColor": "#f00069",
    "accentColor": "#f00069",
    "buttonBackgroundColor": "#f00069",
    "buttonTextColor": "#fffffe",
    "buttonOutlineColor": "#fffffe"
  }
}
```

`#f00069` is a **fourth, distinct pink** — close to but not equal to
poppy-state-pink (`#ed008e`).

Checked whether this is actually live: the homepage (post ID 3156, "Front
Page") does contain `stackable/columns` and `stackable/column` blocks, but
their block attributes carry no `colorScheme` reference or explicit hex
color. **Dormant, not active** — the plugin and scheme both exist and could
be applied to any Stackable block at any time, but nothing on the current
homepage actually renders these colors.

### (Checked and ruled out)

- **Legacy Additional CSS** — a `custom_css` post named `myriam` (the old
  theme's slug) exists in the database but contains only
  `.hide { display: none !important; }` — no colors, and inactive anyway
  since Additional CSS is keyed to the exact active stylesheet slug
  (`myriam2026` ≠ `myriam`).
- **WooCommerce transactional email colors**
  (`woocommerce_email_*_color` options) — real, but scoped to
  order-confirmation emails, not front-end pages. Out of scope unless emails
  are part of the redesign too.

---

## How `theme.json` compiles into SCSS

`webpack.config.js:7-32` runs at **config-load time** — on every
`build`/`dev`/`watch`/`start` — and does this:

```js
const themeJson = require("./theme.json");
const colors = themeJson.settings?.color?.palette || [];
colors.forEach((color) => {
  const varName = color.slug.replace(/-/g, "_");
  scssVars += `$${varName}: ${color.color};\n`;
});
fs.writeFileSync("./css/src/base/_theme-values.scss", scssVars);
```

Only `settings.color.palette` and `settings.layout` (content/wide width) are
pulled — nothing from `fontFamilies`, `fontSizes`, `spacingSizes`, or
`styles`. Current generated output:

```scss
$content-width: 700px;
$wide-width: 1290px;
$poppy_yellow: #F2C60A;
$poppy_state_pink: #ed008e;
$dark_teal: #0b2225;
$black: #000000;
$burnt_sienna: #261404;
$dark_brick: #732424;
$white: #FAFBFC;
$not_quite_white: #F2F2F2;
```

`_theme-values.scss` is fully generated — hand-editing it is pointless, it's
overwritten on the next build. This is a **one-way, build-time snapshot**:
editing `theme.json`'s hex values does nothing to anything that consumes
these Sass variables until webpack reruns. (Per the user: staleness until a
rebuild is fine and expected — noted here for completeness, not as a
problem to solve.)

---

## Three color-access patterns in the compiled SCSS

**A. Build-time Sass variables** (static hex baked into compiled CSS, needs
rebuild to reflect a theme.json change):
- `_layouts.scss` — namespaced: `theme-values.$burnt_sienna`,
  `theme-values.$dark_teal`, `theme-values.$poppy_state_pink`,
  `theme-values.$not_quite_white`
- `_student-portal.scss` — bare, via `@use 'theme-values' as *;`:
  `$dark_teal`, `$poppy_yellow`, `$black`, `$white`, `$dark_brick`,
  `$not_quite_white`
- `_utilities.scss` brand classes (`.bg-brand-primary`, `.bg-brand-secondary`,
  `.bg-neutral-light`) — `theme-values.$burnt-sienna` etc. (hyphenated
  spelling; works because Sass treats `-`/`_` as equivalent in identifiers,
  but it's an inconsistent convention next to the underscored form used
  elsewhere)

**B. Live WP runtime custom properties** (reads theme.json directly on page
load, no rebuild needed):
- `_header.scss` — `var(--wp--preset--color--burnt-sienna)`,
  `var(--wp--preset--color--poppy-yellow)`,
  `var(--wp--preset--color--white)`,
  `var(--wp--preset--color--not-quite-white)`,
  `var(--wp--preset--color--poppy-state-pink)`
- `_footer.scss` — `var(--wp--preset--color--dark-brick)`,
  `var(--wp--preset--color--not-quite-white)`,
  `var(--wp--preset--color--white)`,
  `var(--wp--preset--color--poppy-yellow)`

**C. GeneratePress's own positional custom property** — used once, see
Finding F1 below.

`_book.scss` has **no brand color styling at all** — the only color-ish
line is `var(--gp-border-color, #ddd)`, an inherited GeneratePress fallback.
Book-cover display currently carries zero palette-driven styling of its own.

---

## Findings

#### F1. `.dark-teal-link` references a custom property that doesn't exist — currently broken
**Severity:** High (live, visible bug)
**File:** `css/src/base/_utilities.scss`

```scss
.dark-teal-link {
    a {
        color: var(--theme-palette-color-3) !important;
    }
}
```

`--theme-palette-color-N` is GeneratePress's *positional* custom-property
naming for its global colors (keyed to a color's original creation-order ID,
not its current slug or array position) — a different, older addressing
scheme than the semantic `--contrast`/`--accent`/etc. variables GP also
generates. Checked the live rendered homepage directly: only the semantic
variables are ever emitted. `--theme-palette-color-3` is not defined
anywhere on the page.

This class is not dead code — it's applied right now on the "CREEP" book
heading in the Books grid:
```html
<h2 class="wp-block-heading ... front-cover dark-teal-link has-yellow-color has-text-color ..." id="books">
  <em><a href="…/book/creep/">CREEP</a></em>
</h2>
```
The intended dark-teal override on the nested link isn't landing; it falls
through to whatever color it'd otherwise inherit.

#### F2. `.white-link` is defined twice, both hardcoding a value that's already a variable
**Severity:** Medium
**File:** `css/src/base/_utilities.scss`

Two separate `.white-link { a { ... } }` blocks, both using literal
`#F2F2F2` — which is exactly `$not_quite_white` / `not-quite-white`, already
available in this file via `@use 'theme-values';`. The second block adds a
`:hover` state computing `darken(#F2F2F2, 20%)` inline — an unnamed derived
shade computed off the hardcoded literal rather than the variable.

#### F3. Ungoverned grays with no palette backing
**Severity:** Low
**Files:** `_student-portal.scss`, `_events.scss`

`#999`, `#666` (duplicated independently in both files), `#ddd`, `#fdf0f0`,
and `#d4a900` (a gold close to but not equal to `$poppy_yellow`/`#F2C60A` —
unclear if an intentional darker variant or a drifted duplicate) appear with
no connection to any of the 8 theme.json/GP colors. None of the 8 defined
colors are grays — every neutral/muted tone in the UI is ad hoc.

#### F4. Two parallel "source of truth" palettes, manually synced, no enforcement
**Severity:** Medium (process risk, not a current bug)

`theme.json` and `generate_settings.global_colors` agree on 7 of 8 colors
today only because someone typed the same hex values into both places by
hand. Nothing keeps them in sync — editing one during the redesign and
forgetting the other will silently reintroduce drift (as `base-3` already
demonstrates: GP has a pure white with no theme.json equivalent at all).

#### F5. Stackable's "Alternate Scheme" is a dormant fourth palette
**Severity:** Low (not currently active, but live-capable)

See source §3 above. Not painting anything today, but any editor could
apply it to a Stackable block at any time, introducing `#f00069` (a pink
that doesn't match `poppy-state-pink`) and `#0f0e17`/`#fffffe` with no
relationship to the brand palette.

---

## Bottom line for the eventual palette change

Swapping the 8 hex values in `theme.json` alone will immediately repaint
header, footer, and any Gutenberg content using `.has-*-color` classes —
but will leave GP's nav link colors, the Stackable dormant scheme, and
`.dark-teal-link` (already broken) completely untouched, since none of them
read from theme.json at all. A real palette change needs to touch
`theme.json` **and** `generate_settings.global_colors` **and** decide what
to do about the two SCSS findings above (F1, F2) and the Stackable scheme
(F5), not just edit one JSON file.
