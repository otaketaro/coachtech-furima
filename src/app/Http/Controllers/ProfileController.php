<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = auth()->user();

        return view('mypage.edit', compact('user'));
    }

    public function update(ProfileRequest $request): RedirectResponse
    {
        $user = $request->user();

        // ここでリクエストが来ているかログ出力
        Log::info('profile.update start', ['user_id' => $user->id]);

        if ($request->hasFile('avatar')) {
            $f = $request->file('avatar');
            Log::info('avatar received', [
                'name' => $f->getClientOriginalName(),
                'size' => $f->getSize(),
                'mime' => $f->getMimeType(),
            ]);
        } else {
            Log::warning('avatar NOT received');
        }

        // 基本項目を更新
        $user->fill([
            'name' => $request->input('name'),
            'postal_code' => $request->input('postal_code'),
            'address' => $request->input('address'),
            'building' => $request->input('building'),
        ]);

        // 画像が来ていれば保存（storage:link 済み前提）
        if ($request->hasFile('avatar')) {
            // 旧ファイル削除（任意）
            if (! empty($user->avatar_path) && Storage::disk('public')->exists($user->avatar_path)) {
                Storage::disk('public')->delete($user->avatar_path);
            }

            $path = $request->file('avatar')->store('avatars', 'public'); // storage/app/public/avatars/...
            $user->avatar_path = $path; // ← ここを avatar_path に統一
        }

        $user->save();

        return redirect()
            ->route('mypage.profile.edit')
            ->with('status', 'プロフィールを保存しました。');
    }
}
