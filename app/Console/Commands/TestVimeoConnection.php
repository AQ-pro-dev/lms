<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Vimeo\Laravel\Facades\Vimeo;

class TestVimeoConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vimeo:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test Vimeo API connection and verify upload permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing Vimeo API Connection...');
        $this->newLine();

        // Check configuration
        $clientId = config('vimeo.connections.main.client_id');
        $clientSecret = config('vimeo.connections.main.client_secret');
        $accessToken = config('vimeo.connections.main.access_token');

        $this->info('Configuration Check:');
        $this->line('  Client ID: ' . ($clientId && $clientId !== 'your-client-id' ? '✅ Set (' . strlen($clientId) . ' chars)' : '❌ Not set or invalid'));
        $this->line('  Client Secret: ' . ($clientSecret && $clientSecret !== 'your-client-secret' ? '✅ Set (' . strlen($clientSecret) . ' chars)' : '❌ Not set or invalid'));
        $this->line('  Access Token: ' . ($accessToken ? '✅ Set (' . strlen($accessToken) . ' chars)' : '❌ Not set'));
        $this->newLine();

        if (!$clientId || $clientId === 'your-client-id' || 
            !$clientSecret || $clientSecret === 'your-client-secret' || 
            !$accessToken) {
            $this->error('❌ Vimeo credentials are not properly configured!');
            $this->line('Please check your .env file and ensure VIMEO_CLIENT, VIMEO_SECRET, and VIMEO_ACCESS are set.');
            $this->line('Then run: php artisan config:clear');
            return 1;
        }

        // Test API connection
        $this->info('Testing API Connection...');
        try {
            $response = Vimeo::request('/me', [], 'GET');
            
            if (isset($response['body'])) {
                $this->info('✅ API Connection Successful!');
                $this->line('  Authenticated as: ' . ($response['body']['name'] ?? 'Unknown'));
                $this->line('  User URI: ' . ($response['body']['uri'] ?? 'Unknown'));
                $this->line('  Account Type: ' . ($response['body']['account'] ?? 'Unknown'));
                $this->newLine();

                // Check upload quota
                if (isset($response['body']['upload_quota'])) {
                    $quota = $response['body']['upload_quota'];
                    $this->info('Upload Quota Information:');
                    if (isset($quota['space'])) {
                        $total = $quota['space']['total'] ?? 0;
                        $used = $quota['space']['used'] ?? 0;
                        $max = $quota['space']['max'] ?? 0;
                        
                        $this->line('  Total Space: ' . $this->formatBytes($total));
                        $this->line('  Used Space: ' . $this->formatBytes($used));
                        $this->line('  Max Space: ' . $this->formatBytes($max));
                        
                        if ($max > 0) {
                            $this->line('  ✅ Upload quota available');
                        } else {
                            $this->warn('  ⚠️  No upload quota - account may not have upload permissions');
                        }
                    }
                } else {
                    $this->warn('⚠️  Upload quota information not available - account may not have upload permissions');
                }

                // Check if we can access upload endpoint
                $this->newLine();
                $this->info('Testing Upload Permissions...');
                try {
                    // Try to get upload ticket (this tests upload permissions without actually uploading)
                    $uploadResponse = Vimeo::request('/me/videos', ['type' => 'upload', 'upgrade_to_1080' => false], 'POST');
                    
                    if (isset($uploadResponse['body']['upload']['upload_link'])) {
                        $this->info('✅ Upload permissions verified!');
                        $this->line('  Upload endpoint accessible');
                    } else {
                        $this->error('❌ Upload permissions test failed');
                        $this->line('  Response: ' . json_encode($uploadResponse['body'] ?? []));
                    }
                } catch (\Exception $uploadException) {
                    $this->error('❌ Upload permissions test failed: ' . $uploadException->getMessage());
                    if ($uploadException instanceof \Vimeo\Exceptions\VimeoRequestException) {
                        $body = $uploadException->getBody();
                        if (is_array($body)) {
                            $this->line('  Error: ' . ($body['error'] ?? 'Unknown'));
                            $this->line('  Description: ' . ($body['error_description'] ?? 'No description'));
                            $this->line('  Message: ' . ($body['message'] ?? 'No message'));
                        }
                    }
                    $this->newLine();
                    $this->warn('⚠️  This usually means:');
                    $this->line('  1. Your access token does not have the "video.upload" scope');
                    $this->line('  2. Your access token is from a different app than your Client ID/Secret');
                    $this->line('  3. Your Vimeo account does not have upload permissions');
                }

            } else {
                $this->error('❌ API Connection failed - Invalid response');
                return 1;
            }

        } catch (\Vimeo\Exceptions\VimeoRequestException $e) {
            $this->error('❌ Vimeo API Error: ' . $e->getMessage());
            $body = $e->getBody();
            if (is_array($body)) {
                $this->line('  Error: ' . ($body['error'] ?? 'Unknown'));
                $this->line('  Description: ' . ($body['error_description'] ?? 'No description'));
            }
            $this->newLine();
            $this->warn('This usually means your credentials don\'t match or are invalid.');
            return 1;
        } catch (\Exception $e) {
            $this->error('❌ Connection Error: ' . $e->getMessage());
            $this->line('  Class: ' . get_class($e));
            return 1;
        }

        $this->newLine();
        $this->info('✅ All tests completed!');
        return 0;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }
}
