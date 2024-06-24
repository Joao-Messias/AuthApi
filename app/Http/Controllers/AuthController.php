<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(UserRequest $request)
    {
        $data = $request->validated();

        $user = $this->userService->createUser($data);

        return response()->json([
            'message' => 'ok',
            'user' => [
                'name' => $user->name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'password' => $user->password,
            ]
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $data = $request->validated();

        $user = $this->userService->updateUser($id, $data);

        return response()->json($user, 200);
    }

    public function delete($id)
    {
        $this->userService->deleteUser($id);

        return response()->json(null, 204);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function me()
    {
        return response()->json(Auth::user());
    }

    public function checkAuth(Request $request)
    {
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['auth' => false], 401);
        }

        $token = str_replace('Bearer ', '', $token);

        try {
            $payload = JWTAuth::setToken($token)->getPayload();
        } catch (JWTException $e) {
            return response()->json(['auth' => false], 401);
        }

        $userId = $request->query('user');
        if (!$userId) {
            return response()->json(['auth' => false], 401);
        }

        $user = User::find($userId);

        if (!$user) {
            return response()->json(['auth' => false], 404);
        }

        if ($payload['sub'] == $user->getJWTIdentifier()) {
            return response()->json(['auth' => true], 200);
        }

        return response()->json(['auth' => false], 401);
    }
}
