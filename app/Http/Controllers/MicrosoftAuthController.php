<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class MicrosoftAuthController extends Controller
{
    public function redirectToMicrosoft()
    {
        $clientId = env('AZURE_CLIENT_ID');
        $redirectUri = env('AZURE_REDIRECT_URI');
        $scopes = 'openid profile email offline_access User.read OnlineMeetings.ReadWrite Calendars.ReadWrite';
        $state = csrf_token();

        $url = "https://login.microsoftonline.com/common/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => $scopes,
            'state' => $state,
        ]);

        return redirect($url);
    }

   public function callback(Request $request)
    {
    Log::info('Microsoft callback triggered', ['request' => $request->all()]);

    if ($request->has('error')) {
        return redirect()->route('register')->withErrors(['msg' => 'Microsoft login failed.']);
    }

    $code = $request->input('code');
    Log::info('Code received: ' . $code);

    //retrieving the access token
    $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/common/oauth2/v2.0/token", [
        'client_id' => env('AZURE_CLIENT_ID'),
        'client_secret' => env('AZURE_CLIENT_SECRET'),
        'grant_type' => 'authorization_code',
        'code' => $code,
        'redirect_uri' => env('AZURE_REDIRECT_URI'),
    ]);

    if ($tokenResponse->failed()) {
        return redirect()->route('register')->withErrors(['msg' => 'Failed to retrieve access token.']);
    }

    $accessToken = $tokenResponse->json('access_token');
    $refreshToken = $tokenResponse->json('refresh_token');
    $tokenExpiresAt = now()->addSeconds($tokenResponse->json('expires_in'));

    //fetching ms user profile data
    $userResponse = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/me');

    if ($userResponse->failed()) {
        return redirect()->route('register')->withErrors(['msg' => 'Failed to fetch Microsoft user information.']);
    }

    $microsoftUser = $userResponse->json();


    if (Auth::check()) {
        $user = Auth::user();

        // checking if the ms account is already linked to a different user
        $existingUser = User::where('microsoft_id', $microsoftUser['id'])
            ->first();

        if ($existingUser) {
            return redirect()->route('dashboard.settings')->withErrors([
                'msg' => 'This Microsoft account is already connected to another user.',
            ]);
        }

        // update the current user's info
        $user->update([
            'microsoft_id' => $microsoftUser['id'],
            'microsoft_account' => true,
            'refresh_token' => $refreshToken,
            'token_expires_at' => $tokenExpiresAt,
        ]);

        return redirect()->route('dashboard.settings')->with('success', 'Microsoft account connected successfully.');
    }

    /*if the user is not logged in, this is a new user or user login attempt.
     checking if the user with the same Microsoft ID or email already exists */
    $user = User::where('microsoft_id', $microsoftUser['id'])->orWhere('email', $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'])->first();

    if ($user) {
        // Login the existing user if found
        Auth::login($user);
        return redirect()->route('dashboard.settings');
    }

    // If no user exists, create a new user
    $user = User::create([
        'email' => $microsoftUser['mail'] ?? $microsoftUser['userPrincipalName'],
        'microsoft_id' => $microsoftUser['id'],
        'microsoft_account' => true,
        'password' => bcrypt(Str::random(16)),  // For local login setup
        'role_id' => 3,  // Ensure a role is assigned if needed
        'refresh_token' => $refreshToken,
        'token_expires_at' => $tokenExpiresAt,
    ]);

    // Login the newly created user
    Auth::login($user);

    return redirect()->route('dashboard.settings');
    }

}
