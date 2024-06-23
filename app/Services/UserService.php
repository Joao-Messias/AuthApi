<?php
namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
}
