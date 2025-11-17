# GitHub Pages Deployment Guide

## Prerequisites
Your Laravel backend must be hosted separately (GitHub Pages only serves static files).

**Backend Hosting Options:**
- Railway (free tier available)
- Render (free tier)
- Heroku
- DigitalOcean App Platform

## Steps to Deploy

### 1. Host Your Laravel Backend
Deploy to a hosting service and get the live URL (e.g., `https://yourapp.railway.app`)

### 2. Update API URL
Edit `frontend/.env.production`:
```
REACT_APP_API_URL=https://yourapp.railway.app/api
```

### 3. Install Deployment Tool
```bash
cd frontend
npm install --save-dev gh-pages
```

### 4. Build and Deploy
```bash
cd frontend
npm run deploy
```

This will automatically:
- Set the correct base path for GitHub Pages
- Build the React app with production settings
- Push to the `gh-pages` branch
- Deploy to `https://Fuyad22.github.io/16november_wp`

**Note:** Local development (`npm start`) runs on `http://localhost:3000` (root path). GitHub Pages deployment handles the `/16november_wp` path automatically.

### 5. Enable GitHub Pages
1. Go to repo Settings → Pages
2. Source: Deploy from branch
3. Branch: `gh-pages` / `root`
4. Save

### 6. Update Backend CORS
Add your GitHub Pages URL to Laravel's allowed origins in `config/cors.php`:
```php
'allowed_origins' => [
    'http://localhost:3000',
    'https://Fuyad22.github.io'
],
```

## Troubleshooting

**Blank page on GitHub Pages:**
- Check browser console for errors
- Verify `homepage` in `frontend/package.json` matches your repo name
- Ensure `REACT_APP_API_URL` points to live backend

**API calls fail:**
- Backend CORS must allow your GitHub Pages domain
- Backend must be HTTPS (GitHub Pages is HTTPS)
- Check backend is actually running and accessible

**404 on refresh:**
- GitHub Pages doesn't support client-side routing by default
- Copy `frontend/public/index.html` to `frontend/public/404.html` before deploying
