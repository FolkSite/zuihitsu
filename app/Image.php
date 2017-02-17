<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image as ImageManager;

class Image extends Model
{
    protected $fillable = [
        'post_id', 'name', 'thumbnail_name'
    ];

    public static function uploadImg($request, $post_id)
    {

        /**
        * генерирую имя файла из timestamp, трех случайных символов и расширения,
        * на случай если в одну и ту же секунду будет отправлено несколько файлов
        */
        $file_extension = explode('/',$request->file('images')->getMimeType());
        $file_name = sprintf("%'.03d", rand(0, 999)) . "t" . time() . '.'
            . $file_extension[1];
        $destinationPath = "img";

        $request->file("images")->move($destinationPath, $file_name);

        $img = ImageManager::make($destinationPath . '/' . $file_name);

        $img->resize(150, null, function ($construct) {
            $construct->aspectRatio();
        });

        $thumbnail_name =  'thumbnail_150px_' . $file_name;

        $img->save($destinationPath . '/' . $thumbnail_name);

        $model = $request->user()->images()->create([
            'post_id' => $post_id,
            'name' => $file_name,
            'thumbnail_name' => $thumbnail_name,
        ]);

    }

    /**
    * функция принимает id текущего пользователя и возвращает массив
    * где первый ключь это id поста в котором массив с сылками на картинки
    * которые принадлежат посту
    */
    public static function getImages($user_id) {

        $post_images = array();

        $images = Image::where('user_id', $user_id)->get();
/*
        foreach ($images as $img) {
            $post_images[$img->post_id][] = '/img/'.$img->thumbnail_name;
        }
*/
        for ($i=0; $i < count($images); $i++) {
            $img = $images[$i];
            $post_images[$img->post_id][$i] = array(
                'img' => '/img/' . $img->name,
                'thumbnail' => '/img/' . $img->thumbnail_name,
            );
        }

        return $post_images;
    }

}
