# Myriam2026 — Case Study

**Date:** 2026-09-08
**Purpose:** A pitch/case-study writeup of this theme, for prospective
employers — what it is, what's custom, what problems it solves, and why
it was built the way it was. Companion to [[THEME-AUDIT-REPORT]] (the
warts-and-all internal audit) and [[layout.md]]/[[events.md]]/[[books.md]]
(the decision trail this draws on).

---

## What it is

A GeneratePress child theme for a working author site
(myriamgurba.com — writer and activist Myriam Gurba). WooCommerce runs
a small merch store, ACF structures book/page content, and several
custom post types handle Books, Writing, Events, and a gated student
portal with class content. The theme is mid-redesign: replacing a
visually inconsistent older build — colors that had drifted into a
different saturated background per section with no shared system,
every heading using the same loud treatment regardless of context —
with one deliberate, unified design system.

## What's actually custom, not just configured

- **A theme.json-driven color/typography engine**, not a decorative
  config file. It strips WordPress core's ~28 default colors and
  GeneratePress's own default fonts at the filter level, locks GP's
  "Global Colors" feature to a whitelist with real Customizer-side
  validation (a rejected save shows an actual error, not a silent
  revert), and wires up editor-styles correctly so the block editor
  matches the live front end exactly.
- **A from-scratch events system**, replacing a heavyweight events
  plugin the client found unusable. Not ACF, not a page-builder block,
  not a plugin — a plain custom post type with five flat fields, plus a
  real Gutenberg block (`myriam2026/upcoming-events`) that's fully
  automated: it queries what's actually upcoming, chooses its own
  layout based on how many events that is, and drops events a day
  after they pass. No manual homepage editing, ever, once it's placed.
- **One shared page-structure mechanism**, applied consistently across
  templates instead of five templates each inventing their own. The
  header-clearance value isn't a hardcoded guess — it's measured from
  the actual rendered header height in a few lines of JS, with a
  matching CSS fallback so there's no visible layout shift on load.
- **A hand-rolled lightbox**, about 70 lines total, instead of a JS
  gallery library, because the actual need (one image, click to
  enlarge, no gallery navigation) never justified one.

## The problems, specifically, and why this approach

1. **Color/type drift.** The old site's palette had drifted into a
   different fully-saturated background per section, and every heading
   used the same loud condensed treatment. Fixed at the theme.json
   level, described above — not a per-page patch.
2. **A plugin doing too much, badly.** The events plugin had 28 stale,
   never-cleaned historical entries live on the site and no way to
   automatically hide past events or adapt to how many were upcoming.
   Rebuilt as exactly the amount of system the actual data needed — five
   fields, one CPT — not because plugins are bad, but because this
   problem's shape didn't justify one.
3. **Five templates, five different answers to "how does a page show
   its own title."** One page's title had been disabled entirely via a
   GeneratePress setting and replaced with a hand-typed heading that
   then rendered invisibly behind the fixed header. Each template's
   cause was different — GP's archive-title component vs. its
   entry-header vs. bespoke markup vs. an untouched third-party plugin
   template vs. that per-page "disable headline" flag — and each was
   root-caused individually before being unified under one shared
   mechanism, rather than papering over the visible symptom.

**Why not just reach for a plugin each time?** The recurring judgment
call, made explicitly rather than assumed: does this problem's actual
shape justify the dependency and its abstraction layer? Five flat
fields on one CPT doesn't need ACF's Group/Repeater machinery. One
image per card doesn't need a gallery library's zoom/swipe/caption
tooling. In both cases the custom version was also the *smaller* amount
of code — this was matching the tool to the problem, not
dependency-avoidance as a principle in itself.

## Current state, honestly

Real progress since the first pass at this codebase: the most visible
"unproofread" tell found in that pass — scaffold placeholder text
(`'your-textdomain'`) still sitting in a shipped template — is fixed,
and that file (`single-book.php`) got a real rebuild in the process: a
dead "Button 2" field with real, previously-invisible content got
wired up, and a genuine escaping bug got caught and fixed via
`phpcs --standard=WordPress`. The events block is a legitimately
correct custom Gutenberg block — `block.json`, a minimal hand-rolled
editor script, proper server-side rendering — which is real block
development, not a shortcode or widget dressed up.

What's still outstanding, deliberately deferred rather than hidden:
inconsistent function-naming (a real collision risk, not just a style
nit), inconsistent Sass `@use` conventions across partials, one
leftover second-person AI-pairing comment in `_mixins.scss`, and no
committed `phpcs.xml.dist` despite having run that ruleset by hand
repeatedly now. **Decided:** these get addressed once the theme is
actually deployed, not before — right now the priority is making sure
the redesign itself is moving in the right direction, not polishing
code paths that may still shift.

One note on authorship, since it came up directly: this is, and will
stay, solo-authored — collaborative theme work done for an agency is
that agency's IP and isn't this developer's to show. That's not a
portfolio weakness to fix; it's the normal shape of freelance/solo
work, and treating it as a gap would disqualify most independent
developers. What actually substitutes for the process-visibility a
team's PR history would otherwise provide is already here: the
`notes/` files (this one included) document *why* decisions were made,
not just what shipped — arguably a stronger signal than a generic PR
thread, since it holds up over time without needing someone else
present to ask.

## Open question, not decided — revisit later

Whether `book` should move off ACF onto a hand-rolled custom-fields
setup, the same call already made for `event` (see [[books.md]] /
[[THEME-AUDIT-REPORT]] N4). Counter-consideration: agencies use ACF
heavily, so demonstrating real ACF competency in the portfolio has its
own value — moving *everything* off it isn't obviously the right move
just because `event` didn't need it. Worth weighing deliberately later,
not defaulting to "less ACF is always better."
