<?php

namespace Utils;


use DateTime;
use Utils\Abs\AbstractSingleton;


/**
 * Class DateUtils permet de manipuler des dates
 * @method static DateUtils inst()
 */
class DateUtils extends AbstractSingleton
{
    public $transations=[
        "fr"=>[
           "DAYS"=> [
               "Monday"=>"Lundi",
               "Mon"=>"Lun",
               "Tuesday"=>"Mardi",
               "Tue"=>"Mar",
               "Wednesday"=>"Mercredi",
               "Wed"=>"Mer",
               "Thursday"=>"Jeudi",
               "Thu"=>"Jeu",
               "Friday"=>"Vendredi",
               "Fri"=>"Ven",
               "Saturday"=>"Samedi",
               "Sat"=>"Sam",
               "Sunday"=>"Dimanche",
               "Sun"=>"Dim"
           ],
           "MONTHS"=> [
               "January"=>"Janvier",
               "February"=>"Février",
               " Feb "=>" Fév ",
               "March"=>"Mars",
               "April"=>"Avril",
               " Apr "=>" Avr ",
               "May"=>"Mai",
               "June"=>"Juin",
               "July"=>"Juillet",
               "August"=>"Août",
               "September"=>"Septembre ",
               "October"=>"Octobre",
               "November"=>"Novembre ",
               "December"=>"Décembre ",
           ],
           "MISC"=> [
               "now"=>"à l'instant",
               "Now"=>"À l'instant",
               "Today"=>"Aujourd'hui",
               "Yesterday"=>"Hier",
               "Tomorow"=>"Demain",
               "days"=>"jours"
           ],
           "REGS"=> [
               "/([0-9]*) minute ago/"=>"il y a $1 minute",
               "/([0-9]*) minutes ago/"=>"il y a $1 minutes",
               "/([0-9]*) hours ago/"=>"il y a $1 heures",
               "/([0-9]*) days ago/"=>"il y a $1 jours"
           ],
        ]
    ];

    /**
     * @param string $date Un texte en angalis comprenant des mots tels que lundi, mardi etc...
     * @param string $langcode "fr" par défaut
     * @return mixed
     */
    public function translate($date,$langcode="fr"){
        foreach ($this->transations[$langcode]["REGS"] as $k=>$v){
            $date=preg_replace("$k",$v,$date);
        }
        foreach ($this->transations[$langcode]["DAYS"] as $k=>$v){
            $date=str_replace($k,$v,$date);
        }
        foreach ($this->transations[$langcode]["MONTHS"] as $k=>$v){
            $date=str_replace($k,$v,$date);
        }
        foreach ($this->transations[$langcode]["MISC"] as $k=>$v){
            $date=str_replace($k,$v,$date);
        }

        return $date;
    }

    /**
     * Renvoie la date qui correspond à cette chaine
     * @param String $str
     * @return DateTime
     * @throws \Exception
     */
    public function dateFromString($str){
        $t=strtotime($str);
        return DateTime::createFromFormat("U",$t);
    }
    /**
     * Renvoie une date formatée à partir d'un texte qui ressemble à une date
     * @param string $dateString une date en texte compréhensible par strtotime
     * @param string $format "Y m d H:i:s" par exemple
     * @link http://php.net/manual/en/function.date.php
     * @link http://php.net/manual/en/function.strtotime.php
     * @return false|string
     */
    public function formatFromString($dateString, $format="Y m d H:i:s"){
        return date($format,strtotime($dateString));
    }

    /**
     * @return int Le timestamp présent
     */
    public function timestamp(){
        $d=new \DateTime();
        return $d->getTimestamp();
    }

    /**
     * Renvoie le temps courrant au format Y-m-d H:i:s
     * @return false|string
     */
    public function nowMysql()
    {
        return date("Y-m-d H:i:s");
    }

    /**
     * Maintenant sous forme de DateTime
     * @return DateTime
     */
    public function now(){
        try{
            $d=new DateTime();
        }catch (\Exception $e){}

        return $d;
    }
    /**
     * Renvoie la date il y a X heures
     * @param int $hours Nombre d'heures à retirer
     * @return DateTime
     */
    public function xHoursAgo($hours){
        try{
            $d=$this->now()->sub(new \DateInterval("PT".$hours."H"));
        }catch (\Exception $e){}
        return $d;
    }
    /**
     * Renvoie la date dans X heures
     * @param int $hours Nombre d'heures à ajouter
     * @return DateTime
     */
    public function xHoursLater($hours){
        try{
            $d=$this->now()->add(new \DateInterval("PT".$hours."H"));
        }catch (\Exception $e){}
        return $d;
    }
    /**
     * Renvoie la date il y a X minutes
     * @param int $minutes Nombre d'heures à retirer
     * @return DateTime
     */
    public function xMinutesAgo($minutes){
        try{
            $d=$this->now()->sub(new \DateInterval("PT".$minutes."M"));
        }catch (\Exception $e){}
        return $d;
    }
    /**
     * Renvoie la date dans X minutes
     * @param int $minute Nombre de minutes à ajouter
     * @return DateTime
     */
    public function xMinutesLater($minute){
        try{
            $d=$this->now()->add(new \DateInterval("PT".$minute."M"));
        }catch (\Exception $e){}
        return $d;
    }


