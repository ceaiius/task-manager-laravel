<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\UpdateNameRequest;

use Illuminate\Support\Facades\Hash; 
use Illuminate\Http\JsonResponse;   

class ProfileController extends Controller
{

    public function updateName(UpdateNameRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->name = $request->validated('name');
        $user->save();
        return response()->json(['user' => $user]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->password = Hash::make($request->validated('new_password'));
        $user->save();

        return response()->json(['message' => 'Password changed successfully!']);
    }
}