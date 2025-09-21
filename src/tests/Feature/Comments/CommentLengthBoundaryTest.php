<?php

namespace Tests\Feature\Comments;

use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentLengthBoundaryTest extends TestCase
{
    use RefreshDatabase;

    public function test_comment_255_ok_and_256_ng()
    {
        $user = User::factory()->create();
        $item = Item::factory()->create();

        // 255文字はOK
        $this->actingAs($user)
            ->post("/item/{$item->id}/comments", [
                'content' => str_repeat('あ', 255),
            ])
            ->assertSessionHasNoErrors();

        // 256文字はNG
        $this->actingAs($user)
            ->post("/item/{$item->id}/comments", [
                'content' => str_repeat('あ', 256),
            ])
            ->assertSessionHasErrors('content');
    }
}
