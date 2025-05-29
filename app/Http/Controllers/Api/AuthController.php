<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\Auth\AuthRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{
    protected AuthRepositoryInterface $authRepository;

    public function __construct(AuthRepositoryInterface $authRepository)
    {
        $this->authRepository = $authRepository;
    }


     /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     summary="Register a new user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","password","password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             ),
     *             @OA\Property(property="token", type="string", example="some-generated-token")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
        ]);
    
        $result = $this->authRepository->register($validated);
    
        return response()->json($result);
    }
    



     /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     summary="Login user",
     *     tags={"Auth"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="secret123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="user", type="object",
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", example="john@example.com")
     *             ),
     *             @OA\Property(property="token", type="string", example="some-generated-token")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
 
     
            public function login(Request $request)
        {
            $validated = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $result = $this->authRepository->login($validated);

            return response()->json($result);
        }




    /**
     * @OA\Post(
     *     path="/api/auth/logout",
     *     summary="Logout authenticated user",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Logged out")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */

     public function logout(Request $request)
     {
         $this->authRepository->logout($request->user());
     
         return response()->json(['message' => 'Logged out']);
     }
     
/**
     * @OA\Get(
     *     path="/api/auth/me",
     *     summary="Get authenticated user profile",
     *     tags={"Auth"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile data",
     *         @OA\JsonContent(
     *             @OA\Property(property="id", type="integer", example=1),
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
    public function profile(Request $request)
    {
        return response()->json($request->user());
    }

    /**
 * @OA\Post(
 *     path="/api/profile",
 *     summary="Update authenticated user's profile",
 *     tags={"Auth"},
 *     security={{"sanctum": {}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email"},
 *             @OA\Property(property="name", type="string", example="Jane Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="jane@example.com")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Profile updated successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="name", type="string", example="Jane Doe"),
 *             @OA\Property(property="email", type="string", example="jane@example.com")
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */


 public function updateProfile(Request $request)
 {
     $user = Auth::user();
 
     $validated = $request->validate([
         'name' => ['required', 'string', 'max:255'],
         'email' => [
             'required', 'email', 'max:255',
             Rule::unique('users')->ignore($user->id),
         ],
     ]);
 
     $updatedUser = $this->authRepository->updateProfile($user, $validated);
 
     return response()->json($updatedUser);
 }
 
}