<?php

namespace App\Jobs;

use App\Models\Lecture;
use App\Models\ModelsLecture;
use Vimeo\Laravel\Facades\Vimeo;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class FetchVimeoVideoDetailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // Declare the variables as class properties
    protected $lectureId;
    protected $videoId;

    /**
     * Create a new job instance.
     *
     * @param int $lectureId
     * @param string $videoId
     */
    public function __construct($lectureId, $videoId)
    {
        // Initialize the variables
        $this->lectureId = $lectureId;
        $this->videoId = $videoId;
        // dd($this->videoId);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Log::info("Starting FetchVimeoVideoDetailsJob for Lecture ID: {$this->lectureId}");
        // dd($this->lectureId);

        try {
            for ($i = 0; $i < 10; $i++) {
                sleep(10);
                $videoDetails = Vimeo::request($this->videoId);
                // dd($videoDetails);
                Log::info("Attempt " . ($i + 1) . ": Video ID {$this->videoId} details fetched", $videoDetails);

                if (isset($videoDetails['body']['status']) && $videoDetails['body']['status'] === 'available') {
                    $duration = $videoDetails['body']['duration'];

                    Lecture::where('id', $this->lectureId)->update(['video_duration' => $duration]);
                    Log::info("Lecture ID {$this->lectureId} updated with video duration: {$duration}");
                    return;
                }
            }
            Log::warning("Video ID {$this->videoId} did not become available after 10 attempts.");
        } catch (\Exception $e) {
            Log::error("Error in FetchVimeoVideoDetailsJob: " . $e->getMessage());
        }
    }
}
