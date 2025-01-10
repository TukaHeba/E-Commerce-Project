<?php

namespace App\Services\Photo;

use Exception;
use App\Models\Photo\Photo;
use Illuminate\Support\Str;
use App\Traits\CacheManagerTrait;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PhotoService
{
    use CacheManagerTrait;
    private $groupe_key_cache = 'photos_cache_keys';
    /**
     * @var string The API key for VirusTotal.
     */
    protected $apiKey;

    /**
     * Constructor.
     * Retrieves the VirusTotal API key from the .env file.
     */
    public function __construct()
    {
        $this->apiKey = env('VIRUSTOTAL_API_KEY');
    }

    /**
     * Store a single photo after validating, scanning, and saving it.
     *
     * @param \Illuminate\Http\UploadedFile $photofile The photo file to upload.
     * @param mixed $photoable The related model (e.g., a user or post).
     * @return array Contains the created photo and a success message.
     * @throws Exception If the file is malicious or fails any validation.
     */
    public function storePhoto($photofile, $photoable)
    {
        $message = '';

        // Scan the file for viruses
        $scanResult = $this->scanFile($photofile);
        $maliciousCount = $scanResult['data']['attributes']['stats']['malicious'] ?? 0;
        if ($maliciousCount > 0) {
            throw new Exception('File contains a virus!', 400);
        } else {
            $message = 'Scan completed successfully, no virus found :)';
        }

        // Validate the file name and extension
        $originalName = $photofile->getClientOriginalName();
        $extension = $photofile->getClientOriginalExtension();

        // Disallow double extensions and path traversal attacks
        if (preg_match('/\.[^.]+\./', $originalName) || strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }

        // Validate MIME type
        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
        ];
        $mime_type = $photofile->getClientMimeType();
        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }

        // Generate a unique file name and save the file
        $fileName = Str::random(32) . '.' . $extension;
        $filePath = "photos/{$fileName}";

        if (!Storage::disk('local')->put($filePath, file_get_contents($photofile))) {
            throw new Exception(trans('general.failedToStoreFile'), 500);
        }

        // Save the photo in the database
        $photo = Photo::create([
            'photo_name' => $originalName,
            'photo_path' => $filePath,
            'mime_type' => $mime_type,
            'photoable_id' => $photoable->id,
            'photoable_type' => get_class($photoable),
        ]);
        $this->clearCacheGroup($this->groupe_key_cache);
        return ['photo' => $photo, 'message' => $message];
    }

    /**
     * Store multiple photos by calling storePhoto for each file.
     *
     * @param array $photoFiles An array of uploaded photo files.
     * @param mixed $photoable The related model (e.g., a user or post).
     * @return array Results of each photo upload, including errors if any.
     */
    public function storeMultiplePhotos(array $photoFiles, $photoable)
    {
        set_time_limit(120);
        $results = [];
        foreach ($photoFiles as $photofile) {
            try {
                $results[] = $this->storePhoto($photofile, $photoable);
            } catch (Exception $e) {
                $results[] = ['photo' => null, 'message' => $e->getMessage(), 'status' => 'error'];
            }
        }
        $this->clearCacheGroup($this->groupe_key_cache);
        return $results;
    }

    /**
     * Delete a photo from storage.
     *
     * @param string $filePath The path to the file in storage.
     * @throws Exception If the file does not exist.
     */
    public function deletePhoto($filePath)
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
            $this->clearCacheGroup($this->groupe_key_cache);
        } else {
            throw new Exception('File not found in storage', 404);
        }
    }

    /**
     * Scan a file for viruses using the VirusTotal API.
     *
     * @param \Illuminate\Http\UploadedFile $photofile The file to scan.
     * @return array The scan result from the VirusTotal API.
     * @throws Exception If the scan fails or the API returns an error.
     */
    public function scanFile($photofile)
    {
        $url = 'https://www.virustotal.com/api/v3/files';

        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->attach('file', fopen($photofile->getRealPath(), 'r'), $photofile->getClientOriginalName())->post($url);

        if ($response->successful()) {
            $analysisId = $response->json()['data']['id'];
            return $this->pollScanResult($analysisId);
        } else {
            Log::error('VirusTotal API error:', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);
            throw new Exception('Failed to scan file: ' . $response->body(), $response->status());
        }
    }

    /**
     * Poll VirusTotal for the scan result until it is completed.
     *
     * @param string $analysisId The ID of the scan analysis.
     * @return array The completed scan result.
     * @throws Exception If the scan times out or fails to complete.
     */
    public function pollScanResult($analysisId)
    {
        $url = "https://www.virustotal.com/api/v3/analyses/{$analysisId}";

        for ($i = 0; $i < 10; $i++) {
            sleep(10);

            $response = Http::withHeaders([
                'x-apikey' => $this->apiKey,
            ])->get($url);

            if ($response->successful() && $response->json()['data']['attributes']['status'] === 'completed') {
                return $response->json();
            }
        }
        throw new Exception('Scan timeout or failed to complete after polling.');
    }
}
