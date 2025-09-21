<?php

namespace Tests\Feature\Profile;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarUploadTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function 画像をアップロードするとstorageに保存され_d_bのavatar_pathに反映される()
    {
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->create('avatar.jpg', 10, 'image/jpeg');

        $res = $this->actingAs($user)->put(route('mypage.profile.update'), [
            'name' => 'アイコン太郎',
            'postal_code' => '123-4567',
            'address' => '東京都〇〇',
            'building' => '△△ビル',
            'avatar' => $file, // ← コントローラ側のフィールド名に合わせる
        ]);

        $res->assertRedirect();

        $user->refresh();

        // DBにパスが保存されている
        $this->assertNotNull($user->avatar_path);

        // publicディスクに実体がある
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    /** @test */
    public function 画像はjpeg_pngのみ許可_その他はエラー()
    {
        $user = User::factory()->create();

        $pdf = UploadedFile::fake()->create('avatar.pdf', 10, 'application/pdf');

        $res = $this->actingAs($user)->put(route('mypage.profile.update'), [
            'name' => 'NG太郎',
            'postal_code' => '123-4567',
            'address' => '東京都〇〇',
            'building' => '',
            'avatar' => $pdf,
        ]);

        $res->assertSessionHasErrors(['avatar']);
    }
}
