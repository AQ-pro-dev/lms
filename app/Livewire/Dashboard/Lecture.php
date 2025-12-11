<?php

namespace App\Livewire\Dashboard;

use App\Jobs\FetchVimeoVideoDetailsJob;
use App\Models\Course;
use App\Models\Lecture as ModelsLecture;
use App\Models\Tutor;
use Vimeo\Laravel\Facades\Vimeo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class Lecture extends Component
{
    use LivewireAlert, WithFileUploads;

    public $courses = [];
    public $course_id; // Course selected once for all lectures
    public $lectures = [];
    public $alllectures;
    public $totalLectures = 0;
    public $usedOrders = [];

    protected $rules = [
        'course_id' => 'required|exists:courses,id',
        'lectures.*.title' => 'required|string|max:255', // Moved 'title' inside the lectures array
        'lectures.*.description' => 'nullable|string',
        'lectures.*.video_file' => 'required|file|max:102400|mimetypes:video/mp4,video/avi,video/mov',
        'lectures.*.order' => 'required|integer|distinct|min:1',
    ];

    public function mount()
    {
        $this->loadCourses();
        // Initialize with one lecture input block
        $this->initializeLectureBlock();
    }
    
    public function loadCourses()
    {
        $user = Auth::user();
        $roleId = $user->role_id;
        
        // Build query for recorded courses
        $query = Course::where('course_type', 'recorded');
        
        if ($roleId == 1) {
            // Admin: Show all published, non-drafted courses
            $query->where('is_published', true)
                  ->where('is_drafted', false);
        } elseif ($roleId == 2) {
            // Instructor: Show ALL courses they own OR are assigned to as tutors (regardless of published/draft status)
            // Get all course IDs where user is owner
            $ownedCourseIds = Course::where('user_id', $user->id)
                ->where('course_type', 'recorded')
                ->pluck('id')
                ->toArray();
            
            // Get all course IDs where user is assigned as tutor (and ensure they are recorded courses)
            $assignedCourseIds = DB::table('course_tutors')
                ->join('courses', 'course_tutors.course_id', '=', 'courses.id')
                ->where('course_tutors.user_id', $user->id)
                ->where('courses.course_type', 'recorded')
                ->pluck('courses.id')
                ->toArray();
            
            $allCourseIds = array_unique(array_merge($ownedCourseIds, $assignedCourseIds));
            
            if (!empty($allCourseIds)) {
                $query->whereIn('id', $allCourseIds);
            } else {
                // No courses found, return empty result
                $query->whereRaw('1 = 0');
            }
        } else {
            // Student: Should not access this page, but show nothing if they do
            $query->whereRaw('1 = 0'); // Always false
        }
        
        $this->courses = $query->orderBy('title')->get();
    }
    public function fetchLectures()
    {
        // Reload courses when course selection changes
        $this->loadCourses();
        $this->dispatch('refreshComponent');
    }
    public function updatedCourseId()
    {
        if ($this->course_id) {
            $this->alllectures = ModelsLecture::where('course_id', $this->course_id)->get();
            $this->totalLectures = $this->alllectures->count() + count($this->lectures);
            $this->usedOrders = $this->alllectures->pluck('order')->toArray();
        }
    }

    private function initializeLectureBlock()
    {
        $this->lectures[] = [
            'title' => '',
            'description' => '',
            'video_file' => '',
            'order' => '',
        ];
    }

    public function addLectureBlock()
    {
        $this->initializeLectureBlock();
        $this->totalLectures += 1;
        $this->dispatch('refreshComponent');
    }

    public function removeLectureBlock($index)
    {
        unset($this->lectures[$index]);
        $this->lectures = array_values($this->lectures); // Reindex the array
    }

    public function submit()
    {
        $this->validate();

        // Check for duplicate orders in the current lectures
        $orders = array_column($this->lectures, 'order');
        if (count($orders) !== count(array_unique($orders))) {
            $this->alert('error', 'Lecture order must be unique within the same course.');
            return;
        }
        
        // Verify Vimeo configuration
        $vimeoClient = config('vimeo.connections.main.client_id');
        $vimeoSecret = config('vimeo.connections.main.client_secret');
        $vimeoToken = config('vimeo.connections.main.access_token');
        
        // Check if credentials are set and not default values
        if (empty($vimeoClient) || $vimeoClient === 'your-client-id' || 
            empty($vimeoSecret) || $vimeoSecret === 'your-client-secret' || 
            empty($vimeoToken)) {
            $this->alert('error', 'Vimeo API credentials are not configured properly. Please check your .env file and ensure VIMEO_CLIENT, VIMEO_SECRET, and VIMEO_ACCESS are set.');
            Log::error('Vimeo credentials missing or invalid', [
                'client_id_set' => !empty($vimeoClient) && $vimeoClient !== 'your-client-id',
                'client_secret_set' => !empty($vimeoSecret) && $vimeoSecret !== 'your-client-secret',
                'access_token_set' => !empty($vimeoToken),
                'client_id_value' => substr($vimeoClient ?? '', 0, 10) . '...' // Log first 10 chars for debugging
            ]);
            return;
        }
        
        // Log that credentials are configured (without exposing secrets)
        Log::info('Vimeo credentials verified', [
            'client_id_length' => strlen($vimeoClient),
            'client_secret_length' => strlen($vimeoSecret),
            'access_token_length' => strlen($vimeoToken),
            'client_id_prefix' => substr($vimeoClient, 0, 10) . '...',
            'access_token_prefix' => substr($vimeoToken, 0, 15) . '...'
        ]);
        
        // IMPORTANT: Verify that access token matches the app
        // The access token MUST be generated for the SAME app as Client ID/Secret
        try {
            $meResponse = Vimeo::request('/me', [], 'GET');
            Log::info('Vimeo API connection verified', [
                'authenticated_user' => $meResponse['body']['name'] ?? 'Unknown',
                'user_uri' => $meResponse['body']['uri'] ?? 'Unknown',
                'account_type' => $meResponse['body']['account'] ?? 'Unknown'
            ]);
            
            // Check upload quota to verify upload capability
            if (!isset($meResponse['body']['upload_quota'])) {
                Log::warning('Vimeo account may not have upload capability', [
                    'user' => $meResponse['body']['name'] ?? 'Unknown'
                ]);
                $this->alert('warning', 'Your Vimeo account may not have upload permissions. Please verify your account type and access token scopes.');
            }
        } catch (\Vimeo\Exceptions\VimeoRequestException $e) {
            $errorBody = $e->getBody();
            $errorMsg = $e->getMessage();
            
            if (is_array($errorBody) && isset($errorBody['error'])) {
                $errorMsg = $errorBody['error'];
                if (isset($errorBody['error_description'])) {
                    $errorMsg .= ': ' . $errorBody['error_description'];
                }
            }
            
            Log::error('Vimeo API connection failed - credentials may not match', [
                'error' => $errorMsg,
                'error_body' => $errorBody,
                'status_code' => $e->getCode(),
                'hint' => 'Make sure Client ID, Client Secret, and Access Token are all from the SAME Vimeo app'
            ]);
            $this->alert('error', 'Vimeo API connection failed: ' . $errorMsg . '. Please verify that your Client ID, Client Secret, and Access Token all belong to the SAME Vimeo app and the access token has upload permissions.');
            return;
        } catch (\Exception $e) {
            Log::error('Vimeo API connection failed', [
                'error' => $e->getMessage(),
                'class' => get_class($e),
                'hint' => 'Make sure Client ID, Client Secret, and Access Token are all from the SAME Vimeo app'
            ]);
            $this->alert('error', 'Vimeo API connection failed: ' . $e->getMessage() . '. Please verify your credentials.');
            return;
        }

        // Proceed with the rest of the logic
        $uploadedCount = 0;
        $failedCount = 0;
        $errorMessages = []; // Store error messages to display
        
        foreach ($this->lectures as $index => $lecture) {
            try {
                $videoFile = $lecture['video_file'];
                
                // Ensure we have a valid file object
                if (!$videoFile || !$videoFile instanceof \Illuminate\Http\UploadedFile) {
                    $errorMsg = 'Invalid video file for lecture: ' . ($lecture['title'] ?? 'Unknown');
                    $errorMessages[] = $errorMsg;
                    $this->alert('error', $errorMsg);
                    $failedCount++;
                    continue;
                }
                
                // Get the file path - Livewire stores files temporarily
                $filePath = $videoFile->getRealPath();
                
                // If getRealPath() doesn't work, try the temporary path
                if (!$filePath || !file_exists($filePath)) {
                    // Try alternative path methods
                    $filePath = $videoFile->path();
                    if (!$filePath || !file_exists($filePath)) {
                        // Store temporarily to get a valid path
                        $tempPath = $videoFile->storeAs('temp', uniqid('lecture_') . '.' . $videoFile->getClientOriginalExtension(), 'local');
                        $filePath = Storage::disk('local')->path($tempPath);
                    }
                }
                
                // Verify file exists and is readable
                if (!file_exists($filePath) || !is_readable($filePath)) {
                    $errorMsg = 'Video file is not accessible for lecture: ' . ($lecture['title'] ?? 'Unknown');
                    $errorMessages[] = $errorMsg;
                    $this->alert('error', $errorMsg);
                    $failedCount++;
                    continue;
                }
                
                // Check file size (Vimeo has a 200GB limit, but we should warn for very large files)
                $fileSize = filesize($filePath);
                $maxSize = 200 * 1024 * 1024 * 1024; // 200 GB in bytes
                if ($fileSize > $maxSize) {
                    $errorMsg = 'Video file is too large (' . round($fileSize / 1024 / 1024 / 1024, 2) . ' GB). Maximum size is 200 GB.';
                    $errorMessages[] = $errorMsg;
                    $this->alert('error', $errorMsg);
                    $failedCount++;
                    continue;
                }
                
                // Prepare metadata for Vimeo upload - ensure proper format
                $metadata = [];
                if (!empty($lecture['title'])) {
                    $metadata['name'] = substr($lecture['title'], 0, 128); // Vimeo has a 128 char limit
                }
                if (!empty($lecture['description'])) {
                    $metadata['description'] = substr($lecture['description'], 0, 5000); // Vimeo has limits
                }
                
                // Log upload attempt for debugging
                Log::info('Attempting Vimeo upload', [
                    'file_path' => $filePath,
                    'file_size' => filesize($filePath),
                    'file_exists' => file_exists($filePath),
                    'has_metadata' => !empty($metadata),
                    'lecture_title' => $lecture['title'] ?? 'Unknown',
                    'client_id' => substr($vimeoClient, 0, 10) . '...',
                    'access_token_prefix' => substr($vimeoToken, 0, 10) . '...'
                ]);
                
                // Verify Vimeo connection before upload
                try {
                    // Test the connection by making a simple API call
                    $testResponse = Vimeo::request('/me', [], 'GET');
                    Log::info('Vimeo connection test successful', [
                        'user' => $testResponse['body']['name'] ?? 'Unknown',
                        'uri' => $testResponse['body']['uri'] ?? 'Unknown',
                        'account_type' => $testResponse['body']['account'] ?? 'Unknown'
                    ]);
                    
                    // Check if user has upload capability
                    if (isset($testResponse['body']['upload_quota'])) {
                        Log::info('Vimeo upload quota info', [
                            'space_total' => $testResponse['body']['upload_quota']['space']['total'] ?? 'Unknown',
                            'space_used' => $testResponse['body']['upload_quota']['space']['used'] ?? 'Unknown',
                            'space_max' => $testResponse['body']['upload_quota']['space']['max'] ?? 'Unknown'
                        ]);
                    }
                } catch (\Exception $testException) {
                    Log::warning('Vimeo connection test failed', [
                        'error' => $testException->getMessage(),
                        'trace' => $testException->getTraceAsString()
                    ]);
                    $errorMsg = 'Vimeo API connection test failed: ' . $testException->getMessage();
                    $errorMessages[] = $errorMsg;
                    $this->alert('error', $errorMsg);
                    $failedCount++;
                    continue;
                }
                
                // Upload video directly to Vimeo
                // Vimeo::upload expects: (file_path, metadata_array or null)
                // If metadata is empty, pass null instead of empty array
                try {
                    if (empty($metadata)) {
                        $response = Vimeo::upload($filePath);
                    } else {
                        $response = Vimeo::upload($filePath, $metadata);
                    }
                } catch (\Vimeo\Exceptions\VimeoRequestException $uploadException) {
                    // Re-throw to be caught by outer catch block
                    throw $uploadException;
                } catch (\Exception $uploadException) {
                    // Log and re-throw
                    Log::error('Vimeo upload exception', [
                        'error' => $uploadException->getMessage(),
                        'class' => get_class($uploadException)
                    ]);
                    throw $uploadException;
                }
                
                // Log successful upload response
                Log::info('Vimeo upload successful', [
                    'response' => $response,
                    'lecture_title' => $lecture['title'] ?? 'Unknown'
                ]);

                // Get the video ID or URL from Vimeo's response
                // Response format: /videos/{video_id} or full URL
                $videoId = $response;
                
                // Clean up temporary file if we created one
                if (isset($tempPath)) {
                    Storage::disk('local')->delete($tempPath);
                }

                // Create lecture record in the database (without duration initially)
                $lectureRecord = ModelsLecture::create([
                    'course_id' => $this->course_id,
                    'title' => $lecture['title'],
                    'description' => $lecture['description'],
                    'video_file' => $videoId, // Vimeo video ID/URI
                    'video_duration' => null, // Initially null
                    'order' => $lecture['order'],
                ]);

                // Dispatch a job to fetch video details later
                FetchVimeoVideoDetailsJob::dispatch($lectureRecord->id, $videoId);
                $uploadedCount++;
                
            } catch (\Vimeo\Exceptions\VimeoRequestException $e) {
                // Handle Vimeo-specific errors
                $errorBody = $e->getBody();
                $errorDetails = $e->getMessage();
                
                // Extract more details from the error body if available
                if (is_array($errorBody) && isset($errorBody['error'])) {
                    $errorDetails = $errorBody['error'];
                    if (isset($errorBody['error_description'])) {
                        $errorDetails .= ': ' . $errorBody['error_description'];
                    }
                } elseif (is_array($errorBody) && isset($errorBody['message'])) {
                    $errorDetails = $errorBody['message'];
                }
                
                // Provide helpful guidance for common errors
                $errorMessage = 'Vimeo API Error for "' . ($lecture['title'] ?? 'Unknown') . '": ' . $errorDetails;
                
                // Add helpful hint for permission errors
                if (stripos($errorDetails, "can't upload") !== false || 
                    stripos($errorDetails, "get in touch with the app's creator") !== false) {
                    $errorMessage .= ' | SOLUTION: Your access token must have the "video.upload" scope AND must be generated for the SAME Vimeo app as your Client ID/Secret. Make sure Client ID, Client Secret, and Access Token all belong to the same app.';
                }
                
                $errorMessages[] = $errorMessage;
                $this->alert('error', $errorMessage);
                
                Log::error('Vimeo API error', [
                    'lecture_title' => $lecture['title'] ?? 'Unknown',
                    'error' => $e->getMessage(),
                    'error_body' => $errorBody,
                    'status_code' => $e->getCode() ?? null,
                    'file_path' => $filePath ?? null,
                    'file_size' => isset($filePath) && file_exists($filePath) ? filesize($filePath) : null
                ]);
                $failedCount++;
                continue;
            } catch (\Exception $e) {
                // Handle other errors
                $errorMessage = 'Upload Error for "' . ($lecture['title'] ?? 'Unknown') . '": ' . $e->getMessage();
                $errorMessages[] = $errorMessage;
                $this->alert('error', $errorMessage);
                
                Log::error('Vimeo upload error', [
                    'lecture_title' => $lecture['title'] ?? 'Unknown',
                    'error' => $e->getMessage(),
                    'error_class' => get_class($e),
                    'file_path' => $filePath ?? null,
                    'file_exists' => isset($filePath) ? file_exists($filePath) : false,
                    'file_readable' => isset($filePath) && file_exists($filePath) ? is_readable($filePath) : false,
                    'trace' => $e->getTraceAsString()
                ]);
                $failedCount++;
                continue;
            }
        }
        
        // Provide feedback based on results
        if ($uploadedCount > 0) {
            $message = "Successfully uploaded {$uploadedCount} lecture(s) to Vimeo!";
            if ($failedCount > 0) {
                $message .= " {$failedCount} lecture(s) failed to upload.";
            }
            $this->alert('success', $message . ' Video details will update soon.');
            $this->resetFields();
        } else {
            // Show detailed error message with all collected errors
            if (!empty($errorMessages)) {
                // Show the first error message as the main alert
                $this->alert('error', $errorMessages[0]);
                
                // If there are multiple errors, show them all
                if (count($errorMessages) > 1) {
                    foreach (array_slice($errorMessages, 1) as $errorMsg) {
                        $this->alert('error', $errorMsg);
                    }
                }
            } else {
                // Fallback if no specific error messages were collected
                $errorDetails = "Failed to upload all {$failedCount} lecture(s). ";
                $errorDetails .= "Please check your Vimeo API credentials, file size (max 200GB), and video format (MP4, AVI, MOV). ";
                $errorDetails .= "Check the Laravel logs for more details: storage/logs/laravel.log";
                $this->alert('error', $errorDetails);
            }
        }
    }

    private function resetFields()
    {
        $this->course_id = '';
        $this->lectures = [];
        $this->initializeLectureBlock();
        $this->totalLectures = 0;
        $this->usedOrders = [];
        // Reload courses after successful submission
        $this->loadCourses();
    }
    public function updateorder()
    {
        $this->dispatch('refreshComponent');
    }
    public function render()
    {
        return view('livewire.dashboard.lecture')->layout('components.layouts.dashboard');
    }
}
