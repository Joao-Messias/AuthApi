<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserService
{
    public function createUser($userData)
    {
        $user = new User();
        $user->name = $userData['name'];
        $user->last_name = $userData['last_name'];
        $user->email = $userData['email'];
        $user->password = Hash::make($userData['password']);
        $user->save();

        return $user;
    }

    public function updateUser($id, $userData)
    {
        $user = User::findOrFail($id);

        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        $user->update($userData);

        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(null, 204);
    }

    public function getUserByEmail($email)
    {
        return User::where('email', $email)->first();
    }

    public function validateCredentials(array $credentials): \Illuminate\Http\JsonResponse
    {
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Invalid credentials'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }
}
