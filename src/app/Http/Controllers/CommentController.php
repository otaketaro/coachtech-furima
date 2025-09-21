<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentStoreRequest;
use App\Models\Comment;
use App\Models\Item;

class CommentController extends Controller
{
    public function store(CommentStoreRequest $request, Item $item)
    {
        Comment::create([
            'item_id' => $item->id,
            'user_id' => $request->user()->id,
            'content' => $request->input('content'),
        ]);

        return back(303);
    }

    public function __construct()
    {
        $this->middleware('auth');
    }
}
