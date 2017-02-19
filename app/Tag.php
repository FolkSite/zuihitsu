<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Attitude;
use App\User;

class Tag extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'name'
    ];
    
    /**
    * Получить пользователя - владельца данной задачи
    */

    public function user()
    {
      return $this->belongTo(User::class);
    }

    /*
    * функция проверяет есть ли уже такие теги в таблице и добавляет их, если нет
    * принимает @tags строку с тегами через запятую
    */
    public static function addTag($request, $tags)
    {
        var_dump($tags);
        $tags = explode(',', $tags);
        $tags_return = array();

        foreach ($tags as $tag) {
            var_dump($tag);
            $tag = trim($tag);

            if (!empty($tag)) {
                $tag = mb_strtolower($tag);
                var_dump($tag);
                $availability = Tag::where('name', '=', $tag)->get();
                var_dump($tag);
                if ($availability->count() === 0) {
                    var_dump($tag);
                    $model = $request->user()->tags()->create(array(
                        'name' => $tag,
                    ));

                    $tags_return[] = $model->id;
                }
                var_dump($tag);
                foreach ($availability as $key) {
                    $tags_return[] = $key->id;
                }
                var_dump($tag);
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
            $tagsArr[] = array('name' => $tag->name,
                'id' => $tag->id,
            );
        }

        $tagsCloud = array();

        foreach ($tagsArr as $id => $value) {
            var_dump($id);
            $posts = Attitude::where('tag', '=', $value['id'])->get();
            $tagsCloud[] = array('name' => $value['name'],
                'count' => count($posts),
                'id' => $value['id'],
            );
        }

        usort($tagsCloud, function($a,$b){
            return ($b['count']-$a['count']);
        });
        
        var_dump($tagsCloud);

        return $tagsCloud;
    }
}
