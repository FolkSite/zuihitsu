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
    public function addTag($tags)
    {
        $tags = explode(',', $tags);

        $tags_return = array();

        foreach ($tags as $tag) {
            $tag = trim($tag);

            if (!empty($tag)) {
                $tag = mb_strtolower($tag);

                $availability = $this::where('name', '=', $tag)->get();

                var_dump($availability->count());

                if ($availability->count() === 0) {
                    $model = $this::create(array(
                        'name' => $tag,
                    ));

                    $tags_return[] = $model->id;
                }

                foreach ($availability as $key) {
                    $tags_return[] = $key->id;
                }

            }

            return $tags_return;
        }
    }
}
