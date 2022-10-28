<?php

namespace Utils;


use Utils\Abs\AbstractSingleton;

/**
 * Quelques méthodes pratiques pour gérer des chaines de caractères.
 *
 * @package Pov\Utils
 * @method static StringUtils inst()
 */
class StringUtils extends AbstractSingleton {

    const FORMAT_NO_HTML_SINGLE_LINE = "STRING_FORMAT_NO_HTML_SINGLE_LINE";
    const FORMAT_NO_HTML_MULTI_LINE = "STRING_FORMAT_NO_HTML_MULTI_LINE";
    const FORMAT_HTML = "STRING_FORMAT_HTML";

    /**
     * Generate a random string, using a cryptographically secure
     * pseudorandom number generator (random_int)
     *
     * For PHP 7, random_int is a PHP core function
     * For PHP 5.x, depends on https://github.com/paragonie/random_compat
     *
     * @param int $length      How many characters do we want?
     * @param string $keySpace A string of all possible characters
     *                         to select from
     * @return string
     * @see https://stackoverflow.com/questions/4356289/php-random-string-generator/31107425#31107425
     */
    public function random($length=20, $keySpace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
    {
        $str = '';
        $max = strlen($keySpace) - 1;
        for ($i = 0; $i < $length; ++$i) {
            $str .= $keySpace[rand(0, $max)];
        }
        return $str;
    }

    /**
     * Tente d'arranger un code html
     * @param string $html Code HTML possiblement cassé
     * @return string le code html
     */
    public function fixHtml($html){
        $doc = new \DOMDocument;
        @$doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $nodes = $doc->getElementsByTagName('body')->item(0);
        if($nodes){
            $nodes = $nodes->childNodes;
        }
        if($nodes){
            $clean = '';
            $len = $nodes->length;
            for ($i = 0; $i < $len; $i++) {
                $clean .= $doc->saveHTML($nodes->item($i));
            }
        }else{
            $clean=$html;
        }
        return $clean;
    }

    /**
     * Que des a-z A-Z et tirets
     * @param $string
     * @param string $allowed liste des charactères autorisés (en dehors de A-Z 0-9 - )
     * @return null|string|string[] $string
     */
    public function clean($string,$allowed="") {


        $utf8 = [
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N',
            '/–/'           =>   '-', // UTF-8 hyphen to "normal" hyphen
            '/[’‘‹›‚]/u'    =>   ' ', // Literally a single quote
            '/[“”«»„]/u'    =>   ' ', // Double quote
            '/ /'           =>   ' ', // nonbreaking space (equiv. to 0x160)
        ];
        $string = preg_replace(array_keys($utf8), array_values($utf8), $string);
        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-'.preg_quote($allowed,"/").']/', '', $string); // Removes special chars.
        return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
    }

    /**
     * Retourne le texte sans accents
     * @param $string
     * @return null|string|string[]
     */
    public function noAccents($string){
        $utf8 = [
            '/[áàâãªä]/u'   =>   'a',
            '/[ÁÀÂÃÄ]/u'    =>   'A',
            '/[ÍÌÎÏ]/u'     =>   'I',
            '/[íìîï]/u'     =>   'i',
            '/[éèêë]/u'     =>   'e',
            '/[ÉÈÊË]/u'     =>   'E',
            '/[óòôõºö]/u'   =>   'o',
            '/[ÓÒÔÕÖ]/u'    =>   'O',
            '/[úùûü]/u'     =>   'u',
            '/[ÚÙÛÜ]/u'     =>   'U',
            '/ç/'           =>   'c',
            '/Ç/'           =>   'C',
            '/ñ/'           =>   'n',
            '/Ñ/'           =>   'N'
        ];
        $string = preg_replace(array_keys($utf8), array_values($utf8), $string);
        return $string;
    }

    /**
     * To transform abcdlkjlkjk@hotmail.com in abcdl*****@hotmail.com
     * @param string $email
     * @return string
     */
    public function obfuscateEmail($email)
    {
        $em   = explode("@",$email);
        $name = implode(array_slice($em, 0, count($em)-1), '@');
        $len  = floor(strlen($name)/2);

        return substr($name,0, $len) . str_repeat('*', $len) . "@" . end($em);
    }

    /**
     * Format a text to be used in attributes elements (no special chars and linebreaks)
     * @param string $text
     * @param bool $preventNewLines when set to false newlines are not escaped
     * @return string
     */
    public function forAttributes($text,$preventNewLines=true){
        if($preventNewLines){
            $text=preg_replace( "/\r|\n/", " ", $text );
        }
        return htmlspecialchars($text);
    }

    /**
     * Return a random text that contains a certain number of words.
     * @param int $minimum Minimum number of words
     * @param int $maximum Maximum number of words
     * @return string A random text.
     */
    public function loremIspum($minimum=1,$maximum=null){
        if($maximum===null){
            $maximum=$minimum;
        }
        if(!$this->lorem){
            $this->lorem=file_get_contents(__DIR__."/loremIpsum.txt");
            $this->lorem=explode(" ",$this->lorem);

            $this->lorem=array_merge($this->lorem,$this->lorem,$this->lorem,$this->lorem,$this->lorem,$this->lorem,$this->lorem,$this->lorem,$this->lorem);
            shuffle($this->lorem);
        }
        $length=rand($minimum,$maximum);
        $start=rand(0,count($this->lorem)-$length);

        $ar=array_slice($this->lorem,$start,$length);
        return implode(" ",$ar);

    }
    private $lorem=[];


    /**
     * According the value of count will return one of the 3 possibles values.
     * Tip: In the sentence the string xxxCountxxx will be replaced with the count of the array
     * @param int|array $count Number to determine plural or not
     * @param string $ifOne
     * @param string $ifMoreThanOne If not set, will be $ifOne with an "S" at the end
     * @param string $ifZero If not set will be equal to $ifMoreThanOne
     * @return string
     */
    public function plural($count, $ifOne,$ifMoreThanOne=null,  $ifZero=null)
    {
        if(is_array($count)){
            $count=count($count);
        }
        if(!$ifMoreThanOne){
            $ifMoreThanOne=$ifOne."s";
        }
        if(!$ifZero){
            $ifZero=$ifMoreThanOne;
        }
        switch(true){
            case $count=="0":
                $r= $ifZero;
                break;
            case $count=="1":
                $r= $ifOne;
                break;
            default:
                $r= $ifMoreThanOne;
        }

        $r=str_replace("xxxCountxxx",$count,$r);
        return $r;
    }

}