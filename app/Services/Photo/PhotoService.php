<?php

namespace App\Services\Photo;
use Exception;
use App\Models\Photo\Photo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\File\Exception\FileException;

class PhotoService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('VIRUSTOTAL_API_KEY'); // Add your API key to .env
    }

    /**
     * Create a new Photo with the provided data.
     *
     * @param array $data The validated data to create a Photo.
     * @return Photo|null The created Photo object on success, or null on failure.
     */
    public function storePhoto($photo, $phototableType, $phototableId)
    {
        $message = '';

        // Scan the file
        $scanResult = $this->scanFile($photo);
        
        // Check scan results for malicious content
        if (isset($scanResult['data']['attributes']['stats'])) {
            $maliciousCount = $scanResult['data']['attributes']['stats']['malicious'] ?? 0;
            if ($maliciousCount > 0) {
                throw new Exception('File contains a virus!', 400);
            }else {
                $message = 'Scan completed successfully, no virus found :)';
            }
        } 

        // Validate and store the photo
        $originalName = $photo->getClientOriginalName();
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);

        if (!$extension || strpos($originalName, '..') !== false || strpos($originalName, '/') !== false || strpos($originalName, '\\') !== false) {
            throw new Exception(trans('general.notAllowedAction'), 403);
        }

        $allowedMimeTypes = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp',
        ];

        $mime_type = $photo->getClientMimeType();
        if (!in_array($mime_type, $allowedMimeTypes)) {
            throw new FileException(trans('general.invalidFileType'), 403);
        }

        $fileName = Str::random(32);
        $filePath = "photo/{$fileName}.{$extension}";
        $fileContent = file_get_contents($photo);

        if ($fileContent === false || !Storage::disk('local')->put($filePath, $fileContent)) {
            throw new Exception(trans('general.failedToStoreFile'), 500);
        }

        $photo = Photo::create([
            'photo_name' => $originalName,
            'photo_path' => $filePath,
            'mime_type' => $mime_type,
            'photoable_id' => $phototableId,
            'photoable_type' => $phototableType,
        ]);

        return ['photo' => $photo, 'message' => $message];
    }
    

    public function deletePhoto($filePath)
    {
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        } else {
            throw new Exception('File not found in storage', 404);
        }
    }

    public function scanFile($filePath)
    {
        $url = 'https://www.virustotal.com/api/v3/files';

        // Upload the file to VirusTotal
        $response = Http::withHeaders([
            'x-apikey' => $this->apiKey,
        ])->attach('file', fopen($filePath, 'r'), basename($filePath))->post($url);

        // Check if the file was uploaded successfully
        if ($response->successful()) {
            // Extract the analysis ID from the response
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

    public function pollScanResult($analysisId)
    {
        $url = "https://www.virustotal.com/api/v3/analyses/{$analysisId}";
        $maxAttempts = 10;
        $attempt = 0;

        // Poll every 10 seconds for the result until the scan is complete
        do {
            sleep(10); // wait 10 seconds between polling

            $response = Http::withHeaders([
                'x-apikey' => $this->apiKey,
            ])->get($url);

            $scanResult = $response->json();

            // Check if the scan is completed
            if (isset($scanResult['data']['attributes']['status']) && $scanResult['data']['attributes']['status'] === 'completed') {
                return $scanResult;
            }

            $attempt++;
        } while ($attempt < $maxAttempts);

        throw new Exception('Scan timeout or failed to complete after polling.');
    }

}
