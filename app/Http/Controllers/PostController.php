<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Post;
use App\Tag;
use App\Attitude;

class PostController extends Controller
{
    /**
    * Экземпляр PostRepository.
    *
    * @var PostRepository
    */
    protected $posts;
    protected $tags;
    protected $attitude;

    /**
    * Отображение списка всех задач пользователя.
    *
    * @param Request $Request
    * @return Response*/

    public function __construct(Post $posts, Tag $tags, Attitude $attitude)
    {
        $this->middleware('auth');
        $this->posts = $posts;
        $this->tags = $tags;
        $this->attitude = $attitude;

    }

    public function index (Request $request)
    {
      return view ('posts.index', [
        'posts' => $this->posts->forUser($request->user()),
      ]);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'header' => 'max:255',
            'message' => 'required|max:10240',
            'tags' => 'max:255',
        ]);

        $model = $request->user()->posts()->create([
            'header' => $request->header,
            'message' => $request->message,
            'tags' => $request->tags,
        ]);

        var_dump($model->id);

        $this->attitude->createAttitude($model->id, $request->tags);

//        return redirect('/posts');
    }

    public function destroy(Request $request, Post $post)
    {
        $this->authorize('edit', $post);
        $post->delete();
        return redirect('/posts');
    }

    public function edit(Request $request, Post $post)
    {
        $this->authorize('edit', $post);
        return view ('posts.edit', [
          'post' => $this->posts->getPost($post->id),
        ]);
    }

    public function save(Request $request, Post $post)
    {
        $this->authorize('edit', $post);
        /*
        * полагаю, код ниже так себе, но пока не могу сказать почему
        */
         $this->posts->setPost($request, $post);
         $this->tags->addTag($request->tags);
//         return redirect('/posts');
    }
}
