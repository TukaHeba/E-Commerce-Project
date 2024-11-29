<?php

namespace App\Http\Controllers\Photo;

use App\Http\Requests\Photo\StorePhotoRequest;
use App\Http\Requests\Photo\UpdatePhotoRequest;
use App\Models\Photo\Photo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PhotoController extends Controller
{
  
    protected PhotoService $PhotoService;

    public function __construct(PhotoService $PhotoService)
    {
        $this->PhotoService = $PhotoService;
    }

    /**
     * Display a listing of the resource.
     * @throws \Exception
     */
    public function index(Request $request): JsonResponse
    {
        $photos = $this->PhotoService->getPhotos($request);
        return self::paginated($photos, 'Photos retrieved successfully', 200);
    }

    /**
     * Store a newly created resource in storage.
     * @throws \Exception
     */
    public function store(StorePhotoRequest $request): JsonResponse
    {
        $photo = $this->PhotoService->storePhoto($request->validated());
        return self::success($photo, 'Photo created successfully', 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Photo $photo): JsonResponse
    {
        return self::success($photo, 'Photo retrieved successfully');
    }

    /**
     * Update the specified resource in storage.
     * @throws \Exception
     */
    public function update(UpdatePhotoRequest $request, Photo $photo): JsonResponse
    {
        $updatedPhoto = $this->PhotoService->updatePhoto($photo, $request->validated());
        return self::success($updatedPhoto, 'Photo updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Photo $photo): JsonResponse
    {
        $photo->delete();
        return self::success(null, 'Photo deleted successfully');
    }

    /**
     * Display soft-deleted records.
     */
    public function showDeleted(): JsonResponse
    {
        $photos = Photo::onlyTrashed()->get();
        return self::success($photos, 'Photos retrieved successfully');
    }

    /**
     * Restore a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function restoreDeleted(string $id): JsonResponse
    {
        $photo = Photo::onlyTrashed()->findOrFail($id);
        $photo->restore();
        return self::success($photo, 'Photo restored successfully');
    }

    /**
     * Permanently delete a soft-deleted record.
     * @param string $id
     * @return JsonResponse
     */
    public function forceDeleted(string $id): JsonResponse
    {
        $photo = Photo::onlyTrashed()->findOrFail($id)->forceDelete();
        return self::success(null, 'Photo force deleted successfully');
    }
}
