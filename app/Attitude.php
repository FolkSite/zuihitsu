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

    public function createAttitude($post_id, $tags_string)
    {

        /*
        * отправляю строку с тегами функции addTag чтобы получить массив с
        * id тегов, которые надо закрепить за id поста
        */
        $tags_id_arr = Tag::addTag($tags_string);

        foreach ($tags_id_arr as $tag_id) {

            $this::create(array(
                'post' => $post_id,
                'tag' => $tag_id,
            ));

        }
    }

    public function getTags($post_id)
    {
        $attitude = Attitude::where('post', '=', $post_id)->get();

        $tags_id = array();
        $tags = array();

        foreach ($attitude as $key) {
            $tags_id[] = $key->tag;
        }

        foreach ($tags_id as $tag_id) {
             $tag_row = Tag::find($tag_id);
             $tags[] = $tag_row->name;
        }

        return $tags;
    }

    public static function getPostsByTagId($tag_id)
    {
        $posts = Attitude::where('tag', '=', $tag_id)->get();
        return $posts;
    }
}
