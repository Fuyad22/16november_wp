# WP16 Laravel + React Stack

Full-stack demo that pairs a Laravel 10+ backend (file-based storage in `storage/posts.json`) with a React + Tailwind frontend for managing posts, handling email verification, and showcasing deployment to GitHub Pages.

## Features

- JSON-backed API: no external database is required; posts are stored in `storage/posts.json`.
- Email verification flow with 6-digit codes (mailers stubbed for local dev, but logic is ready for SMTP).
- React Router powered UI with dedicated pages for Home, Create Post, Verify Email, and Manage Posts.
- Single command (`npm run server`) that boots both Laravel (`php artisan serve`) and the React dev server through `concurrently`.
- GitHub Pages deployment pipeline for the frontend with environment overrides via `.env.production`.

## Tech Stack

- Laravel 10 / PHP 8.2+
- React (Create React App) + TailwindCSS + lucide-react icons
- Node.js/npm for tooling, `concurrently` for multi-process scripts
- GitHub Pages for frontend hosting (see `GITHUB_PAGES_DEPLOY.md`)

## Prerequisites

- PHP 8.2+
- Composer 2+
- Node.js 18+ and npm
- Git Bash (or any shell) with access to Git + OpenSSL (for Laravel key generation)

## Local Setup

```bash
git clone https://github.com/Fuyad22/16november_wp.git
cd 16november_wp

composer install
npm install          # installs root dependencies (concurrently, vite helpers, etc.)
cd frontend && npm install && cd ..

cp .env.example .env
php artisan key:generate
# configure mail settings if you plan to send real emails
```

If you need seeded storage, copy `storage/posts.example.json` (create one) to `storage/posts.json`, or hit the API once to let Laravel create it automatically.

## Running the Project

```bash
npm run server
```

The script launches:

- Backend: `php artisan serve --host=0.0.0.0 --port=8000`
- Frontend: `npm start` within `frontend/`

Visit `http://localhost:3000` for the React app. API requests are proxied to `http://127.0.0.1:8000` by default.

Useful alternatives:

- `npm run backend` – Laravel server only.
- `npm run frontend` – React dev server only (expects backend already running).

## Environment Variables

- Laravel: configure `.env` as usual (`APP_URL`, `MAIL_MAILER`, etc.). File storage uses `storage/posts.json`, so no DB credentials are required.
- React: create `frontend/.env.local` for local overrides and `frontend/.env.production` for GitHub Pages (already tracked with `REACT_APP_API_URL`).

When deploying, update `REACT_APP_API_URL` so the static site knows how to reach your hosted Laravel API.

## Email Verification Flow

1. User submits email and receives a 6-digit code (logged locally if mail is not configured).
2. `EmailVerificationController` validates the code and issues a temporary token.
3. Token is required to create posts via `PostController`.

Because we persist everything to JSON, restart-safe verification works out-of-the-box.

## Frontend Pages

- **Home** – quick navigation cards for each workflow.
- **Create Post** – token + post submission form.
- **Verify Email** – request/verify 6-digit codes.
- **Manage Posts** – list posts, delete them, and view status indicators.

All routes live in `frontend/src/App.js` using `react-router-dom`.

## Deploying to GitHub Pages

1. Host the Laravel API (Render, Railway, VPS, etc.).
2. Update `frontend/.env.production` with the public API URL.
3. From the repo root run:

	```bash
	cd frontend
	npm run deploy
	```

Detailed backend hosting, CORS configuration, and DNS guidance is in `GITHUB_PAGES_DEPLOY.md`.

## Troubleshooting

- **Port already in use**: stop stray Node/PHP processes (`taskkill /F /IM node.exe` on Windows) before re-running `npm run server`.
- **storage/posts.json missing**: create the file manually or let Laravel write it by hitting `POST /api/posts` once.
- **CORS errors on GitHub Pages**: confirm the deployed backend lists the GitHub Pages domain in its allowed origins.

## License

MIT. See `LICENSE` if you plan to reuse significant portions.
