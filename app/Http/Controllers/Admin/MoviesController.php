<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
USE Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Common\MyUpload;
use App\Movie;
use App\Comment;
use App\Visit;
use App\Tag;
use Auth;
use GrahamCampbell\Markdown\Facades\Markdown;

class MoviesController extends Controller
{
  /**
   * 返回所有的文章 [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    $order = $request->order;
    $status = $request->status;
    $top = $request->top;
    $search = $request->search;
    $movies = Movie::when(isset($status), function ($query) use ($status) {
      return $query->where('is_hidden', $status);
    })->when(isset($top), function ($query) use ($top) {
      return $query->where('is_top', $top);
    })->when(isset($search), function ($query) use ($search) {
      return $query->where('title', 'like', '%' . $search . '%');
    })->when(isset($order), function ($query) use ($order) {
      $arr = explode('_', $order);
      $isDesc = end($arr) == 'desc';
      if ($isDesc == 'desc') {
        array_pop($arr);
        return $query->orderBy(join('_', $arr), 'desc');
      } else {
        return $query->orderBy($order);
      }
    })->paginate($request->pagesize);

    for ($i = 0; $i < sizeof($movies); $i++) {
      $movies[$i]->key = $movies[$i]->id;
      $movies[$i]->content = str_limit(strip_tags($movies[$i]->content), 60);
      $movies[$i]->updated_at_diff = $movies[$i]->updated_at->diffForHumans();
    }
    return $movies;
  }

  /**
   * 返回某个文章 [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    $movie = Movie::findOrFail($id);
    for ($i = 0; $i < sizeof($movie->tags); $i++) {
      $movie->tags[$i] = $movie->tags[$i]->name;
    }

    $tags = Tag::all();
    for ($i = 0; $i < sizeof($tags); $i++) {
      $tags[$i] = $tags[$i]->name;
    }
    return response()->json(['movie' => $movie, 'tags_arr' => $tags,]);
  }

  /**
   * 创建或更新文章 [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function store(Request $request)
  {
    if ($request->id) {
      $movie = Movie::findOrFail($request->id);
      $message = '保存成功！';
    } else {
      $movie = new Movie;
      $message = '创建成功！';
    }
    $movie->title = $request->title;
    $movie->cover = $request->cover;
    $movie->is_markdown = $request->is_markdown;
    if ($request->is_markdown) {
      $movie->content_markdown = $request->content_markdown;
      $movie->conten = Markdown::convertToHtml($request->content_markdown);
    } else {
      $movie->content = $request->content_html;
    }
    $movie->save();
    //处理标签
    //先删除文章关联的所有标签
    //遍历标签，如果标签存在则添加关联，如果标签不存在先创建再添加关联
    $movie->tags()->detach();
    for ($i = 0; $i < sizeof($request->tags); $i++) {
      $tag = Tag::where('name', $request->tags[$i])->first();
      if ($tag) {
        $movie->tags()->attach($tag->id);
      } else {
        $tag = new Tag;
        $tag->name = $request->tags[$i];
        $tag->save();
        $movie->tags()->attach($tag->id);
      }
    }
    return response()->json(['message' => $message]);
  }

  /**
   * 删除文章 [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function destroy($id)
  {
    $movie = Movie::findOrFail($id);
    $movie->delete();
    return response()->json(['message' => '删除成功!']);
  }

  /**
   * 发表（或隐藏）文章 [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function publish($id)
  {
    $movie = Movie::findOrFail($id);
    if ($movie->is_hidden) {
      $movie->is_hidden = 0;
      $movie->save();
      return response()->json(['message' => '文章已发表！']);
    } else {
      $movie->is_hidden = 1;
      $movie->save();
      return response()->json(['message' => '文章已切换为笔记！']);
    }
  }

  /**
   * 置顶文章 [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function top($id)
  {
    $movie = Movie::findOrFail($id);
    if ($movie->is_top) {
      $movie->is_top = 0;
      $movie->save();
      return response()->json(['message' => '文章已取消置顶！']);
    } else {
      $movie->is_top = 1;
      $movie->save();
      return response()->json(['message' => '文章已置顶！']);
    }
  }

  /**
   * html 转 markdown [API]
   *
   * @return \Illuminate\Http\Response
   */
  public function markdown(Request $request)
  {
    $converter = new HtmlConverter();
    return $converter->convert($request->content_html);
  }

  /**
   * API直接存储文件
   */
  public function uploadFileApi(Request $request)
  {
    return MyUpload::uploadFile($request->file);
  }

  /**
   * 导入其他数据库文章
   */
  public function import(Request $request)
  {
    $inputs = $request->all();

    $movies = DB::table($inputs['table'])->get();
    unset($inputs['table']);

    foreach ($movies as $movie) {
      $newMovie = new Movie;
      $newMovie->id = $movie->id;
      foreach ($inputs as $key => $value) {

        if ($key == 'is_top' || $key == 'is_hidden') {
          $arr = explode('|', $value);
          $arr0 = $arr[0];//字段名
          $arr1 = $arr[1];//true值
          $newMovie->$key = $movie->$arr0 == $arr1 ? 1 : 0;
        } elseif ($key == 'content') {
          $newMovie->content = $movie->$value;
        } elseif ($key == 'cover') {
          $arr = explode('/', $movie->$value);
          if (sizeof($arr)) {
            $newMovie->$key = $arr[sizeof($arr) - 1];
          } else {
            $newMovie->$key = $movie->$value;
          }
        } else {
          $newMovie->$key = $movie->$value;
        }
      }
      $newMovie->save();
    }

    return response()->json(['message' => '导入成功！']);
  }
}
