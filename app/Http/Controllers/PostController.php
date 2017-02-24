<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Post;
use App\Tag;
use App\Attitude;
use App\Image;

class PostController extends Controller
{
    /**
    * FIXME: Строки в табллицах Attitude, Tags, и кажется Images не привязаны к
    * конкретному пользователи, то есть данные оттуда может получить кто угодно и
    * делать с ними что угодно. Я уже добавил это поле в миграциях соответствующих
    * таблиц. Надо дописать код, чтобы его использовать
    * FIXME: добавил, вроде работает. Осталось добавить проверку соответствия
    * записи в таблице пользователю перед ее редактирвоанием
    */

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
    
    /**
     * Показывает главную страницу блога со всеми постами
     * @param type $page номер страницы
     */
    public function index (Request $request, $page = 1)
    {
        /*
         * вторым аргументом передается количество постов, которое требуется вывести на страницу
         */
        $post_for_user = $this->posts->forUser($request->user(), 0);

        return $this->index_view_return($post_for_user, $request, $page);
    }
    
    
    /**
    * Возвращает массив для генерации нумерации страниц
    * @param $posts_on_page количество постов на странице
    * @param $this_page номер текущей страницы
    */
    public function countPages($request, $posts_on_page, $this_page)
    {
        $pages = array();
        
        /*
         * округляет количество страниц в большую сторону
         */
        $pages_count = ceil((Post::getPageCount($request->user()) / $posts_on_page));
        
        /*
         * создает массив в котором ключ - номер страницы, а значение равно 'this'
         * если это текущая страница
         */
        for ($index = $pages_count; $index > 0; $index--) {
            if ((int) $index === (int) $this_page) {
                $pages[$index] = "this";
                $prev = $index - 1;
                $next = $index + 1;           
            } else {
                $pages[$index] = "";
            }           
        }
        
        ksort($pages);
        
        /*
         * если это последняя страницы, то кнопка NEXT будет равна нулю
         * тогда ВИД делает ее неактивной
         */
        if($next >= $pages_count){            
            $next = 0;
        }
        
        $pagesButtons = array('buttons' => array(
                                                'next' => $next,
                                                'prev' => (int) $prev,
                                            ),
                'pages' => $pages,
        );
        
        return $pagesButtons;
    }

        /**
     * Отображает все посты, которые принадлежат тегу
     */
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

        return $this->index_view_return($post_by_tag, $request);
    }

    public function index_view_return($posts, $request, $page)
    {
        return view ('posts.index', [
        'posts' => $posts,
        'tags' => $this->getTags($posts),
        'images' => Image::getImages($request->user()->id),
        'tags_cloud' => Tag::getTagCloud(),
        'pages' => $this->countPages($request, 10, $page),
        ]);
    }

    public function getTags($posts) {
        $tags_arr = array();
        foreach ($posts as $post) {
           $tags_arr[$post->id] = $this->attitude->getTags($post->id, 'name');
        }
        return $tags_arr;
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'header' => 'max:255',
            'message' => 'required|max:10240',
            'tags' => 'max:255',
            'images' => 'image',

        ]);

        $model = $request->user()->posts()->create([
            'header' => $request->header,
            'message' => $request->message,
        ]);

        $tags = $this->tags->addTag($request, $request->tags);
        $this->attitude->createAttitude($request, $model->id, $tags);

        if ($request->hasFile('images')) {
            var_dump('Файл получен');
            if ($request->file('images')->isValid()) {
                Image::uploadImg($request, $model->id);
            } else {
                var_dump('Файл поврежден');
            }
        }

        return redirect('/posts');
        
        /*
         * для AJAX запроса
        $html = '
            <div class="panel panel-default post" style="word-break: break-word;">
            <div class="panel-heading"><strong>Заголовок</strong></div>
                <div class="panel-body">
                    Сообщение
                  <br>
                  <br>
                      Картинки
                  <br>
                <em>
                  <br>
                      Теги:
                </em>
                </div>
                <div class="panel-footer">                      
                    <button type="submit" id="" class="btn btn-default btn-sm">
                        <i class="fa fa-btn fa-trash"></i>Изменить</button>
                    <button type="submit" id="" class="btn btn-default btn-sm">
                        <i class="fa fa-btn fa-trash"></i>Удалить</button>
                </div>
            </div>
        ';
        
        return $html;
        */
    }
    
    /**
     * метод удаляет пост
     */

    public function destroy(Request $request, Post $post)
    {
    
        $this->authorize('edit', $post);
        
        /*
         * Удаляет теги, картинки и сам пост
         */
        Attitude::delAttitudeToPost($post->id);
        Image::delImagesToPost($post);       
        $post->delete(); 
        /*
         * 
        Обновляет страницу. Использовался до внедрения AJAX
        */
        return redirect('/posts');
                
        /*
         * если пост удален, то AJAX запросу будет возвращен TRUE                 
        return "true";
         */

    }
    
    /**
     * Метод удаляет картинку в посте при его редактировании
     */
    public function destroyImage(Request $request, Image $image)
    {        
        $this->authorize('edit', $image);

        Image::delImagesOne($image);
        
        /**
         * TODO: функция должна переходить обратно на страницу с редактирование поста
         *  в которой был вызван. Как-то надо сгенерировать POST запрос для вызова edit()
         */
        return redirect('/posts');
    }
    
    /**
     * метод удаляет тег
     */
    public function destroyTag(Request $request, Tag $tag)
    {
        /**
        * TODO: должны удаляться все зависимости 
        *
        */
        $this->authorize('edit', $tag);
        $tag->delete();
        Attitude::delAttitudeToTag($tag);
        return redirect('/post/edit/tags');
    }

    public function edit(Request $request, Post $post)
    {
        $this->authorize('edit', $post);
        return view ('posts.edit', [
          'edit_post' => $this->posts->getPost($post->id),
          /*
          * склеиваю в строку, потому что функция возвращает массив
          */
          'edit_tags' => implode(', ', Attitude::getTags($post->id, 'name')),
          'images' => Image::getImagesToPost($post),
        ]);
    }

    public function editTags(Request $request, Post $post)
    {
        return view ('posts.tags_edit', [
        'tags_cloud' => Tag::getTagCloud(),
        ]);
    }

    public function save(Request $request, Post $post)
    {
        $this->authorize('edit', $post);

        $this->posts->setPost($request, $post);

        /*
        * получаю массив с id тегов в таблице Tags которые в изменном посте
        */
        $tags_change = $this->tags->addTag($request, $request->tags);

        /*
        * получаю массив в котором перечислени теги соответствующие конкретному
        * посту
        */
        $tags_by_post = Attitude::getTags($post->id, 'id');

        /*
        * если у поста нет тега, то он будет добавлен
        */

        $tags_new = array();

        /*
        * собираю в строку id тегов, которые не были найдены в посте до редактирования
        */
        foreach ($tags_change as $tag) {
            if (array_search($tag, $tags_by_post) === FALSE) {
                $tags_new[] = $tag;
            }
        }

        if (!empty($tags_new)) {
            Attitude::createAttitude($request, $post->id, $tags_new);
        }

        /*
        * если тег не найден серди тегов в измененном посте, то он удаляется
        * из поста
        */
        foreach ($tags_by_post as $tag) {
            if (array_search($tag, $tags_change) === FALSE) {
                Attitude::delAttitude($post->id, $tag);
            }
        }

        return redirect('/posts');
    }
}
