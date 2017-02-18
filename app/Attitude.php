<?php

namespace App;

use App\Tag;

use Illuminate\Database\Eloquent\Model;

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
    protected $tags;

    public static function createAttitude($post_id, $tags_id)
    {
        foreach ($tags_id as $tag_id) {

            Attitude::create(array(
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

    public static function delAttitudeAll($post_id) {
        $attitude = Attitude::where('post', '=', $post_id)->get();

        foreach ($attitude as $key) {
            $key->delete();
        }
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
