<?php


namespace Utils\Vars\Layout;


class LayoutOG
{
    public $title;
    public $description;
    public $url;
    public $type;

    private $image;
    /**
     * @var string
     */
    private $image_width="";
    /**
     * @var string
     */
    private $image_height="";
    /**
     * @var string
     */
    private $image_type="";

    /**
     * @return string
     */
    public function getImageWidth(): string
    {
        return $this->image_width;
    }

    /**
     * @return string
     */
    public function getImageHeight(): string
    {
        return $this->image_height;
    }

    /**
     * @return string
     */
    public function getImageType(): string
    {
        return $this->image_type;
    }


    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image): void
    {
        if(file_exists($image)){
            list($width, $height, $type, $attr) = getimagesize($image);
            $this->image_width=$width;
            $this->image_height=$height;
            $this->image_type=mime_content_type($image);
            $this->image=the()->fileSystem->filesystemToHttp($image,true);
        }
        //$this->image = $image;
    }
}