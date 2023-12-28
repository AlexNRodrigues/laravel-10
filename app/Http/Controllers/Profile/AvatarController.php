<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use App\Http\Requests\AvatarUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI\Laravel\Facades\OpenAI;

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

    public function generate(Request $request)
    {
        $result = OpenAI::images()->create([
            'prompt' => 'create avatar for user with cool style animated',
            'n'      => 1,
            'size'   => '256x256',
        ]);
        
        $contents = file_get_contents($result->data[0]->url);

        $filename = Str::random(25);

        $oldAvatar = $request->user()->avatar;
        if($oldAvatar) {
            Storage::disk('public')->delete($oldAvatar);
        }

        Storage::disk('public')->put("avatars/$filename.jpg", $contents);

        auth()->user()->update(['avatar' => "avatars/$filename.jpg"]);
        return redirect(route('profile.edit'))->with('message', 'Avatar is updated');
        
    }
}
