# AGENTS.md

Guidance for AI agents working in this repository.

## Project overview

This is a **static HTML** Git tutorial/demo repository. There is no backend, no package manager, no build step, and no configured linters or test runners.

| Path | Role |
|------|------|
| `index.html`, `about.html` | Simple “Online Repo Update” placeholders |
| `home.html`, `template.html`, `template2.html` | Branch/PR demo pages |
| `contact.html` | Minimal contact page |
| `landing.html` | Large e-commerce-style mock (“The Concept Key”) |
| `images/` | PNG assets used by some pages |
| `demo.txt` | Plain-text Git demo file |

## Cursor Cloud specific instructions

### Services

No long-running application services are required. For browser testing and relative asset URLs, serve the repo over HTTP:

```bash
cd /workspace && python3 -m http.server 8080
```

Use a dedicated tmux session (for example `static-http-server`) so the server stays up across agent steps.

### Lint, test, and build

None are configured in this repo. Do not expect `npm test`, ESLint, or a compile step unless tooling is added later.

### Manual verification (hello world)

With the static server on port 8080:

1. Open `http://localhost:8080/landing.html` — confirm “The Concept Key” nav/branding.
2. Click the logo → `index.html` — confirm “Online Repo Update” headings.
3. Open `http://localhost:8080/contact.html` — confirm “Contact Us”.
4. Open `http://localhost:8080/home.html` — confirm “Developer 1 Branch” demo text.

Quick non-GUI check: `curl -s -o /dev/null -w "%{http_code}" http://localhost:8080/index.html` should return `200` for each HTML file.

### Caveats

- `landing.html` references many image/CSS assets that are **not** in `images/`; broken images in the browser are expected.
- Pages work over `file://` for simple text demos, but HTTP is preferred for consistency.
