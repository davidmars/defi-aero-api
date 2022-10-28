<?php
set_time_limit(120);
//setting php
error_reporting(E_ALL);
ini_set('display_errors', E_ALL);

require 'vendor/autoload.php';

use Utils\Utils;
use Utils\FileSystem;
use Utils\Header;
use Utils\SingletonRedBeanFacade;
use Utils\The;
use Utils\View;



//conf des views
View::$possiblesPath[]=__DIR__."/v";

/**
 * La vue courante (Autocompletion dans les vues pour éviter de documenter $this dans chaque vue)
 */
$view=new View("");

/**
 * Permet d'accéder à des instances et définitions globales
 * @return The
 */
function the(){
    return The::inst();
}
/**
 * Accès rapide vers les classes utilitaires
 * @return Utils
 */
function utils(){
    return Utils::inst();
}

if(isset($_GET["route"])){
    the()->route=$_GET["route"];
}
if(!the()->route){
    the()->route="index";
}

//conf header
the()->headerOutput=new Header();
the()->headerOutput->enableCORS();

//conf globale
the()->fileSystem=new FileSystem();


require_once __DIR__."/../config.php";


//conf bdd ?

/**
 * La base de données du projet
 * @return SingletonRedBeanFacade
 */
function db(){
    return SingletonRedBeanFacade::inst();
}
if(\the()->dbConf->dsn){
    define( 'REDBEAN_MODEL_PREFIX', '\\Models\\' );
    db()->setup(
        the()->dbConf->dsn,
        the()->dbConf->username,
        the()->dbConf->password,
        the()->dbConf->frozen
    );
}

//------------------------contrôleur------------------------------


// logout ?
if(\the()->request('logout')==='1'){
    \the()->passwordIsCorrect(false);
    \the()->headerOutput->setRedirect(\the()->fmkHref(true,true,"admin"));
}

//route = view ?
switch (true){
    //route = v/etc/etc.php ?
    case View::isValid(\the()->route):
        $v=new View(\the()->route,[]);
        break;

    // image generator
    case preg_match('#^im\/.*\/im-#m',\the()->route):
        include __DIR__."/images/image-generator.php";
        die();
        break;

    // image generator alias
    case preg_match('#^photo-([a-z0-9\-]+)/(\d+)x(\d+).([a-z]+)$#m',\the()->route,$m):
        $slug=$m[1];
        $w=$m[2];
        $h=$m[3];
        $extension=$m[4];
        include __DIR__."/images/image-generator-WxH.jpg.php";
        break;

    //page photo
    case preg_match('#^photo-([a-z0-9\-]+)$#m',\the()->route,$m):
        $v=new View("html/photo",["slug"=>$m[1]]);
        break;

    //dwd photo
    case preg_match('#^dwd-photo-([a-z0-9\-]+)$#m',\the()->route,$m):
        $v=new View("dwd/photo",["slug"=>$m[1]]);
        break;

    //admin
    case preg_match('#^admin$#m',\the()->route,$m):
        $v=new View("html/admin",[]);
        break;

    //page event/slug
    case preg_match('#event/([a-z0-9\-]+)$#m',\the()->route,$m):
        $v=new View("html/event",["slug"=>$m[1]]);
        break;

    //page event/export-csv/slug
    case preg_match('#event/export-csv/([a-z0-9\-]+)$#m',\the()->route,$m):
        $v=new View("dwd/event/csv",["slug"=>$m[1]]);
        break;
    //page event/export-zip/slug
    case preg_match('#event/export-zip/([a-z0-9\-]+)$#m',\the()->route,$m):
        $v=new View("dwd/event/zip",["slug"=>$m[1]]);
        break;

    default:
        var_dump(View::$possiblesPath);
        die('error controler '.\the()->route."\n".View::getRealPath(\the()->route));
        //$v=new View("vuejs-layout",[]);
}


//render la view
$render=$v->render();
\the()->headerOutput->run();
echo $render;
//die();