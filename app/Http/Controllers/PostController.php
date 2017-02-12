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
        $post_for_user = $this->posts->forUser($request->user());

        return view ('posts.index', [
        'posts' => $post_for_user,
        'tags' => $this->getTags($post_for_user),
        ]);
    }

    public function getPostsByTag(Request $request, $tag) {

        $post_for_user = $this->posts->forUser($request->user());

        $tag_id = Tag::getTagId($tag);
        $posts_attitude = Attitude::getPostsByTagId($tag_id);
        $post_by_tag = array();

        /*
        * складываю в массив $post_by_tag только те посты, которые соответствуют
        * тегу, чтобы передать их в Вид
        */
        foreach ($posts_attitude as $attitude_post) {

            foreach ($post_for_user as $post) {
                if ($post->id === $attitude_post->post) {
                    $post_by_tag[] = $post;
                }
            }
        }

        return view ('posts.index', [
        'posts' => $post_by_tag,
        'tags' => $this->getTags($post_by_tag),
        ]);
    }

    public function getTags($posts) {
        $tags_arr = array();
        foreach ($posts as $post) {
           $tags_arr[$post->id] = $this->attitude->getTags($post->id);
        }
        return $tags_arr;
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

        $this->attitude->createAttitude($model->id, $request->tags);

        return redirect('/posts');
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
         return redirect('/posts');
    }
}
