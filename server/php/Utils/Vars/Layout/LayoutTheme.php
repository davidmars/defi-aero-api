<?php


namespace Utils\Vars\Layout;

/**
 * Permet de definir des options de themes sur la page HTML
 * @package EE09\Core
 */
class LayoutTheme
{

    /**
     * @var string
     */
    private $color="FFFFFF";

    /**
     * @return string Renvoie le code hexa à utiliser
     */
    public function getColor()
    {
        return "#".$this->color;
    }

    public function setColor($color)
    {
        $this->color=trim($color,"# ");
    }

    /**
     * @var String image url
     */
    private $favicon;

    /**
     * @return String
     */
    public function getFavicon()
    {
        return $this->favicon;
    }

    /**
     * @param string $favicon
     */
    public function setFavicon($favicon): void
    {
        if(file_exists($favicon)){
            $this->favicon=the()->fileSystem->filesystemToHttp($favicon,true);
        }
    }

}