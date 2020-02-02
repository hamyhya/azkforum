<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Comment;
use App\forum;
use Auth;

class CommentController extends Controller
{
    public function addComment(Request $request, Forum $forum)
    {
    	$comment = new Comment;
    	$comment->user_id = Auth::user()->id;
    	$comment->content = $request->content;

    	$forum->comments()->save($comment);

    	return back()->withInfo('Terimakasih telah membantu! :)');

    }

    public function replyComment(Request $request, Comment $comment)
    {
    	$reply = new Comment;
    	$reply->user_id = Auth::user()->id;
    	$reply->content = $request->content;

    	$comment->comments()->save($reply);

    	return back()->withInfo('Terimakasih telah membantu! :)');

    }
}
