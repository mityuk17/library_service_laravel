<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Create new user
     */
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required|alpha_num|min:5',
            'role' => 'required'
        ]);

        $inputs = $request->all();
        $inputs['password'] = Hash::make($inputs['password']);

        $user = User::create($inputs);

        return response()->json($user);
    }

    /**
     * Delete user
     */
    public function delete(string $user_id)
    {
        $user = User::find($user_id);

        if (!$user){
            return response()->json('No User found', 404);
        }

        $user->delete();


        return response()->json('Successfully deleted');
    }


}
