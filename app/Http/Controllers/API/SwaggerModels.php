<?php

namespace App\Http\Controllers\API;

/**
 * @OA\Schema(
 *     schema="Audition",
 *     type="object",
 *     title="Audition",
 *     description="Audition model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="movie_id", type="integer", example=1),
 *     @OA\Property(property="role", type="string", example="Lead Actor"),
 *     @OA\Property(property="applicant_name", type="string", example="John Doe"),
 *     @OA\Property(property="uploaded_videos", type="array", 
 *         @OA\Items(type="string", example="http://example.com/storage/audition_videos/video.mp4")
 *     ),
 *     @OA\Property(property="old_video_backups", type="array", 
 *         @OA\Items(type="string", example="http://example.com/storage/audition_videos/old_video.mp4")
 *     ),
 *     @OA\Property(property="notes", type="string", example="Experienced actor with 5 years in the industry"),
 *     @OA\Property(property="status", type="string", enum={"pending", "viewed", "shortlisted", "rejected"}, example="pending"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-04T12:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Movie",
 *     type="object",
 *     title="Movie",
 *     description="Movie model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Inception"),
 *     @OA\Property(property="description", type="string", example="A thief who steals corporate secrets..."),
 *     @OA\Property(property="genre", type="array", 
 *         @OA\Items(type="string", example="Sci-Fi")
 *     ),
 *     @OA\Property(property="end_date", type="string", format="date", example="2025-12-31"),
 *     @OA\Property(property="director", type="string", example="Christopher Nolan"),
 *     @OA\Property(property="budget", type="number", example=160000000),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive", "upcoming"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-04T12:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="MovieRole",
 *     type="object",
 *     title="Movie Role",
 *     description="Movie Role model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="movie_id", type="integer", example=1),
 *     @OA\Property(property="role_type", type="string", example="Lead Actor"),
 *     @OA\Property(property="gender", type="string", example="Male"),
 *     @OA\Property(property="age_range", type="string", example="25-35"),
 *     @OA\Property(property="dialogue_sample", type="string", example="Sample dialogue..."),
 *     @OA\Property(property="status", type="string", enum={"open", "closed"}, example="open"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="deleted_at", type="string", format="date-time", example="2025-12-04T12:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role_id", type="integer", example=1),
 *     @OA\Property(property="phone", type="string", example="+1234567890"),
 *     @OA\Property(property="address", type="string", example="123 Main St"),
 *     @OA\Property(property="status", type="string", enum={"active", "inactive"}, example="active"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-04T12:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     title="Role",
 *     description="Role model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="Casting Director"),
 *     @OA\Property(property="permissions", type="array", 
 *         @OA\Items(type="string", example="manage_movies")
 *     ),
 *     @OA\Property(property="description", type="string", example="Can manage movies and auditions"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-04T12:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="SystemSetting",
 *     type="object",
 *     title="System Setting",
 *     description="System Setting model",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="site_name", type="string", example="Movie Auditions"),
 *     @OA\Property(property="site_description", type="string", example="Platform for movie auditions"),
 *     @OA\Property(property="contact_email", type="string", format="email", example="admin@example.com"),
 *     @OA\Property(property="casting_director_payment_required", type="boolean", example=true),
 *     @OA\Property(property="audition_user_payment_required", type="boolean", example=true),
 *     @OA\Property(property="video_upload_limit_mb", type="integer", example=100),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-12-04T12:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-12-04T12:00:00Z")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Error",
 *     type="object",
 *     title="Error Response",
 *     description="Error response model",
 *     @OA\Property(property="success", type="boolean", example=false),
 *     @OA\Property(property="message", type="string", example="Error message"),
 *     @OA\Property(property="data", type="object", nullable=true)
 * )
 */

/**
 * @OA\Schema(
 *     schema="Success",
 *     type="object",
 *     title="Success Response",
 *     description="Success response model",
 *     @OA\Property(property="success", type="boolean", example=true),
 *     @OA\Property(property="data", type="object"),
 *     @OA\Property(property="message", type="string", example="Success message")
 * )
 */
class SwaggerModels
{
    // This class is only used for Swagger documentation purposes
}