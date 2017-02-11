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

        $this->createAttitude($model->id, $request->tags);

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
          'post' => $this->getPost($post->id),
        ]);
    }

    public function save(Request $request, Post $post)
    {
        $this->authorize('edit', $post);
        /*
        * полагаю, код ниже так себе, но пока не могу сказать почему
        */
         $this->setPost($request, $post);
         $this->addTag($request->tags);
         return redirect('/posts');
    }

    public function getPost($id)
    {
        return Post::find($id);
    }

    public function setPost($request, $post)
    {
        $post_edit = Post::find($post->id);
        $post_edit->header = $request->header;
        $post_edit->message = $request->message;
        $post_edit->tags = $request->tags;
        $post_edit->save();
    }

    public function createAttitude($post_id, $tags_string)
    {
        /*
        * отправляю строку с тегами функции addTag чтобы получить массив с
        * id тегов, которые надо закрепить за id поста
        */
        $tags_id_arr = $this->addTag($tags_string);

        foreach ($tags_id_arr as $tag_id) {

            var_dump($tag_id);
            var_dump($post_id);

            /*
            * создание строки почему-то не работает с методом create()
            * но если заменить его на insert(), то работает
            */
            Attitude::create(array(
                'post' => $post_id,
                'tag' => $tag_id,
            ));

        }
    }

    public function addTag($tags)
    {
        $tags = explode(',', $tags);

        $tags_return = array();

        foreach ($tags as $tag) {
            $tag = trim($tag);

            if (!empty($tag)) {
                $tag = mb_strtolower($tag);

                $availability = Tag::where('name', '=', $tag)->get();

                if ($availability->count() === 0) {
                    $model = Tag::create(array(
                        'name' => $tag,
                    ));

                    $tags_return[] = $model->id;
                }

                foreach ($availability as $key) {
                    $tags_return[] = $key->id;
                }

            }

            return $tags_return;
        }
    }
}
