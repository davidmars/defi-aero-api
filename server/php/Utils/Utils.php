<?php

namespace Utils;

use Utils\Abs\AbstractSingleton;

class Utils extends AbstractSingleton
{
    /**
     * @var FileUtils
     */
    public $files;
    /**
     * @var StringUtils
     */
    public $string;
    /**
     * @var DateUtils
     */
    public $date;
}

Utils::inst()->string=StringUtils::inst();
Utils::inst()->date=DateUtils::inst();
Utils::inst()->files=FileUtils::inst();
