<?php

use Intervention\Image\ImageManager;
set_time_limit(20);

$error="";
$img=null;
$manager=new ImageManager();
//           src       w       h        mode     background  quality  ext
$reg="/^im\/(.*)\/im-([0-9]+)x([0-9]+)-([a-z]+)-([A-Za-z0-9]+)-([0-9]+)\.([a-z]+)/";
if(preg_match($reg,the()->route,$m)){
    $src=$m[1];
    $w=$m[2];
    $h=$m[3];
    $mode=$m[4];
    $bg=$m[5];
    $quality=$m[6];
    $ext=$m[7];

    //création de l'imge
    try{
        $img=$manager->make($src)->orientate();
    }catch(Exception $exception){
        $error=$exception->getMessage();
    }

    if(!$error){
        if(!in_array($ext,["jpg","png","webp"])){
            $error="invalid extension";
        }
    }
    if(!$error) {
        if($bg!=="transparent"){
            $canvas = $manager->canvas($img->width(), $img->height(), "#$bg");
            $canvas->insert($img);
            $img = $canvas;
        }
    }

    if(!$error){
        if($mode==="cover"){
            $img->fit($w, $h);
        }else{
            $img->resize($w, $h,function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }

        the()->fileSystem->prepareDir(the()->route);
        //$img->encode("webp","10");
        $img->save(the()->route,$quality);
    }
}else{
    $error="malformed";
}
if($error){
    $img=$manager->canvas(500,500,'#888888');
    $img->text($error, 10, 20, function($font) {
        $font->file(3);
        $font->size(40);
        $font->color('#334466');
        $font->align('left');
        $font->valign('left');
    });
}
echo $img->response();

//echo implode("<br>",$out);


