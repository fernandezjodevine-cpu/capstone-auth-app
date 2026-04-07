# Google OAuth 2.0 Setup Guide

This UtangListo app now supports Google OAuth 2.0 for seamless login.

## Prerequisites

- Composer dependencies installed (`vendor/` folder exists)
- Google Cloud account with billing enabled

## Setup Steps

### 1. Create Google Cloud Project

1. Go to [Google Cloud Console](https://console.cloud.google.com)
2. Create a new project
3. Enable the **Google+ API** (or Identity Toolkit API)
4. Go to **Credentials** → **Create Credentials** → **OAuth client ID**
5. Choose **Web application**
6. Add Authorized redirect URIs:
   ```
   http://localhost/capstone-auth-app/oauth2callback.php
   ```
   (Change `localhost` to your domain in production)

### 2. Configure Your App

#### Option A: Environment Variables (Recommended)

Create a `.env` file in the root directory:
```bash
cp .env.example .env
```

Then edit `.env` with your Google credentials:
```
GOOGLE_CLIENT_ID=xxxxx.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=xxxxx
GOOGLE_REDIRECT_URI=http://localhost/capstone-auth-app/oauth2callback.php
```

#### Option B: Edit Config File

Edit `config/google-oauth.php` and replace:
```php
'client_id' => 'YOUR_CLIENT_ID_HERE',
'client_secret' => 'YOUR_CLIENT_SECRET_HERE',
```

### 3. Database Schema

The `users` table now includes OAuth fields:
- `oauth_provider` - Provider name (google, etc)
- `oauth_id` - User ID from provider
- `oauth_access_token` - Access token for API calls

These fields are auto-created on first app startup.

### 4. Update Callback URL (if needed)

If your app URL changes, update:
- Google Cloud Console: Authorized Redirect URIs
- `.env` file: `GOOGLE_REDIRECT_URI`
- `oauth2callback.php`: Line 8

## How It Works

1. User clicks **"Sign in with Google"** button
2. User is redirected to Google login
3. After authentication, Google redirects to `oauth2callback.php`
4. App creates or updates user account
5. User is logged in and redirected to dashboard

## Features

- ✅ New user registration via Google
- ✅ Existing user linking via Google
- ✅ Automatic email verification
- ✅ Access token stored for future API calls
- ✅ Fallback to regular login

## Troubleshooting

| Issue | Solution |
|-------|----------|
| "Invalid client" error | Check Client ID and Client Secret |
| Redirect URI mismatch | Ensure Google Console URI matches your app |
| Blank page after login | Check `.env` file for missing credentials |
| Token expired error | App will prompt user to re-authenticate |

## Security Notes

- Never commit `.env` file to version control
- Use HTTPS in production
- Regenerate tokens if they're exposed
- Scope requests limited to `email` and `profile`