    /**
     * Renvoie la date il y a X jours
     * @param int $days Nombre de jours à retirer
     * @return DateTime
     */
    public function xDaysAgo($days){
        try{
            $d=$this->now()->sub(new \DateInterval("P".$days."D"));
        }catch (\Exception $e){}
        return $d;
    }
    /**
     * Renvoie la date dans X jours
     * @param int $days Nombre de jours à ajouter
     * @return DateTime
     */
    public function xDaysLater($days){
        try{
            $d=$this->now()->add(new \DateInterval("P".$days."D"));
        }catch (\Exception $e){}
        return $d;
    }

    /**
     * Detecte si la date demandée est hier, aujourd'hui ou demain
     * @param DateTime $date
     * @return string today | yesterday | tomorow ou rien
     */
    public function isYesterdayTodayTomorow($date){
        //détecter aujourd'hui hier et demain...
        $today = new \DateTime(); // This object represents current date/time
        $today->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison
        $match_date = $date;
        $match_date->setTime( 0, 0, 0 ); // reset time part, to prevent partial comparison
        $diff = $today->diff( $match_date );
        $diffDays = (integer)$diff->format( "%R%a" ); // Extract days count in interval
        $particular="";
        switch( $diffDays ) {
            case 0:
                return "today";
            case -1:
                return "yesterday";
            case +1:
                return "tomorow";
            default:
                return "";
        }
    }

    /**
     * Formate la date dasn la langue spécifiée
     * @param \DateTime $dateTime
     * @param string $format something like "l d M Y" to get something like lundi 31 décembre 2014
     * @return string
     */
    public function formatLocale($dateTime, $format, $langCode="fr_FR"){
        if(!is_a($dateTime,"DateTime")){
            return $format;
        }
        $ts=$dateTime->getTimestamp();
        setlocale(LC_TIME, "$langCode.UTF-8");
        return strftime($this->dateFormatToStrftime($format),$dateTime->getTimestamp());
    }

    /**
     * Convert a classic date format expression in strftime format
     * @param string $dateFormat Something like
     * @return string
     */
    private function dateFormatToStrftime($dateFormat) {

        $caracs = [
            // Day - no strf eq : S
            'd' => '%d', 'D' => '%a', 'j' => '%e', 'l' => '%A', 'N' => '%u', 'w' => '%w', 'z' => '%j',
            // Week - no date eq : %U, %W
            'W' => '%V',
            // Month - no strf eq : n, t
            'F' => '%B', 'm' => '%m', 'M' => '%b',
            // Year - no strf eq : L; no date eq : %C, %g
            'o' => '%G', 'Y' => '%Y', 'y' => '%y',
            // Time - no strf eq : B, G, u; no date eq : %r, %R, %T, %X
            'a' => '%P', 'A' => '%p', 'g' => '%l', 'h' => '%I', 'H' => '%H', 'i' => '%M', 's' => '%S',
            // Timezone - no strf eq : e, I, P, Z
            'O' => '%z', 'T' => '%Z',
            // Full Date / Time - no strf eq : c, r; no date eq : %c, %D, %F, %x
            'U' => '%s'
        ];

        return strtr((string)$dateFormat, $caracs);
    }

    /**
     * Teste si la date donnée est passée
     * @param \DateTime $dateTime
     * @return bool
     */
    public function isPast($dateTime){
        $d=new \DateTime();
        if($d->getTimestamp()>$dateTime->getTimestamp()){
            return true;
        }
    }


    /**
     * To get a smart date display
     * @param DateTime $dateTime
     * @param bool $precise when set to false, will not display hours after 24h
     * @return string
     */
    public function timeAgo($dateTime,$precise=true){

        $now=new DateTime();
        $dif=$dateTime->diff($now);

        //less than one hour
        if($now->getTimestamp()-$dateTime->getTimestamp() < 3600){
            //less than one minute
            if($dif->i<1){
                return "now";
            }
            //one minute
            if($dif->i==1){
                return $dif->i." minute ago";
            }
            //less than one hour
            if($dif->h<1){
                return $dif->i." minutes ago";
            }
        }

        //round to manage hours in the day and get "human" understanding of 1 day, 2 days etc...
        $now->setTime(0,0,2);
        $dateTimeRound=clone $dateTime;
        $dateTimeRound->setTime(0,0,1);
        $dif=$dateTimeRound->diff($now);

        if($dif->days < 1 && $now->format("l")==$dateTime->format("l")){
            $r="Today";
        }elseif($dif->days < 2 ){
            //yesterday
            $r="Yesterday";
        }elseif($dif->days<7){
            //less than a week
            $r=$dif->days." days ago";
        }else{
            $r=$dateTime->format("d ").$dateTime->format("M");
            if($dateTime->format("Y")!=$now->format("Y")){
                $r.=" ".$dateTime->format("Y");
            }
        }

        if($precise){
            $r.=$dateTime->format(" H:i");
        }

        return $r;


    }




}