<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use OpenApi\Annotations as OA; // تأكد من استيراد هذه

/**
 * @OA\Info(
 * version="1.0.0",
 * title="ELKOOD To-Do List API Documentation",
 * description="API documentation for the To-Do List application using Laravel and Sanctum.",
 * @OA\Contact(
 * email="info@elkood.com"
 * ),
 * @OA\License(
 * name="Apache 2.0",
 * url="http://www.apache.org/licenses/LICENSE-2.0.html"
 * )
 * )
 *
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="ELKOOD To-Do List API Server"
 * )
 *
 * @OA\SecurityScheme(
 * type="http",
 * description="Authentication token",
 * name="Authorization",
 * in="header",
 * scheme="bearer",
 * bearerFormat="JWT",
 * securityScheme="bearerAuth",
 * )
 *
 * @OA\Schema(
 * schema="User",
 * title="User",
 * description="User model",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="name", type="string", example="Owner User"),
 * @OA\Property(property="email", type="string", format="email", example="owner@example.com"),
 * @OA\Property(property="role", type="string", enum={"owner", "guest"}, example="owner")
 * )
 *
 * @OA\Schema(
 * schema="Category",
 * title="Category",
 * description="Category model",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="name", type="string", example="Work"),
 * @OA\Property(property="description", type="string", nullable=true, example="Work related tasks")
 * )
 *
 * @OA\Schema(
 * schema="Priority",
 * title="Priority",
 * description="Priority model",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="name", type="string", example="High"),
 * @OA\Property(property="level", type="integer", example=3)
 * )
 *
 * @OA\Schema(
 * schema="Task",
 * title="Task",
 * description="Task model",
 * @OA\Property(property="id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="user_id", type="integer", readOnly="true", example=1),
 * @OA\Property(property="title", type="string", example="Finish API Documentation"),
 * @OA\Property(property="description", type="string", nullable=true, example="Add Swagger annotations for all endpoints."),
 * @OA\Property(property="is_completed", type="boolean", example=false),
 * @OA\Property(property="due_date", type="string", format="date", nullable=true, example="2025-06-15"),
 * @OA\Property(property="priority_id", type="integer", nullable=true, example=2),
 * @OA\Property(property="category_id", type="integer", nullable=true, example=1),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly="true", example="2025-06-03T10:00:00Z"),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly="true", example="2025-06-03T10:00:00Z"),
 * @OA\Property(property="category", type="object", ref="#/components/schemas/Category"),
 * @OA\Property(property="priority", type="object", ref="#/components/schemas/Priority")
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;
}