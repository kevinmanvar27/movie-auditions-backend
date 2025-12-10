<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Movie Auditions API",
 *      description="API Documentation for Movie Auditions Backend",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      ),
 *      @OA\License(
 *          name="Apache 2.0",
 *          url="http://www.apache.org/licenses/LICENSE-2.0.html"
 *      )
 * )
 * 
 * @OA\Server(
 *      url=L5_SWAGGER_CONST_HOST,
 *      description="Movie Auditions API Server"
 * )
 * 
 * @OA\SecurityScheme(
 *      securityScheme="bearerAuth",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT"
 * )
 * 
 * @OA\Schema(
 *      schema="Audition",
 *      type="object",
 *      required={"id", "user_id", "movie_id", "role", "applicant_name", "status"},
 *      @OA\Property(property="id", type="integer", format="int64", example=1),
 *      @OA\Property(property="user_id", type="integer", format="int64", example=1),
 *      @OA\Property(property="movie_id", type="integer", format="int64", example=1),
 *      @OA\Property(property="role", type="string", example="Lead Actor"),
 *      @OA\Property(property="applicant_name", type="string", example="John Doe"),
 *      @OA\Property(property="uploaded_videos", type="array", @OA\Items(type="string")),
 *      @OA\Property(property="old_video_backups", type="array", @OA\Items(type="string")),
 *      @OA\Property(property="notes", type="string", example="Experienced actor with 5 years in the industry"),
 *      @OA\Property(property="status", type="string", example="pending"),
 *      @OA\Property(property="created_at", type="string", format="date-time"),
 *      @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 * 
 * @OA\Schema(
 *      schema="Movie",
 *      type="object",
 *      required={"id", "title", "director", "end_date", "genre", "status"},
 *      @OA\Property(property="id", type="integer", format="int64", example=1),
 *      @OA\Property(property="title", type="string", example="Inception"),
 *      @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
 *      @OA\Property(property="director", type="string", example="Christopher Nolan"),
 *      @OA\Property(property="end_date", type="string", format="date", example="2025-12-31"),
 *      @OA\Property(property="genre", type="array", @OA\Items(type="string")),
 *      @OA\Property(property="budget", type="number", format="float", example=160000000.00),
 *      @OA\Property(property="status", type="string", example="active"),
 *      @OA\Property(property="created_at", type="string", format="date-time"),
 *      @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class BaseAPIController extends Controller
{
    /**
     * Success response method.
     *
     * @param mixed $result
     * @param string $message
     * @param int $code
     * @return JsonResponse
     */
    public function sendResponse($result, $message, $code = 200)
    {
        $response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];

        return response()->json($response, $code);
    }

    /**
     * Return error response.
     *
     * @param string $error
     * @param array $errorMessages
     * @param int $code
     * @return JsonResponse
     */
    public function sendError($error, $errorMessages = [], $code = 404)
    {
        $response = [
            'success' => false,
            'message' => $error,
        ];

        if (!empty($errorMessages)) {
            $response['data'] = $errorMessages;
        }

        return response()->json($response, $code);
    }
}