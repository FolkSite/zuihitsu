<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];

    /*
    * функция проверяет есть ли уже такие теги в таблице и добавляет их, если нет
    * принимает @tags строку с тегами через запятую
    */
    public static function addTag($tags)
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
        }

        /*
        * получаю массив с id тегов в таблице Tags
        */
        return $tags_return;
    }

    public static function getTagId($name)
    {
        $tag_id = Tag::where('name', '=', $name)->first();
        return $tag_id->id;
    }
}
