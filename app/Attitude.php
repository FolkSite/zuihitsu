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

    public function __construct()
    {
        $this->tags = new Tag;
    }

    public function createAttitude($post_id, $tags_string) {
        var_dump($post_id);
        var_dump($tags_string);
        /*
        * отправляю строку с тегами функции addTag чтобы получить массив с
        * id тегов, которые надо закрепить за id поста
        */
        $tags_id_arr = $this->tags->addTag($tags_string);

        var_dump($tags_id_arr);

        foreach ($tags_id_arr as $tag_id) {

            var_dump($tag_id);
            var_dump($post_id);

            /*
            * создание строки почему-то не работает с методом create()
            * но если заменить его на insert(), то работает
            */
            $this::create(array(
                'post' => $post_id,
                'tag' => $tag_id,
            ));

        }
    }
}
