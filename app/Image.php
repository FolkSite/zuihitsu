<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Intervention\Image\Facades\Image as ImageManager;
use App\User;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    protected $fillable = [
        'post_id', 'name', 'thumbnail_name'
    ];
    
    /**
    * Получить пользователя - владельца данной задачи
    */

    public function user()
    {
      return $this->belongTo(User::class);
    }

    public static function uploadImg($request, $post_id)
    {

        /**
        * генерирую имя файла из timestamp, трех случайных символов и расширения,
        * на случай если в одну и ту же секунду будет отправлено несколько файлов
        * FIXME: лучше сделать чтобы для файлов каждого пользователя была отдельная
        * папка
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

        for ($i=0; $i < count($images); $i++) {
            $img = $images[$i];
            $post_images[$img->post_id][$i] = array(
                'img' => '/img/' . $img->name,
                'thumbnail' => '/img/' . $img->thumbnail_name,
            );
        }

        return $post_images;
    }
    
    /**
     * Получение всех картинок конкретного поста
     */
    
    public static function getImagesToPost($post) {

        $post_images = array();

        $images = Image::where('post_id', $post->id)->get();
        
        foreach ($images as $img) {
            $post_images[] = array(
                'id' => $img->id,
                'img' => '/img/' . $img->name,
                'thumbnail' => '/img/' . $img->thumbnail_name,
            );
        }

        return $post_images;
    }
    
    /**
     * Удаляет картинки, принадлежащие переданному посту
     */
    public static function delImagesToPost($post) {
        $images = Image::where('post_id', $post->id)->get();
        $disk = Storage::disk('images');
        foreach ($images as $img) {
            $disk->delete($img->name);
            $disk->delete($img->thumbnail_name);
            $img->delete();
        }
    }
    
    /**
     * Удаляет одну картинку по ее id
     */
    public static function delImagesOne($image) {
        $images = Image::find($image->id);
        $disk = Storage::disk('images');
        $disk->delete($images->name);
        $disk->delete($images->thumbnail_name);
        $images->delete();
    }

}
