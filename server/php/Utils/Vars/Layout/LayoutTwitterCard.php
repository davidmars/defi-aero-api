<?php


namespace Utils\Vars\Layout;

class LayoutTwitterCard
{
    /**
     * @var String Bla bla
     */
    public $title;
    /**
     * @var String Bla bla
     */
    public $description;
    /**
     * @var String summary
     */
    public $card;
    /**
     * @var @TWITTER_ID
     */
    public $site;
    /**
     * @var @creator
     */
    public $creator;
    /**
     * @var String image url
     */
    private $image;

    /**
     * @return String
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Permet de définir un fichier qui ne sera ajouté à la carte que si le fichier est valide
     * @param string $image
     */
    public function setImage($image): void
    {
        if(file_exists($image)){
            $this->image=the()->fileSystem->filesystemToHttp($image,true);
        }
    }

    /**
     * Permet de définir directement une url d'image sans vérification
     * @param $image
     */
    public function setImageUrl($image){
        $this->image=$image;
    }
}