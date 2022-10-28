<?php

namespace Utils;

use Exception;

/**
 * La Class FileSystem gère proprement les fichiers d'un projet donné.
 *
 * @package Pov\Configs
 */
class FileSystem {
    /**
     * @var string le répertoire de base où sont stockés les fichiers de db, cache etc... (pour cette instance)
     */
    public $rootPath="";
    /**
     * @var string Un répertoire où sont stockés des fichiers
     */
    public $dbPath="";
    /**
     * @var string Un répertoire temporaire
     */
    public $tmpPath="";
    /**
     * @var string Le répertoire où sont stockés les fichiers uploadés par les utilisateurs
     */
    public $uploadsPath="";
    /**
     * @var string Le répertoire où sont stockés les fichiers en cache
     */
    public $cachePath="";
    /**
     * @var string Le répertoire où sont stockés les fichiers avant suppression définitive
     */
    public $trashPath="";

    public function __construct(){
       $this->boot();
    }

    private function boot()
    {
        $this->rootPath = "fs";
        $dirs=["tmp","db","up","cache","trash"];
        foreach($dirs as $subDir){
            $fullPath=$this->rootPath."/".$subDir;
            if(!is_dir($fullPath)){
                mkdir($fullPath,0777,true);
            }
        }
        $this->tmpPath=$this->rootPath."/tmp";
        $this->dbPath=$this->rootPath."/db";
        $this->uploadsPath=$this->rootPath."/up";
        $this->cachePath=$this->rootPath."/cache";
        $this->trashPath=$this->rootPath."/trash";
    }

    /**
     * Déplace le fichier donné vers la poubelle
     * @param string $filePath
     * @return bool
     */
    public function trash($filePath){
        if(preg_match("/^fs\/up\//",$filePath)){
            if(file_exists($filePath) && is_file($filePath)){
                $trashPath=$this->trashPath."/".date("Y-m-d")."/".$filePath;
                $this->prepareDir($trashPath);
                return rename($filePath,$trashPath);
            }
        }

        return false;
    }

    /**
     * Retourne le chemin d'un fichier upload pour être affiché via http
     * @param string $filePath
     * @param bool $absolute si true renverra l'url avec http://etc...
     * @return string
     */
    public function uploadHttpPath($filePath,$absolute=false)
    {
        $r=the()->fmkHttpRoot."/".$this->uploadLocalPath($filePath);
        if($absolute){
            return the()->fmkHref().$r;
        }
        return $r;
    }

    public function moveFromTmpToUpload($tmpFilePath){
        $fileName=basename($tmpFilePath);
        if(!file_exists($tmpFilePath) || !is_file($tmpFilePath)){
            return "from n'existe pas";
        }
        if(!is_dir($this->uploadsPath)){
            return "$this->uploadsPath n'existe pas";
        }
        if(!is_writable($this->uploadsPath)){
            return "$this->uploadsPath n'est pas en écriture'";
        }
        $dest=$this->uploadsPath."/$fileName";
        $success=rename($tmpFilePath,$dest);
        if(!$success){
            return "erreur au moment de copier $dest";
        }
        return $dest;
    }
    /**
     * Retourne le chemin d'un fichier upload en sytème local
     * @param string $filePath
     * @return string
     */
    public function uploadLocalPath($filePath)
    {
        $filePath=trim($filePath,"/");
        return $this->uploadsPath."/$filePath";
    }

    /**
     * Identique à file_put_contents mais crée les sous répertoires si nécessaire
     *
     * @param string $filePath
     * @param string $content
     *
     * @return bool|int
     */
    public function filePutContents($filePath, $content)
    {
        $this->prepareDir($filePath);
        return file_put_contents($filePath,$content);
    }

    /**
     * Crée le répertoire (récursivement) d'un fichier donné
     * @param string $filePath Chemin vers le fichier
     */
    public function prepareDir($filePath){
        $dir=dirname($filePath);
        if(!is_dir($dir)){
            mkdir($dir,0777,true);
        }
    }

    /**
     * Supprime du chemin la partie qui concerne le serveur apache
     * @param string $filePath exemple monserveur/virtual-host/mon-repertoire/monfichier.ext
     * @return string exemple mon-repertoire/monfichier.ext
     */
    public function pathWithoutRootDir($filePath)
    {
        return str_replace(realpath(".")."/","",$filePath);
    }

    /**
     * Retourne un identifiant de base de données sqLite (utilisable par redbean).
     * Attention ceci ne crée par la base de données, ça donne simplement un chemin logique.
     * @param string $databaseName Le nom de la base de données
     * @return string l'identifiant qui ressemblera à sqlite:files/my-project-dir/db/$databaseName.db
     */
    public function dbSqlLitePath($databaseName,$backupSuffix=null)
    {
        if($backupSuffix===null){
            $backupSuffix="bcp-".date("Y-m-d");
        }
        $dbFile=$this->dbPath."/$databaseName.db";
        $bcpFile=$this->dbPath."/$databaseName.$backupSuffix.db";
        if(file_exists($dbFile) && is_file($dbFile) && !file_exists($bcpFile)){
            copy($dbFile,$bcpFile);
        }
        return "sqlite:$dbFile";
    }

    /**
     * Rajoute une extension au fichier en fonction de son mime
     * @param string $fileWithoutExtension
     * @return string
     */
    private function setFileExtension($fileWithoutExtension){
        if(file_exists($fileWithoutExtension) && is_file($fileWithoutExtension)){
            $mime=mime_content_type($fileWithoutExtension);
            if($mime){
                $ext=utils()->pov()->utils->files->mimeToExtension($mime);
                if($ext){
                    $newName=$fileWithoutExtension.".$ext";
                    rename($fileWithoutExtension,$newName);
                    return $newName;
                }
            }
        }
        return $fileWithoutExtension;
    }

    /**
     * identique à file_get_contents
     * @param $filePath
     * @return bool|string
     * @throws Exception
     */
    public function fileGetContents($filePath)
    {
        //die($filePath);
        if(!$filePath || !file_exists($filePath) || !is_file($filePath)){
            $m="$filePath n'existe pas!";
            throw new Exception($m);
        }else{
            return file_get_contents($filePath);
        }
    }

    /**
     * Convertit un chemin serveur en chemin http
     * @param string $localPath un chemin en mode serveur
     * @param bool $absolute Si true renverra le chemin absolut.
     * @return string un chemin en mode http
     */
    function filesystemToHttp($localPath,$absolute=false){
        $r=preg_replace("%^".preg_quote(getcwd())."%","",$localPath);
        $r=str_replace("\\","/",$r);
        $r=trim($r,"/");
        return the()->fmkHref($absolute,$absolute,$r);
    }



}