# Myriam Theme

Myriam is a custom child theme of Blocksy. It uses SCSS and Webpack for modern asset bundling and lives in a **monorepo** with `wp-content/` as the repository root.
This README is a WIP.

---

## 🚀 Requirements

- WordPress (preferably 6.0+ with FSE support)
- PHP 7.4+
- Node.js (18+ recommended)
- npm (v8+)
- Composer

---

## 📁 Repo Structure

This theme assumes your repo root is `wp-content/`. From there:

```
wp-content/
├── themes/
│   ├── blocksy/          ← Installed via Composer
│   └── myriam/     ← This theme
├── plugins/
├── composer.json
```

---

## ⚙️ Composer Setup (Parent Theme: Blocksy)

The **Blocksy** parent theme is installed via Composer using [WPackagist](https://wpackagist.org).

### 1. Install dependencies from the `wp-content` root:

```bash
composer install
```

This will place the Blocksy theme in:

```
wp-content/themes/blocksy/
```

> Blocksy is defined in `composer.json` and placed using `installer-paths`.

---

## 📦 Theme Setup

Navigate to the `myriam` theme directory:

```bash
cd wp-content/themes/myriam
npm install
```

---

## 🔨 Build Commands

From the `myriam` directory:

| Command           | Description                                      |
|-------------------|--------------------------------------------------|
| `npm run build`   | Development build (fast, unminified)             |
| `npm run dist`    | Production build (minified with hashed filenames)|
| `npm run watch`   | Rebuild on file changes                          |

The output CSS file is named `theme.min.[hash].css` and enqueued dynamically via PHP.

---

## 🧱 File Structure

```
myriam/
├── css/
│   ├── build/               # Output: hashed, minified CSS
│   └── src/                 # SCSS source files
│       ├── base/
│       │   ├── _layout.scss
│       │   └── _theme-values.scss (generated from theme.json)
│       └── main.scss
│       └── overrides.scss
├── js/
│   ├── shrink-header.js     # Optional JS
│   └── build/               # Output JS
├── functions.php            # Enqueues hashed CSS
├── theme.json               # FSE settings (layout, fonts, colors)
├── style.css                # Required WordPress theme header only
├── webpack.config.js        # Webpack build config
├── package.json             # npm scripts
```

---

## 🧠 Notes

- `overrides.css` is a css file that will print inline and the bottom of head for overrides that can't be made otherwise. Use sparingly, for emergencies, in place of !important.
- `style.css` exists only for WordPress recognition — styles are built via SCSS.
- CSS is enqueued via `glob()` and `filemtime()` in `functions.php` for cache-busting and hash awareness.

---

## 🧼 Cleanup

To remove previous build artifacts:

```bash
rm -rf css/build js/build
npm run dist
```

---

## 🧠 About

Built by Geoff Cordner.  
Based on the [Blocksy]([https://creativethemes.com/blocksy/]) WordPress theme.
