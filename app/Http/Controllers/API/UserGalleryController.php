<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController as Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="User Gallery",
 *     description="API Endpoints for User Image Gallery Management"
 * )
 */
class UserGalleryController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/users/{userId}/gallery",
     *      operationId="getUserGallery",
     *      tags={"User Gallery"},
     *      summary="Get user image gallery",
     *      description="Returns the image gallery for a specific user with full URLs",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="userId",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="gallery", type="array",
     *                      @OA\Items(type="string", format="uri")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Image gallery retrieved successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      )
     *     )
     *
     * Get user image gallery
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        
        // Return full URLs for images
        $gallery = $user->image_gallery ?? [];
        $galleryWithUrls = array_map(function($image) {
            return url('storage/' . $image);
        }, $gallery);
        
        return $this->sendResponse([
            'gallery' => $galleryWithUrls
        ], 'Image gallery retrieved successfully.');
    }
    
    /**
     * @OA\Post(
     *      path="/api/v1/users/{userId}/gallery",
     *      operationId="uploadUserGalleryImages",
     *      tags={"User Gallery"},
     *      summary="Upload images to user gallery",
     *      description="Upload one or more images to a user's gallery",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="userId",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  @OA\Property(
     *                      property="images",
     *                      type="array",
     *                      @OA\Items(type="string", format="binary")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="gallery", type="array",
     *                      @OA\Items(type="string", format="uri")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Images uploaded successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     *
     * Upload images to user gallery
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        if (!$request->hasFile('images')) {
            return $this->sendError('No images provided.', [], 400);
        }
        
        $gallery = $user->image_gallery ?? [];
        
        foreach ($request->file('images') as $image) {
            $path = $image->store('user_galleries/' . $userId, 'public');
            $gallery[] = $path;
        }
        
        $user->image_gallery = $gallery;
        $user->save();
        
        // Return full URLs for images
        $galleryWithUrls = array_map(function($image) {
            return url('storage/' . $image);
        }, $gallery);
        
        return $this->sendResponse([
            'gallery' => $galleryWithUrls
        ], 'Images uploaded successfully.');
    }
    
    /**
     * @OA\Put(
     *      path="/api/v1/users/{userId}/gallery",
     *      operationId="updateUserGallery",
     *      tags={"User Gallery"},
     *      summary="Update user image gallery",
     *      description="Update user image gallery (reorder or remove images)",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="userId",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"gallery"},
     *              @OA\Property(property="gallery", type="array",
     *                  @OA\Items(type="string")
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="gallery", type="array",
     *                      @OA\Items(type="string", format="uri")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Image gallery updated successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User not found"
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation Error"
     *      )
     *     )
     *
     * Update user image gallery (reorder/remove)
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $userId)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        
        $validator = Validator::make($request->all(), [
            'gallery' => 'required|array',
            'gallery.*' => 'string',
        ]);
        
        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }
        
        $user->image_gallery = $request->gallery;
        $user->save();
        
        // Return full URLs for images
        $galleryWithUrls = array_map(function($image) {
            return url('storage/' . $image);
        }, $request->gallery);
        
        return $this->sendResponse([
            'gallery' => $galleryWithUrls
        ], 'Image gallery updated successfully.');
    }
    
    /**
     * @OA\Delete(
     *      path="/api/v1/users/{userId}/gallery/{imagePath}",
     *      operationId="deleteUserGalleryImage",
     *      tags={"User Gallery"},
     *      summary="Remove an image from user gallery",
     *      description="Remove a specific image from a user's gallery",
     *      security={{"bearerAuth": {}}},
     *      @OA\Parameter(
     *          name="userId",
     *          description="User ID",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="integer")
     *      ),
     *      @OA\Parameter(
     *          name="imagePath",
     *          description="Image path",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="object",
     *                  @OA\Property(property="gallery", type="array",
     *                      @OA\Items(type="string", format="uri")
     *                  )
     *              ),
     *              @OA\Property(property="message", type="string", example="Image removed successfully.")
     *          )
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthenticated"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="User or image not found"
     *      )
     *     )
     *
     * Remove an image from user gallery
     * 
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $userId
     * @param  string  $imagePath
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $userId, $imagePath)
    {
        $user = User::find($userId);
        
        if (!$user) {
            return $this->sendError('User not found.', [], 404);
        }
        
        $gallery = $user->image_gallery ?? [];
        
        // Decode the image path (it might be URL encoded)
        $decodedImagePath = urldecode($imagePath);
        
        // Remove the image from the gallery
        $gallery = array_filter($gallery, function($image) use ($decodedImagePath) {
            return $image !== $decodedImagePath;
        });
        
        // Re-index array
        $gallery = array_values($gallery);
        
        // Delete the file from storage
        Storage::disk('public')->delete($decodedImagePath);
        
        $user->image_gallery = $gallery;
        $user->save();
        
        // Return full URLs for images
        $galleryWithUrls = array_map(function($image) {
            return url('storage/' . $image);
        }, $gallery);
        
        return $this->sendResponse([
            'gallery' => $galleryWithUrls
        ], 'Image removed successfully.');
    }
}
