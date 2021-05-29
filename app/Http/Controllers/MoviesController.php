<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use League\HTMLToMarkdown\HtmlConverter;
use App\Movie;
use App\Comment;
use App\Visit;
use App\Tag;
use App\User;
use App\Search;
use Auth;

class MoviesController extends Controller
{
  /**
   * 跳转全部文章页
   *
   * @return Response
   */
  public function list()
  {
    $movies = Movie::where('is_hidden', 'f')->orderBy('created_at', 'desc')->paginate(10);
    foreach ($movies as $movie) {
      $movie->cover = imageURL($movie->cover);
      $movie->source = videoURL($movie->source);
      $movie->type = explode(".", $movie->source)[1];
      $movie->content = str_limit(strip_tags($movie->content_html), 150);
      $movie->created_at_date = $movie->created_at->toDateString();
      $movie->updated_at_diff = $movie->updated_at->diffForHumans();
    }

    $tags = Tag::all();
    return view('movies.list', compact('movies', 'tags'));
  }

  /**
   * 搜索文章
   *
   * @return Response
   */
  public function search(Request $request)
  {
    $key = $request->key;

    // 保存（更新）搜索关键词
    $search = Search::where('name', $key)->first();
    if (!$search) {
      $search = new Search;
      $search->name = $key;
      $search->save();
    }
    $search->increment('search_num');

    $movies = Movie::when($key, function ($query) use ($key) {
      return $query->where('title', 'like', '%' . $key . '%');
    })->where('is_hidden', 'f')->orderBy('created_at', 'desc')->paginate(10);
    foreach ($movies as $movie) {
      $movie->cover = imageURL($movie->cover);
      $movie->source = videoURL($movie->source);
      $movie->type = explode(".", $movie->source)[1];
      $movie->content = str_limit(strip_tags($movie->content_html), 150);
      $movie->created_at_date = $movie->created_at->toDateString();
      $movie->updated_at_diff = $movie->updated_at->diffForHumans();
    }

    $searches = Search::where('search_num', '>', 1)->orderBy('search_num')->limit(10)->get();
    return view('movies.list', compact('movies', 'searches'));
  }

  /**
   * 跳转某篇文章
   * @param Request $request
   * @param $id
   * @return Response
   */
  public function show(Request $request, $id)
  {
    $movie = Movie::findOrFail($id);
    $movie->increment('view');
    $movie->created_at_date = $movie->created_at->toDateString();
    $movie->cover = imageURL($movie->cover);
    $movie->source = videoURL($movie->source);
    $movie->type = explode(".",$movie->source)[1];
    $comments = $movie->comments()->where('parent_id', 0)->orderBy('created_at', 'desc')->get();

    //处理评论，关联回复
    foreach ($comments as $comment) {
      $comment->created_at_diff = $comment->created_at->diffForHumans();
      if ($comment->name) {
        $comment->avatar = mb_substr($comment->name, 0, 1, 'utf-8');
      } else {
        $comment->avatar = '匿';
        $comment->name = 'null';

      }
      if ($comment->user_id == 1) {
        $comment->master = User::select('name', 'avatar')->findOrFail(1);
        $comment->master->avatar = imageURL($comment->master->avatar);
      }

      // $comment->replys = $comment->replys;
      foreach ($comment->replys as $reply) {
        $reply->created_at_diff = $reply->created_at->diffForHumans();
        $reply->target_name = Comment::findOrFail($reply->target_id)->name;
        if ($reply->name) {
          $reply->avatar = mb_substr($reply->name, 0, 1, 'utf-8');
        } else {
          $reply->avatar = '匿';
          $reply->name = 'null';

        }
        if ($reply->user_id == 1) {
          $reply->master = User::select('name', 'avatar')->findOrFail(1);
          $reply->master->avatar = imageURL($reply->master->avatar);
        }
      }
    }

    //自动填写
    $input = (object)[];
    if (Auth::id()) {
      $input = User::select('name', 'email', 'website')->findOrFail(Auth::id());
    } else {
      $comment = Comment::where('ip', $request->ip())->orderBy('created_at', 'desc')->select('name', 'email', 'website')->first();
      $input = $comment ? $comment : $input;
    }

    return view('movies.show', compact('movie', 'comments', 'input'));
  }

}
