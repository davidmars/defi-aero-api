<?php

namespace Models;

use RedBeanPHP\SimpleModel;
use Utils\The;

/**
 * Un évènement
 * @property string $name Le nom lisible pour les humains
 * @property string $pwd pwd pour voir cet event
 * @property string $slug Le nom technique ( a-z 0-9 - ) utilisé dans les urls par exemple. Ce slug est déduit du name
 * @property string $paththumbnail Chemin local vers le fichier de thumbnail
 */
class Event extends BaseModel
{
    /**
     * Efface les photos de l'Event
     * @return void
     */
    public function delete() {
        foreach ($this->photos() as $p){
            db()->trash($p);
        }
        //TODO delete thumbnail + caches
    }

    /**
     * 
     * @param $text
     * @return string
     */
    private static function getSlug($text){
        $divider="-";
        // replace non letter or digits by divider
        $text = preg_replace('~[^\pL\d]+~u', $divider, $text);

        // transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, $divider);

        // remove duplicate divider
        $text = preg_replace('~-+~', $divider, $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

    /**
     * Renvoie les photos de l'event
     * @return Photo[]
     */
    public function photos(){
        $list= db()->find("photo","event_id = ? ORDER BY date_created",[$this->id]);
        $r=[];
        foreach ($list as $p){
            $r[]=$p;
        }
        return $r;
    }

    /**
     * Renvoie le poids total de l'event
     * @return integer
     */
    public function bytes(){
        $bytes=0;
        foreach ($this->photos() as $photo){
            $bytes+=$photo->bytes;
        }
        return $bytes;
    }

    /**
     * Renvoie et crée si besoin l'event dont le name est fourni
     * @param $name
     * @param bool $createIfNull
     * @return Event|null
     */
    public static function getByName($name, $createIfNull=false){
        $slug=self::getSlug($name);
        /** @var Event $e */
        $e= self::getBySlug($slug);
        if(!$e && $createIfNull){
            /** @var Event $e */
            $e=db()->dispense("event");
            $e->name=$name;
            $e->slug=$slug;
            db()->store($e);
        }
        return $e;
    }

    /**
     * @param $slug
     * @return Event|null
     */
    public static function getBySlug($slug)
    {
        return db()->findOne("event","slug = ? ",[$slug]);
    }

    /**
     * Renvoie les events classés par ordre alphabétique
     * @return Event[]
     */
    public static function all(){
        return db()->find("event","ORDER BY name");
    }

    /**
     * Renvoie le lien vers la page de l'event'
     * @return string
     */
    public function hrefPage(){
        return the()->fmkHref(true,true,"event/$this->slug");
    }
    public function hrefZip()
    {
        return the()->fmkHref(true,true,"event/export-zip/$this->slug");
    }
    public function hrefCsv()
    {
        return the()->fmkHref(true,true,"event/export-csv/$this->slug");
    }

    public function dateStart($format)
    {
        $arr=$this->photos();
        $el=array_shift($arr);
        if(!$el){
            $el=$this;
        }
        if($el){
            return utils()->date->formatFromString($el->date_created,$format);
        }
    }
    public function dateEnd($format)
    {
        $arr=$this->photos();
        $el=array_pop($arr);
        if(!$el){
            $el=$this;
        }
        if($el){
            return utils()->date->formatFromString($el->date_created,$format);
        }
    }

    /**
     * Renvoie true si l'utilisateur est admin ou loggué à cet event
     * @return bool
     */
    public function isVisible()
    {
        \the()->sessionStart();
        if(\the()->theUserIsAdmin()){
            return true;
        }
        if(isset($_SESSION["event-connected-$this->id"])){
            if($_SESSION["event-connected-$this->id"]===true){
                return true;
            }
        }
        return false;

    }

    /**
     * renvoie les variables à exporter dans le xls pour chaque photo
     * @return string[]
     */
    public function getVariables(){
        $variables=[
            "id","slug","date","email","url page","url téléchargement","poids (octets)","poids","event"
        ];
        foreach ($this->photos() as $photo){
            $obj= json_decode($photo->json,true);
            if($obj){
                $variables=array_unique(
                    array_merge(
                        $variables,
                        array_keys($obj)

                    )
                );
            }

        }
        return $variables;
    }

    /**
     * Teste si le pwd est correct et logue l'utilisateur au besoin
     * @param $pwd
     * @return bool
     */
    public function passwordIsCorrect($pwd){
        if(\the()->passwordIsCorrect($pwd)){
            return true;
        }
        \the()->sessionStart();
        if($pwd && $pwd === $this->pwd){
            $_SESSION["event-connected-$this->id"] = true;
            return true;
        }
        $_SESSION['isAdmin'] = false;
        return false;
    }

    /**
     * Renvoie et définit et enregistre au besoin le pwd
     * @return string
     */
    public function getPwd()
    {
        if(!$this->pwd){
           $this->pwd=utils()->string->random(6);
           db()->store($this);
        }
        return $this->pwd;
    }


    /**
     * Renvoie une url de la thumbnail resizée
     * @param int $width
     * @param int $height
     * @param string $mode
     * @param string $backgroundColor
     * @param int $quality
     * @param null $extension
     * @return string|null
     */
    public function resizeUrl($width=300,$height=300,$mode="contain",$backgroundColor="transparent",$quality=80,$extension="jpg",$absolute=true){
        if(!$this->paththumbnail){
            return \the()->fileSystem->filesystemToHttp("pub/logo.png",true);
        }
        if(!$mode){
            $mode="contain";
        }
        if(!$backgroundColor){
            $backgroundColor="808080";
        }
        if($extension==="jpeg"){
            $extension="jpg";
        }
        $base=str_replace("fs/up","im/fs/up",$this->paththumbnail);


        $path= "$base/im-$width"."x".$height."-".$mode."-".$backgroundColor."-".$quality.".".$extension;
        if($absolute){
            return the()->fileSystem->filesystemToHttp($path,$absolute);
        }
        return $path;
    }


}