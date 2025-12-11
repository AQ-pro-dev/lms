# Vimeo API Setup Guide

## Step 1: Get Vimeo API Credentials

1. Go to [Vimeo Developer Portal](https://developer.vimeo.com/)
2. Log in with your Vimeo account
3. Click on "Create an app" or select an existing app
4. You'll need three credentials:
   - **Client ID** (also called App ID)
   - **Client Secret**
   - **Access Token** (you may need to generate this)

## Step 2: Generate Access Token (CRITICAL - This is likely your issue!)

1. In your Vimeo app settings, go to "Authentication" or "Access Tokens"
2. Click "Generate Access Token" or "Generate New Token"
3. **IMPORTANT**: You MUST select these scopes (permissions):
   - ✅ `video.upload` - **REQUIRED** for uploading videos
   - ✅ `video.edit` - Required for editing video metadata
   - ✅ `video.delete` - Optional, for deleting videos
   - ✅ `public` - May be required for some operations
   - ✅ `private` - If you want private videos
4. **Make sure `video.upload` is checked!** This is the most common cause of upload errors
5. Copy the generated access token
6. **Important**: If you're using an existing token, it might not have the correct scopes. Generate a NEW token with the correct scopes.

## Step 3: Add Credentials to .env File

Open your `.env` file in the root directory and add these three lines:

```env
VIMEO_CLIENT=your_client_id_here
VIMEO_SECRET=your_client_secret_here
VIMEO_ACCESS=your_access_token_here
```

**Important Notes:**
- Replace `your_client_id_here`, `your_client_secret_here`, and `your_access_token_here` with your actual credentials
- Do NOT use quotes around the values
- Do NOT leave any spaces around the `=` sign
- Make sure there are no extra spaces or special characters

## Step 4: Clear Configuration Cache

After adding the credentials, run these commands:

```bash
php artisan config:clear
php artisan cache:clear
```

## Step 5: Verify Configuration

You can verify your configuration is loaded correctly by checking:

```bash
php artisan tinker
```

Then run:
```php
config('vimeo.connections.main.client_id')
config('vimeo.connections.main.client_secret')
config('vimeo.connections.main.access_token')
```

These should return your credentials (not null or 'your-client-id').

## Troubleshooting

### Error: "Vimeo API credentials are not configured"
- Make sure you've added all three variables to `.env`
- Make sure you've cleared the config cache: `php artisan config:clear`
- Check that there are no typos in the variable names
- Verify the values don't have quotes or extra spaces

### Error: "Invalid parameter" or "Unable to initiate an upload"
- **MOST COMMON**: Verify your access token has the `video.upload` scope enabled
- Check that your access token hasn't expired
- Ensure your video file is a supported format (MP4, AVI, MOV)
- Check file size limits (Vimeo allows up to 200 GB)

### Error: "You can't upload the video. Please get in touch with the app's creator."
**This error means your access token doesn't have upload permissions!**

**Solution:**
1. Go to your Vimeo Developer Portal: https://developer.vimeo.com/
2. Select your app
3. Go to "Authentication" or "Access Tokens"
4. **Generate a NEW access token** (don't reuse an old one)
5. **Make absolutely sure `video.upload` scope is CHECKED**
6. Copy the new access token
7. Update your `.env` file with the new token:
   ```env
   VIMEO_ACCESS=your_new_access_token_here
   ```
8. Clear config cache:
   ```bash
   php artisan config:clear
   ```
9. Try uploading again

**Why this happens:**
- Old tokens might not have had upload permissions
- Tokens generated before enabling uploads won't work
- You need to generate a NEW token with the correct scopes

### Error: "Authentication failed"
- Double-check your Client ID, Client Secret, and Access Token
- Regenerate your access token if needed
- Make sure you're using the correct credentials for your Vimeo app

## Example .env Entry

```env
# Vimeo API Configuration
VIMEO_CLIENT=abc123def456ghi789
VIMEO_SECRET=xyz789uvw456rst123
VIMEO_ACCESS=v1.abc123def456ghi789jkl012mno345pqr678stu901vwx234yz
```

## Need Help?

- Check Laravel logs: `storage/logs/laravel.log`
- Check Vimeo API documentation: https://developer.vimeo.com/api/guides/videos/upload
- Verify your app has the correct permissions in Vimeo Developer Portal
