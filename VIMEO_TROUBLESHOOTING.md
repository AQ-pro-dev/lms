# Vimeo Upload Error Troubleshooting

## Error: "You can't upload the video. Please get in touch with the app's creator."

This error occurs when your **Access Token doesn't have upload permissions** OR when your **credentials don't match**.

## Critical Check: All Credentials Must Be From the SAME App

**IMPORTANT**: Your Client ID, Client Secret, and Access Token **MUST** all come from the **SAME Vimeo app**. You cannot mix credentials from different apps.

### Step-by-Step Verification:

1. **Go to Vimeo Developer Portal**: https://developer.vimeo.com/
2. **Select your app** (the one you're using for this project)
3. **Check your App Details page** - Note your:
   - Client ID (App ID)
   - Client Secret

4. **Go to "Authentication" or "Access Tokens"**
5. **Generate a NEW Access Token** for THIS specific app
6. **Make sure these scopes are checked:**
   - ✅ Public (required)
   - ✅ Upload (CRITICAL!)
   - ✅ Edit
   - ✅ Video Files
   - ✅ Private (if you want private videos)

7. **Copy the NEW access token**

8. **Update your `.env` file** with ALL THREE credentials from the SAME app:
   ```env
   VIMEO_CLIENT=your_client_id_from_this_app
   VIMEO_SECRET=your_client_secret_from_this_app
   VIMEO_ACCESS=your_new_access_token_from_this_app
   ```

9. **Clear config cache:**
   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

10. **Restart your web server** (if using PHP-FPM or similar)

## Common Mistakes:

### ❌ Wrong: Mixing credentials from different apps
```env
VIMEO_CLIENT=app1_client_id
VIMEO_SECRET=app1_client_secret
VIMEO_ACCESS=app2_access_token  # ❌ WRONG! Different app!
```

### ✅ Correct: All from the same app
```env
VIMEO_CLIENT=app1_client_id
VIMEO_SECRET=app1_client_secret
VIMEO_ACCESS=app1_access_token  # ✅ CORRECT! Same app!
```

## Verify Your Configuration:

Run this command to check if your config is loaded:

```bash
php artisan tinker
```

Then run:
```php
config('vimeo.connections.main.client_id')
config('vimeo.connections.main.client_secret')
config('vimeo.connections.main.access_token')
```

All three should return values (not null).

## Check Laravel Logs:

After attempting an upload, check `storage/logs/laravel.log` for:
- "Vimeo credentials verified" - Should show credential lengths
- "Vimeo API connection verified" - Should show authenticated user
- "Vimeo API error" - Will show the actual error details

## Still Not Working?

1. **Double-check your .env file** - Make sure there are no typos, quotes, or extra spaces
2. **Verify the access token was generated with "Upload" scope checked**
3. **Make sure you cleared the config cache** after updating .env
4. **Check that all three credentials are from the SAME Vimeo app**
5. **Try generating a completely new access token** (don't reuse old ones)

## Need More Help?

Check the Laravel logs at `storage/logs/laravel.log` for detailed error information. The logs will show:
- Which credentials are being used
- Whether the API connection is successful
- The exact error from Vimeo API
