<?php

namespace Utils\Vars;

use Utils\Vars\Layout\LayoutMetas;
use Utils\Vars\Layout\LayoutOG;
use Utils\Vars\Layout\LayoutTheme;
use Utils\Vars\Layout\LayoutTwitterCard;

/**
 * Variables qui vont bien pour gérer une page HTML
 */
class Layout
{
    public $googleAnalyticsId="";
    /**
     * @var LayoutMetas
     */
    public $meta;
    /**
     * @var LayoutTheme
     */
    public $theme;
    /**
     * @var LayoutOG
     */
    public $og;
    /**
     * @var LayoutTwitterCard
     */
    public $twitterCard;


    public function __construct()
    {
        $this->meta=new LayoutMetas();
        $this->theme=new LayoutTheme();
        $this->og=new LayoutOG();
        $this->twitterCard=new LayoutTwitterCard();

    }
}