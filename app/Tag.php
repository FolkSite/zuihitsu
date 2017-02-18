<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attitude;

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
        var_dump($tag_id->id);
        return $tag_id->id;
    }

    public static function getTagCloud()
    {
        $tagsArr = array();
        $tags = Tag::all();

        foreach ($tags as $tag) {
            $tagsArr[$tag->id] = array('name' => $tag->name);
        }

        $tagsCloud = array();

        foreach ($tagsArr as $id => $value) {
            var_dump($id);
            $posts = Attitude::where('tag', '=', $id)->get();
            $tagsCloud[$id] = array('name' => $value['name'],
                'count' => count($posts),
            );
        }

        usort($tagsCloud, function($a,$b){
            return ($b['count']-$a['count']);
        });

        return $tagsCloud;
    }
}
