<?php

namespace App;

use App\Tag;

use Illuminate\Database\Eloquent\Model;
use App\User;

/*
* модель создает строку в одноименной таблице, в которой присваивает постам теги
* или посты тегам, что одно и то же, чтобы потом можно было искать все посты
* по конкретному тегу
*/
class Attitude extends Model
{
    protected $table = 'attitude';
    protected $fillable = [
        'post', 'tag'
    ];
    public $timestamps = false;
    
    // WTF: не знаю зачем я сделал это свойство, надо будет попробовать без него
    // когда допилю аутентификацию
    protected $tags;
    
    /**
    * Получить пользователя - владельца данной задачи
    */
    public function user()
    {
      return $this->belongTo(User::class);
    }

    public static function createAttitude($request, $post_id, $tags_id)
    {
        foreach ($tags_id as $tag_id) {

            $request->user()->attitude()->create(array(
                'post' => $post_id,
                'tag' => $tag_id,
            ));

        }
    }

    public static function delAttitude($post_id, $tag_id_del) {
        $attitude = Attitude::where('post', '=', $post_id)->get();

        foreach ($attitude as $key) {
            if($key->tag === $tag_id_del) {
                $del_row = Attitude::find($key->id);
                $del_row->delete();
            }
        }
    }

    public static function delAttitudeToPost($post_id) {
        $attitude = Attitude::where('post', '=', $post_id)->get();

        foreach ($attitude as $key) {
            $key->delete();
        }
    }
    
    /**
     * Удаляет все отношения тегов к постам конкретного пользователя принадлежащие 
     * определенному тегу
     */
    public static function delAttitudeToTag($tag) {
        
        
        $attitude = Attitude::where('user_id', '=', $tag->user_id)
                ->where('tag', '=', $tag->id)
                ->get();

        foreach ($attitude as $key) {
            $key->delete();
        }
    }
    
    public function forUser(User $user)
    {
      return $this::where('user_id', $user->id)
        ->get();
    }

    /*
    * аргумент $type определяет вернет функция name или id тега;
    */
    public static function getTags($post_id, $type)
    {
        $attitude = Attitude::where('post', '=', $post_id)->get();

        $tags_id = array();
        $tags = array();

        foreach ($attitude as $key) {
            $tags_id[] = $key->tag;
        }

        foreach ($tags_id as $tag_id) {
             $tag_row = Tag::find($tag_id);

             if ($type == 'name') {
                 $tags[] = $tag_row->name;
             } else if ($type == 'id') {
                 $tags[] = $tag_row->id;
             }
        }

        /*
        * возвращаю массив в котором перечислени теги соответствующие конкретному
        * посту
        */

        return $tags;
    }

    public static function getPostsByTagId($tag_id)
    {
        $posts = Attitude::where('tag', '=', $tag_id)->get();
        return $posts;
    }
}
