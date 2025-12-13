<?php

namespace App\Http\Controllers\API;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends BaseAPIController
{
    /**
     * @OA\Get(
     *      path="/api/v1/pages",
     *      operationId="getPublicPages",
     *      tags={"Pages"},
     *      summary="Get list of published pages",
     *      description="Returns list of published pages for public viewing",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation",
     *          @OA\JsonContent(
     *              @OA\Property(property="success", type="boolean", example=true),
     *              @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Page")),
     *              @OA\Property(property="message", type="string", example="Pages retrieved successfully")
     *          )
     *      )
     * )
     */
    public function index()
    {
        $pages = Page::published()
            ->select(['id', 'title', 'slug', 'meta_title', 'meta_description', 'order', 'show_in_menu', 'created_at', 'updated_at'])
            ->orderBy('order')
            ->get();

        return $this->sendResponse($pages, 'Pages retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/pages/menu",
     *      operationId="getMenuPages",
     *      tags={"Pages"},
     *      summary="Get pages for menu",
     *      description="Returns list of published pages that should appear in menu",
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      )
     * )
     */
    public function menu()
    {
        $pages = Page::published()
            ->inMenu()
            ->select(['id', 'title', 'slug', 'order'])
            ->get();

        return $this->sendResponse($pages, 'Menu pages retrieved successfully');
    }

    /**
     * @OA\Get(
     *      path="/api/v1/pages/{slug}",
     *      operationId="getPageBySlug",
     *      tags={"Pages"},
     *      summary="Get page by slug",
     *      description="Returns a single published page by its slug",
     *      @OA\Parameter(
     *          name="slug",
     *          description="Page slug",
     *          required=true,
     *          in="path",
     *          @OA\Schema(type="string")
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful operation"
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="Page not found"
     *      )
     * )
     */
    public function show($slug)
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->first();

        if (!$page) {
            return $this->sendError('Page not found', [], 404);
        }

        return $this->sendResponse($page, 'Page retrieved successfully');
    }
}
