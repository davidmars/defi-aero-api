<?php
namespace Utils;

use Utils\Abs\AbstractSingleton;
use Utils\Vars\EnvVariables;
use Utils\Vars\Layout;

/**
 * La classe "The" permet de centraliser les singletons les plus courants
 */
class The extends AbstractSingleton {
    /**
     * @var float microtime quand le script a été lancé
     */
    public $startTime=0;

    /**
     * @var EnvVariables
     */
    public $env;
    /**
     * @var string url relative par défaut pour accéder au fmk (la plupart du temps ça ne sert à rien, mais si le fmk est installé dans un sous répertoire, ça prend tout son sens)
     */
    public $fmkHttpRoot="";
    /**
     * @var Header Le header courant qui sera renvoyé
     */
    public $headerOutput;
    /**
     * @var FileSystem Le système de fichiers rattaché à l' url du projet
     */
    public $fileSystem;
    /**
     * Eventuellement un objet de réponse api
     * @var null|ApiResponse
     */
    public $apiResponse=null;

    /**
     * @var DbConf objet de connexion à la BDD
     */
    public $dbConf;

    //-----------layout stuff-----------------------

    /**
     * @var Layout Quelques infos propres au layout html
     */
    public $layout;

    /**
     * Variables transmises à javascript au moment du chargement de celui-ci
     */
    public $htmlVars=[];


    //-------route / controller ------------------


    /**
     * @var string La route passée par le .htaccess
     */
    public $route="";

    /**
     * @var Mailer To send emails
     */
    public $mailer=null;
    /**
     * @var string le mot de passe
     */
    private $pwd;


    /**
     * Renvoie un paramètre que l'on peut trouver dans $_REQUEST
     * @param string $queryParam Le paramètre Get ou Post
     * @param mixed   $ifNull La valeur à renvoyer si null ou indéfini
     * @return string|array|null
     */
    public function request($queryParam,$ifNull=null)
    {
        if(isset($_REQUEST[$queryParam])){
            if(is_array($ifNull)){
                $checked = $_REQUEST[$queryParam];
                $ret=[];
                for($i=0; $i < count($checked); $i++){
                    $ret[]=$checked[$i];
                }
                return $ret;
            }
            return $_REQUEST[$queryParam];
        }
        return $ifNull;
    }

    /**
     * Renvoie l' url saisie dans le navigateur https://sdd.ndd.com/mon-chemin-complet
     * Cette url n' est pas traitée, c'est bien la requête initiale qui est analysée et retournée ici
     * @param boolean $http doit-on retourner http(s):// ?
     * @param boolean $domain doit on retourner ndd.com ? (sera forcément true si on choisit de retourner $http
     * @return string Un truc du genre https://sdd.ndd.com/mon-chemin-complet
     */
    public function requestHref($http=true, $domain=true): string
    {
        $scheme=isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host=$_SERVER['HTTP_HOST'];
        $request=$_SERVER['REQUEST_URI'];
        $parts=[];
        if($http){
            $domain=true; // avoir http sans le ndd n'a pas de sens
            $parts[]=$scheme.'://';
        }
        if($domain){
            $parts[]=$host;
        }
        $parts[]=$request;
        return  implode($parts);
    }

    /**
     * Renvoie l' url publique du fmk
     * @param boolean $http doit-on retourner http(s):// ?
     * @param boolean $domain doit on retourner ndd.com ? (sera forcément true si on choisit de retourner $http
     * @return string Un truc du genre https://sdd.ndd.com/mon-chemin-complet
     */
    public function fmkHref($http=true, $domain=true,$path=""): string
    {
        $scheme=isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http";
        $host=$_SERVER['HTTP_HOST'];
        $request=$this->fmkHttpRoot;
        $parts=[];
        if($http){
            $domain=true; // avoir http sans le ndd n'a pas de sens
            $parts[]=$scheme.'://';
        }
        if($domain){
            $parts[]=$host."/";
        }
        if($request){
            $parts[]=$request."/";
        }
        if($path){
            $parts[]="".trim($path,"/");
        }
        return  implode($parts);
    }

    /**
     * Temps d'exécution au moment de l'appel
     * @return float
     */
    public function time(){
        return microtime(true) - $this->startTime ;
    }

    public function __construct(){
        $this->fmkHttpRoot=preg_replace(
            "#/index.php$#",
            "",
            $_SERVER["SCRIPT_NAME"]
        );
        $this->fmkHttpRoot=trim($this->fmkHttpRoot,"/");
        parent::__construct();
    }

    public function sessionStart(){
        if(session_status()!==PHP_SESSION_ACTIVE){
            session_start();
        }
    }
    /**
     * Dit si l'utilisateur est admin ou non
     * @return bool
     */
    public function theUserIsAdmin(){
        $this->sessionStart();
        if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin']=== true){
            return true;
        }
        return false;
    }

    /**
     * Teste si le mot de passe est correct.
     * Si il est correct, loggue l'utilisateur, sinon le déloggue
     * @param string|false $pwd
     * @return boolean
     */
    public function passwordIsCorrect($pwd)
    {
        $this->sessionStart();
        if($pwd && $pwd === $this->pwd){
            $_SESSION['isAdmin'] = true;
            return true;
        }
        $_SESSION['isAdmin'] = false;
        return false;
    }

    /**
     * Permet de définir le mot de passe
     * @param string $pwd
     * @return void
     */
    public function passwordIs(string $pwd)
    {
        $this->pwd=$pwd;
    }


}

The::inst()->layout=new Layout();
The::inst()->mailer=new Mailer();
The::inst()->env=new EnvVariables();
The::inst()->dbConf=new DbConf();
The::inst()->startTime=microtime(true);





