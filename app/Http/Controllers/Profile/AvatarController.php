<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class AvatarController extends Controller
{
    public function update(AvatarUpdateRequest $request): RedirectResponse
    {
        if($request->validated()) {
    
            $path = Storage::disk('public')->put('avatars', $request->file('avatar'));
            
            $oldAvatar = $request->user()->avatar;
            if($oldAvatar) {
                Storage::disk('public')->delete($oldAvatar);
            }
            
            auth()->user()->update(['avatar' => $path]);            
        }

        return Redirect::route('profile.edit')->with('status', 'avatar-updated');
    }
}
