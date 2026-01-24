<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function show(Request $request)
    {
        return $request->user()->load('plan');
    }

    public function update(UpdateUserRequest $request)
    {
        $user = Auth::user();
        
        $validated = $request->validated();

        if ($request->hasFile('avatar')) {
             // Delete old avatar if exists
             if ($user->avatar) {
                 \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
             }
             $path = $request->file('avatar')->store('avatars', 'public');
             $validated['avatar'] = $path;
        }

        $user->update($validated);
        
        return $user;
    }
}
