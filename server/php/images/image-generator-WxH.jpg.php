<?php
$photo=\Models\Photo::getBySlug($slug);
if(!$photo){
    die("err");
}
$url=$photo->resizeUrl($w,$h,"contain","000000",80,$extension,true);
header("Content-type: image/$extension");
echo file_get_contents($url);
die();

