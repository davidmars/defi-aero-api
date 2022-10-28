<?php

namespace Utils;

class DbConf
{
    /**
     * @var string Database connection string
     */
    public $dsn="";
    /**
     * @var string Username for database
     */
    public $username="";
    /**
     * @var string Password for database
     */
    public $password="";
    /**
     * @var bool if you want to setup in frozen mode
     */
    public $frozen=false;

    /**
     * Configure pour sqlLite
     */
    public function configSqlLite($dbName="mon-site-db",$backupSuffix="Y-m-d"){
        $this->dsn=the()->fileSystem->dbSqlLitePath(
            $dbName,
            //"mon-site-db","week-".date("Y-W") //backup une fois par semaine
            "backup-".date($backupSuffix) //backup une fois par heure
        );
    }

    /**
     * Configure pour MySql
     * @param $host
     * @param $dbName
     * @param $username
     * @param $password
     */
    public function configMySql($host,$dbName,$username,$password){
       $this->dsn= "mysql:host=$host;dbname=$dbName";
       $this->password=$password;
       $this->username=$username;
    }
}