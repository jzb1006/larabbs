<?php

namespace App\Http\Controllers\Api;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Api\ImageRequest;
use App\Models\Image;
use App\Transformers\ImageTransformer;
use Illuminate\Http\Request;

class ImagesController extends Controller
{
    //
    public function store(ImageRequest $request,ImageUploadHandler $imageUploadHandler,Image $image){
        $user = $this->user();
        $size = $request->type =='avatar'?362:1024;
        $result = $imageUploadHandler->save($request->image,str_plural($request->type),$user->id,$size);

        $path = $result['path'];
        $image->path = $path;
        $image->user_id = $user->id;
        $image->type = $request->type;
        $image->save();

        return $this->response->item($image,new ImageTransformer())->setStatusCode(201);
    }
}
