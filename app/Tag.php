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
        $tags = explode(',', $tags);
        $tags_return = array();

        foreach ($tags as $tag) {
            $tag = trim($tag);

            if (!empty($tag)) {
                $tag = mb_strtolower($tag);
                $availability = Tag::where('name', '=', $tag)->get();
                if ($availability->count() === 0) {
                    $model = $request->user()->tags()->create(array(
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
             $posts = Attitude::where('tag', '=', $value['id'])->get();
            $tagsCloud[] = array('name' => $value['name'],
                'count' => count($posts),
                'id' => $value['id'],
            );
        }

        usort($tagsCloud, function($a,$b){
            return ($b['count']-$a['count']);
        });
        

        return $tagsCloud;
    }
}
