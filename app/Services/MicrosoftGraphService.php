<?php

namespace App\Services;

use App\Models\Classe;
use App\Models\Events;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MicrosoftGraphService
{
    public function refreshAccessToken(User $user)
    {
        try {
            $refreshToken = $user->refresh_token;

            if (!$refreshToken) {
                throw new \Exception('No refresh token found in session.');
            }

            $response = Http::asForm()->post('https://login.microsoftonline.com/common/oauth2/v2.0/token', [
                'client_id' => config('services.microsoft.client_id'),
                'client_secret' => config('services.microsoft.client_secret'),
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
            ]);

            if ($response->failed()) {
                Log::error('Failed to refresh access token', ['response' => $response->json()]);
                throw new \Exception('Failed to refresh access token.');
            }

            $newAccessToken = $response->json('access_token');
            $newExpiryTime = now()->addSeconds($response->json('expires_in'));

            $user->update([
                'token_expires_at' => $newExpiryTime,
            ]);

            session(['access_token' => $newAccessToken]);

            return $newAccessToken;
        } catch (\Exception $e) {
            Log::error('Error refreshing access token: ' . $e->getMessage());
            throw $e;
        }
    }
    public function createCalendarEventWithTeamsMeeting(Classe|Events $model, array $attendees)
    {
        try {
            $accessToken = $this->refreshAccessToken(Auth::user());

            $userTimezone = Auth::user()->timezone;
            $startTime = Carbon::parse($model->class_date . ' ' . $model->class_time, 'UTC')->setTimezone($userTimezone);
            $endTime = $startTime->copy()->addHours(1);

            $eventData = [
                'subject' => $model->title,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $model->description ?? 'No description provided.',
                ],
                'start' => [
                    'dateTime' => $model->class_date . 'T' . $model->class_time,
                    'timeZone' => $userTimezone,
                ],
                'end' => [
                    'dateTime' => $model->class_date . 'T' . $model->class_time,
                    'timeZone' => $userTimezone,
                ],
                'location' => [
                    'displayName' => 'Online',
                ],
                'attendees' => $this->formatAttendees($attendees),
                'allowNewTimeProposals' => true,
                'isOnlineMeeting' => true,
                'onlineMeetingProvider' => 'teamsForBusiness',
            ];

            $response = Http::withToken($accessToken)->post('https://graph.microsoft.com/v1.0/me/events', $eventData);
            // dd($response);
            if ($response->failed()) {
                dd($response->body());
            }


            $meetingLink = $response->json('onlineMeeting.joinUrl');
            $microsoft_event_id = $response->json('id');
            $model->update([
                'microsoft_event_id' => $microsoft_event_id,
                'teams_link' => $meetingLink,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error creating calendar event with Teams meeting: ' . $e->getMessage());
            throw $e;
            return false;
        }
    }

    private function formatAttendees(array $attendees)
    {
        return array_map(function ($attendee) {
            return [
                'emailAddress' => [
                    'address' => $attendee['email'],
                    'name' => $attendee['name'],
                ],
                'type' => 'optional',
            ];
        }, $attendees);
    }

    public function createCalendarEvent(Classe|Events $model)
    {
        try {
            $accessToken = $this->refreshAccessToken(Auth::user());
            $userTimezone = Auth::user()->timezone ?? 'UTC';
            $durationInHours = 1;
            $startDateTime = \Carbon\Carbon::createFromFormat('Y-m-d\TH:i', $model->class_date . 'T' . $model->class_time, 'America/New_York');
            $endDateTime = $startDateTime->copy()->addHours($durationInHours);

            $eventData = [
                'subject' => $model->title,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $model->description ?? 'No description provided.',
                ],
                'start' => [
                    'dateTime' => $startDateTime->toIso8601String(),
                    'timeZone' => $userTimezone,
                ],
                'end' => [
                    'dateTime' => $endDateTime->toIso8601String(),
                    'timeZone' => $userTimezone,
                ],
                'location' => [
                    'displayName' => $model->onsite_address,
                ],
            ];

            $response = Http::withToken($accessToken)->post('https://graph.microsoft.com/v1.0/me/events', $eventData);

            if ($response->failed()) {
                dd($response->body());
            }

            $microsoft_event_id = $response->json('id');
            $model->update([
                'microsoft_event_id' => $microsoft_event_id,
            ]);

            // dd($response);



            return true;
        } catch (\Exception $e) {
            Log::error('Error creating calendar event: ' . $e->getMessage());
            throw $e;
            return false;
        }
    }
    public function addDynamicAttendees(Classe|Events $model, array $attendees, $organizerId)
    {
        try {
            $accessToken = $this->getAccessTokenWithPermissions();

            if (!$model->microsoft_event_id) {
                throw new \Exception('Microsoft Event ID not found for the class.');
            }

            $attendeesData = $this->formatAttendees($attendees);

            $updateResponse = Http::withToken($accessToken)
                ->patch('https://graph.microsoft.com/v1.0/users/' . $organizerId . '/events/' . $model->microsoft_event_id . '?sendUpdates=all', [
                    'attendees' => $attendeesData,
                ]);

            if ($updateResponse->failed()) {
                Log::error('Error updating event: ' . $updateResponse->body());
                throw new \Exception('Error updating event: ' . $updateResponse->body());
            }

            Log::info('Event updated successfully with new attendees', ['response' => $updateResponse->json()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error adding dynamic attendees: ' . $e->getMessage());
            throw $e;
        }
    }
    private function getAccessTokenWithPermissions()
    {
        $tenantId = env('AZURE_TENANT_ID');

        $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => env('AZURE_CLIENT_ID'),
            'client_secret' => env('AZURE_CLIENT_SECRET'),
            'grant_type' => 'client_credentials',
            'scope' => 'https://graph.microsoft.com/.default',
        ]);

        if ($tokenResponse->failed()) {
            Log::error('Failed to retrieve access token: ' . $tokenResponse->body());
            throw new \Exception('Failed to retrieve access token.');
        }

        Log::info('Access token retrieved successfully', ['response' => $tokenResponse->json()]);

        return $tokenResponse->json('access_token');
    }

    public function createUserCalendarEvent($title, $description, $startDateTime, $endDateTime, $location, $attendees)
    {
        try {
            $accessToken = $this->refreshAccessToken(Auth::user());

            $userTimezone = Auth::user()->timezone ?? 'UTC';

            $eventData = [
                'subject' => $title,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $description ?? 'No description provided.',
                ],
                'start' => [
                    'dateTime' => Carbon::parse($startDateTime, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                    'timeZone' => $userTimezone,
                ],
                'end' => [
                    'dateTime' => Carbon::parse($endDateTime, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                    'timeZone' => $userTimezone,
                ],
                'location' => [
                    'displayName' => $location,
                ],
                'attendees' => $this->formatAttendees($attendees),
                'allowNewTimeProposals' => true,
                'isOnlineMeeting' => false,
            ];

            $response = Http::withToken($accessToken)->post('https://graph.microsoft.com/v1.0/me/events', $eventData);

            if ($response->failed()) {
                Log::error('Error creating calendar event: ' . $response->body());
                throw new \Exception('Error creating calendar event: ' . $response->body());
            }

            Log::info('Calendar event created successfully', ['response' => $response->json()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error creating calendar event: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateCalendarEvent($eventId, $title, $description, $startDateTime, $endDateTime, $location, $attendees, $isOnlineMeeting = false)
    {
        try {
            $accessToken = $this->refreshAccessToken(Auth::user());

            $userTimezone = Auth::user()->timezone ?? 'UTC';

            // Retrieve existing attendees
            $existingEventResponse = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/me/events/' . $eventId);
            if ($existingEventResponse->failed()) {
                Log::error('Error retrieving existing event: ' . $existingEventResponse->body());
                throw new \Exception('Error retrieving existing event: ' . $existingEventResponse->body());
            }
            $existingEvent = $existingEventResponse->json();
            $existingAttendees = $existingEvent['attendees'] ?? [];

            $eventData = [
                'subject' => $title,
                'body' => [
                    'contentType' => 'HTML',
                    'content' => $description ?? 'No description provided.',
                ],
                'start' => [
                    'dateTime' => Carbon::parse($startDateTime, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                    'timeZone' => $userTimezone,
                ],
                'end' => [
                    'dateTime' => Carbon::parse($endDateTime, 'UTC')->setTimezone($userTimezone)->toIso8601String(),
                    'timeZone' => $userTimezone,
                ],
                'location' => [
                    'displayName' => $location,
                ],
                'attendees' => $existingAttendees,
                'allowNewTimeProposals' => false,
            ];

            if ($isOnlineMeeting) {
                $eventData['isOnlineMeeting'] = true;
                $eventData['onlineMeetingProvider'] = 'teamsForBusiness';
            }

            $response = Http::withToken($accessToken)->patch('https://graph.microsoft.com/v1.0/me/events/' . $eventId . '?sendUpdates=all', $eventData);

            if ($response->failed()) {
                Log::error('Error updating calendar event: ' . $response->body());
                throw new \Exception('Error updating calendar event: ' . $response->body());
            }

            Log::info('Calendar event updated successfully', ['response' => $response->json()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating calendar event: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateDynamicAttendees(Classe|Events $model, array $attendees, $organizerId)
    {
        try {
            $accessToken = $this->getAccessTokenWithPermissions();

            if (!$model->microsoft_event_id) {
                throw new \Exception('Microsoft Event ID not found for the class.');
            }

            $attendeesData = $this->formatAttendees($attendees);

            $updateResponse = Http::withToken($accessToken)
                ->patch('https://graph.microsoft.com/v1.0/users/' . $organizerId . '/events/' . $model->microsoft_event_id . '?sendUpdates=all', [
                    'attendees' => $attendeesData,
                ]);

            if ($updateResponse->failed()) {
                Log::error('Error updating event: ' . $updateResponse->body());
                throw new \Exception('Error updating event: ' . $updateResponse->body());
            }

            Log::info('Event updated successfully with new attendees', ['response' => $updateResponse->json()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error updating dynamic attendees: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteDynamicAttendees(Classe|Events $model, array $attendees, $organizerId)
    {
        try {
            $accessToken = $this->getAccessTokenWithPermissions();

            if (!$model->microsoft_event_id) {
                throw new \Exception('Microsoft Event ID not found for the class.');
            }

            $attendeesData = $this->formatAttendees($attendees);

            $updateResponse = Http::withToken($accessToken)
                ->patch('https://graph.microsoft.com/v1.0/users/' . $organizerId . '/events/' . $model->microsoft_event_id . '?sendUpdates=all', [
                    'attendees' => $attendeesData,
                ]);

            if ($updateResponse->failed()) {
                Log::error('Error deleting event attendees: ' . $updateResponse->body());
                throw new \Exception('Error deleting event attendees: ' . $updateResponse->body());
            }

            Log::info('Event attendees deleted successfully', ['response' => $updateResponse->json()]);

            return true;
        } catch (\Exception $e) {
            Log::error('Error deleting dynamic attendees: ' . $e->getMessage());
            throw $e;
        }
    }


}
