<?php

namespace App\Http\Controllers\Photo;

use App\Models\Photo\Photo;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use App\Services\Photo\PhotoService;
use App\Http\Requests\Photo\StorePhotoRequest;


class PhotoController extends Controller
{
  
    protected PhotoService $PhotoService;

    public function __construct(PhotoService $PhotoService)
    {
        $this->PhotoService = $PhotoService;
    }

    /**
     * Store a photo dynamically based on the model.
     */
    public function storePhoto(StorePhotoRequest $request, Model $model): JsonResponse
    {
        // Store the photo
        $photo = $this->PhotoService->storePhoto($request->file('photo'), get_class($model), $model->id);

        return self::success($photo, 'Photo uploaded successfully', 201);
    }

    /**
     * Remove a photo.
     */
    public function destroy(Photo $photo): JsonResponse
    {
        $this->PhotoService->deletePhoto($photo->photo_path);
        $photo->delete();

        return self::success(null, 'Photo deleted successfully');
    }

}
