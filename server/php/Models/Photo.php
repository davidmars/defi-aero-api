<?php

namespace Models;

use RedBeanPHP\SimpleModel;

/**
 * Une photo dans la base de données
 * @property string $path Chemin local vers le fichier
 * @property string $slug Chemin local vers la page
 * @property string $email email de la personne
 * @property string $json variables json en plus des autres champs
 * @property string $event_name; Nom de l'event au moment où la photo a été enregistrées
 * @property Event|null $event; Modèle de l'event
 * @property int $event_id; Identifiant de liaison vers le modèle de l'event
 * @property int $bytes Poids de la photo
 */
class Photo extends BaseModel
{

    function update(){

        $this->bytes=filesize($this->path);
        $this->event=Event::getByName($this->event_name,true);
        if(!$this->id){
            $this->date_created=date("Y-m-d H:i:s");
        }
        if(!$this->slug){
            $this->slug=strtolower(utils()->string->random()."-".time());
        }
    }

    /**
     * Efface la photo en même temps que le record de la BDD
     * @return void
     */
    public function delete() {

        if(is_file($this->path)){
            unlink($this->path);
        }
        if(is_dir("im/$this->path")){
            utils()->files->rrmdir("im/$this->path");
        }
    }

    /**
     * Url de téléchargement de la photo
     * @return string
     */
    public function hrefDownload(){
        return the()->fmkHref(true,true,"dwd-photo-$this->slug");
    }

    /**
     * Renvoie le lien vers la page de la photo
     * @return string
     */
    public function hrefPage($absolute=false){
        return the()->fmkHref($absolute,$absolute,"photo-$this->slug");
    }

    /**
     * Renvoie une url de photo resizée
     * @param int $width
     * @param int $height
     * @param string $mode
     * @param string $backgroundColor
     * @param int $quality
     * @param null $extension
     * @return string|null
     */
    public function resizeUrl($width=300,$height=300,$mode="contain",$backgroundColor="transparent",$quality=80,$extension="jpg",$absolute=true){

            if(!$mode){
                $mode="contain";
            }
            if(!$backgroundColor){
                $backgroundColor="808080";
            }
            if($extension==="jpeg"){
                $extension="jpg";
            }

            $base=str_replace("fs/up","im/fs/up",$this->path);
            $path= "$base/im-$width"."x".$height."-".$mode."-".$backgroundColor."-".$quality.".".$extension;
            if($absolute){
                return the()->fileSystem->filesystemToHttp($path,$absolute);
            }
            return $path;
    }

    /**
     * Renvoie une photo par son slug
     * @param string $slug
     * @return Photo|null
     */
    public static function getBySlug($slug)
    {
        return db()->findOne("photo","slug = ? ",[$slug]);
    }

    /**
     * @return string
     */
    public function fileName()
    {
        $ext = pathinfo($this->path, PATHINFO_EXTENSION);
        return $this->event->slug." - ".utils()->date->formatFromString($this->date_created,"Y-m-d His").".$ext";
    }


    /**
     * @param string[] $variables
     * @return array
     */
    public function getExport($variables)
    {
        $arr=[];
        foreach ($variables as $var){
            $arr[$var]=$this->getVariable($var);
        }
        return $arr;
    }

    private function getVariable($var){
        if(isset($this->$var)){
            return $this->$var;
        }
        switch ($var){
            case "date";
                return $this->date_created;
            case "url page";
                return $this->hrefPage(true);
            case "url téléchargement";
                return $this->hrefDownload(true);
            case "poids (octets)";
                return $this->bytes * 1.0;
            case "poids";
                return utils()->files->humanFileSize($this->bytes);;
            case "event";
                return $this->event_name;
        }

        $json=json_decode($this->json,true);
        if($json && isset($json[$var])){
            return $json[$var];
        }

        return "";
    }


}