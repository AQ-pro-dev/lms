# Fix Vimeo Upload Error - Step by Step Guide

## Current Issue
Your access token is **invalid or incorrectly configured**. The diagnostic shows:
- ❌ Access token is only 32 characters (should be 128+ characters)
- ❌ "The authentication token is missing a user ID"
- ❌ Upload quota information not available

## Solution: Generate a NEW Access Token

### Step 1: Go to Vimeo Developer Portal
1. Visit: https://developer.vimeo.com/
2. Log in with your Vimeo account
3. Click on your app (or create a new one if needed)

### Step 2: Get Your App Credentials
1. In your app's page, note down:
   - **Client ID** (App ID) - Should be visible on the app page
   - **Client Secret** - Click "Show" to reveal it

### Step 3: Generate a NEW Access Token (CRITICAL!)
1. In your Vimeo app, go to **"Authentication"** or **"Access Tokens"** section
2. Click **"Generate New Token"** or **"Generate Access Token"**
3. **IMPORTANT - Select these scopes (permissions):**
   - ✅ **`video.upload`** - **REQUIRED!** (This is the most important one)
   - ✅ `video.edit` - For editing video metadata
   - ✅ `video.delete` - Optional, for deleting videos
   - ✅ `public` - May be required
   - ✅ `private` - If you want private videos
4. Click **"Generate"** or **"Create Token"**
5. **Copy the ENTIRE token** - It should be a long string (128+ characters)

### Step 4: Update Your .env File
Open your `.env` file in the project root and update these three lines:

```env
VIMEO_CLIENT=your_actual_client_id_here
VIMEO_SECRET=your_actual_client_secret_here
VIMEO_ACCESS=your_new_long_access_token_here
```

**Important Notes:**
- Replace with your ACTUAL credentials from Step 2 and Step 3
- The access token should be LONG (128+ characters)
- Do NOT use quotes around the values
- Do NOT leave spaces around the `=` sign
- Make sure all three credentials are from the SAME Vimeo app

### Step 5: Clear Configuration Cache
After updating `.env`, run these commands:

```bash
php artisan config:clear
php artisan cache:clear
```

### Step 6: Verify the Fix
Run the test command again:

```bash
php artisan vimeo:test
```

You should see:
- ✅ Access Token: ✅ Set (128+ chars) - Should be much longer now
- ✅ Upload permissions verified!
- ✅ Upload quota information available

### Step 7: Try Uploading Again
After verifying, try uploading your video again. It should work now!

## Common Mistakes to Avoid

### ❌ Wrong: Using a short/invalid token
```env
VIMEO_ACCESS=abc123  # ❌ Too short! Should be 128+ characters
```

### ✅ Correct: Using a full access token
```env
VIMEO_ACCESS=v1.abc123def456ghi789jkl012mno345pqr678stu901vwx234yz567abc890def123ghi456jkl789mno012pqr345stu678vwx901yz234abc567def890ghi123jkl456mno789pqr012stu345vwx678yz901
```

### ❌ Wrong: Mixing credentials from different apps
```env
VIMEO_CLIENT=app1_client_id
VIMEO_SECRET=app1_client_secret
VIMEO_ACCESS=app2_access_token  # ❌ Different app!
```

### ✅ Correct: All from the same app
```env
VIMEO_CLIENT=app1_client_id
VIMEO_SECRET=app1_client_secret
VIMEO_ACCESS=app1_access_token  # ✅ Same app!
```

## Still Not Working?

1. **Double-check your .env file** - Make sure there are no typos, quotes, or extra spaces
2. **Verify the access token length** - Should be 128+ characters
3. **Make sure you selected "video.upload" scope** when generating the token
4. **Try generating a completely new token** - Don't reuse old ones
5. **Check Laravel logs**: `storage/logs/laravel.log` for detailed error information

## Need More Help?

Check the Laravel logs at `storage/logs/laravel.log` for detailed error information. The logs will show:
- Which credentials are being used
- Whether the API connection is successful
- The exact error from Vimeo API

