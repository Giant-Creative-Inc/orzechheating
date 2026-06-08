# Orzech Theme — Gulp Build System

## First-time setup

```bash
cd wp-content/themes/orzech-theme
npm install
```

## Commands

| Command | Description |
|---------|-------------|
| `npm run dev` | Compile SCSS with source maps, watch for changes |
| `npm run build` | Full production build (minified CSS, JS, optimised images) |
| `npm run critical` | Extract & write critical CSS (requires local site running) |

---

## How it works

### CSS
- **Source:** `assets/scss/`
- **Output:** `assets/css/*.min.css`
- The main stylesheet (`main.scss`) imports variables, mixins, fonts, global, header, and footer partials.
- **Page-specific CSS** lives in `assets/scss/pages/{slug}.scss` and compiles to `assets/css/{slug}.min.css`.
- WordPress auto-loads the page-specific file if the page slug matches (see `includes/enqueue.php`).

### Critical CSS
- `gulp critical` visits the pages listed in `CRITICAL_PAGES` (gulpfile.js) and extracts above-the-fold styles.
- Output: `assets/css/critical-{slug}.min.css`
- These are inlined into `<head>` by `includes/critical-css.php`, and the full stylesheets are loaded async.
- Update `SITE_URL` in `gulpfile.js` to match your Local dev URL.
- **Requires the local site to be running before you run `gulp critical`.**

### The existing style.css
- `style.css` in the theme root is the original salient-child CSS, preserved as-is.
- It is always enqueued and loads as usual.
- New styles go in SCSS. The compiled `assets/css/style.min.css` is enqueued on top of `style.css`.

### JS
- Source files: `assets/js/src/*.js`
- Output: `assets/js/main.js`
- `custom.js` (accessibility/hero JS) is enqueued separately and is not part of the Gulp JS pipeline.

---

## Adding a new page stylesheet

1. Create `assets/scss/pages/{page-slug}.scss`
2. Add an entry to `cssEntries` in `gulpfile.js`:
   ```js
   ['./assets/scss/pages/{page-slug}.scss', '{page-slug}.min.css'],
   ```
3. Run `npm run build` (or `npm run dev` while developing).
4. WordPress will automatically enqueue the compiled file on that page.

## Adding critical CSS for a new page

1. Add the page path to `CRITICAL_PAGES` in `gulpfile.js`:
   ```js
   const CRITICAL_PAGES = [
     '/',
     '/your-page-slug',
   ];
   ```
2. Make sure `SITE_URL` is correct and the site is running.
3. Run `npm run critical`.
