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
    /**
     * The PhotoService instance.
     *
     * @var PhotoService
     */
    protected PhotoService $PhotoService;

    /**
     * PhotoController constructor.
     *
     * @param PhotoService $PhotoService The service for handling photo operations.
     */
    public function __construct(PhotoService $PhotoService)
    {
        $this->PhotoService = $PhotoService;
    }

    /**
     * Store a photo dynamically based on the model.
     *
     * @param StorePhotoRequest $request The validated request containing photo data.
     * @param Model $model The associated model for the photo like (user , product , MainCategory , SubCategory)
     * @return JsonResponse The JSON response indicating success or failure.
     */
    public function storePhoto(StorePhotoRequest $request, Model $model): JsonResponse
    {
        // Store the photo using the PhotoService
        $photo = $this->PhotoService->storePhoto($request->file('photo'), $model);

        return self::success($photo, 'Photo uploaded successfully', 201);
    }

    /**
     * Remove a photo.
     *
     * @param Photo $photo The photo model instance to delete.
     * @return JsonResponse The JSON response indicating success or failure.
     */
    public function destroy(Photo $photo): JsonResponse
    {
        // Delete the photo from storage and database
        $this->PhotoService->deletePhoto($photo->photo_path);
        $photo->delete();

        return self::success(null, 'Photo deleted successfully');
    }
}
