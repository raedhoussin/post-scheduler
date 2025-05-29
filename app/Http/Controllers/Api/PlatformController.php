<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Platform\PlatformRepositoryInterface;



class PlatformController extends Controller
{
    protected PlatformRepositoryInterface $platformRepository;

   
    public function __construct(PlatformRepositoryInterface $platformRepository)
    {
        $this->middleware('auth:sanctum');
        $this->platformRepository = $platformRepository;
    }
   
    
      /**
     * @OA\Get(
     *     path="/api/platforms",
     *     summary="Get list of all platforms",
     *     tags={"Platforms"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(response=200, description="List of platforms",
     *         @OA\JsonContent(type="array",
     *             @OA\Items(ref="#/components/schemas/Platform")
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $platforms = $this->platformRepository->allWithUserStatus($request->user());
        return response()->json($platforms);


    }
    

   
     /**
     * @OA\Post(
     *     path="/api/platforms",
     *     summary="Create a new platform",
     *     tags={"Platforms"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","type"},
     *             @OA\Property(property="name", type="string", example="Twitter"),
     *             @OA\Property(property="type", type="string", example="social")
     *         )
     *     ),
     *     @OA\Response(response=201, description="Platform created",
     *         @OA\JsonContent(ref="#/components/schemas/Platform")
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:platforms,name',
            'type' => 'required|string',
        ]);

        $platform = $this->platformRepository->create($request->only(['name', 'type']));
        return response()->json($platform, 201);
    }

  /**
     * @OA\Get(
     *     path="/api/platforms/{id}",
     *     summary="Get a platform by ID",
     *     tags={"Platforms"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, description="Platform ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Platform data",
     *         @OA\JsonContent(ref="#/components/schemas/Platform")
     *     ),
     *     @OA\Response(response=404, description="Platform not found")
     * )
     */
    public function show($id)
    {
        $platform = $this->platformRepository->find($id);
        if (!$platform) {
            return response()->json(['message' => 'Platform not found'], 404);
        }
        return response()->json($platform);
    }

   /**
     * @OA\Put(
     *     path="/api/platforms/{id}",
     *     summary="Update a platform",
     *     tags={"Platforms"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, description="Platform ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="Twitter"),
     *             @OA\Property(property="type", type="string", example="social")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Platform updated",
     *         @OA\JsonContent(ref="#/components/schemas/Platform")
     *     ),
     *     @OA\Response(response=404, description="Platform not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
 
     public function update(Request $request, $id)
     {
         $platform = $this->platformRepository->find($id);
         if (!$platform) {
             return response()->json(['message' => 'Platform not found'], 404);
         }
 
         $request->validate([
             'name' => 'sometimes|required|string|unique:platforms,name,' . $platform->id,
             'type' => 'sometimes|required|string',
         ]);
 
         $updatedPlatform = $this->platformRepository->update($id, $request->only(['name', 'type']));
         return response()->json($updatedPlatform);
     }

    /**
     * @OA\Delete(
     *     path="/api/platforms/{id}",
     *     summary="Delete a platform",
     *     tags={"Platforms"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="id", in="path", required=true, description="Platform ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Platform deleted"),
     *     @OA\Response(response=404, description="Platform not found")
     * )
     */
    public function destroy($id)
    {
        $deleted = $this->platformRepository->delete($id);
        if (!$deleted) {
            return response()->json(['message' => 'Platform not found'], 404);
        }
        return response()->json(['message' => 'Platform deleted']);
    }

/**
     * @OA\Post(
     *     path="/api/platforms/toggle",
     *     summary="Enable or disable platform for authenticated user",
     *     tags={"Platforms"},
     *     security={{"sanctum": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"platform_id","enabled"},
     *             @OA\Property(property="platform_id", type="integer", example=1),
     *             @OA\Property(property="enabled", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Platform status updated"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function toggleUserPlatform(Request $request)
    {
        $request->validate([
            'platform_id' => 'required|exists:platforms,id',
            'enabled' => 'required|boolean',
        ]);

        $this->platformRepository->toggleUserPlatform($request->user(), $request->platform_id, $request->enabled);

        return response()->json(['message' => 'Platform status updated']);
    }

}