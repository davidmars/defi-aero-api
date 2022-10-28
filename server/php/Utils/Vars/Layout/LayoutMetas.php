<?php


namespace Utils\Vars\Layout;


class LayoutMetas
{
    /**
     * @var string Le titre de la page html
     */
    public $title="";
    /**
     * @var string La description de la page html
     */
    public $description="";
    /**
     * @var string to index or not to index this is the question
     */
    public $robots="index,follow";

    /**
     * @var string Lien canonique si nécessaire
     */
    public $canonical="";
    /**
     * @var string Le lien vers le sitemap
     */
    public $sitemap="";
    /**
     * @var string Code iso de localisation
     */
    public $language="";
    /**
     * @var string données json ld libres
     */
    public $jsonld;


    public function disableSEO(){
        $this->robots="noindex, nofollow";
    }
    public function enableSEO(){
        $this->robots="index,follow";
    }

    public function __construct()
    {
    }
}