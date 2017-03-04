<?php

namespace App;

use App\User;
use App\Post;
use App\Attitude;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    /**
    * Массово присваиваемые атрибуты.
    *
    * @var ArrayAccess
    */

    protected $fillable = [
        'header', 'message'
    ];

    /**
    * Получить пользователя - владельца данной задачи
    */

    public function user()
    {
      return $this->belongTo(User::class);
    }

    /**
    * Получить все задачи заданного пользователя.
    *
    * @param User $user
    * @return Collection
    */
    public function forUser(User $user, $skip)
    {
      return $this::where('user_id', $user->id)
        ->orderBy('created_at', 'desc')
        ->skip($skip)
        ->take(10)
        ->get();
    }
    
    /**
     * Получает количество постов, чтобы потом посчитать количество страниц
     */
    public static function getPageCount(User $user)
    {
        return Post::where('user_id', $user->id)
        ->count();
    }

    public function getPost($id)
    {
        return $this::find($id);
    }
    
    public function setPost($request, $post)
    {
        $post_edit = $this::find($post->id);

        $post_edit->header = $request->header;
        $post_edit->message = $request->message;


//        $post_edit->tags = $request->tags;

        $post_edit->save();
    }
    
    /**
    * Возвращает массив для генерации нумерации страниц
    * @param $posts_on_page количество постов на странице
    * @param $this_page номер текущей страницы
    */
    public  function countPages($request, $posts_on_page, $this_page)
    {
        $pages = array();
        
        /*
         * округляет количество страниц в большую сторону
         */
        $pages_count = ceil((Post::getPageCount($request->user()) / $posts_on_page));
        
        /*
         * если количество постов меньше чем счетчик $posts_on_page, то
         * функция вернет FALSE и ссылки на страницы не будут генерироваться
         */
        if ($pages_count > 1) {
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
        
        return $pagesButtons = false;
    }
}
