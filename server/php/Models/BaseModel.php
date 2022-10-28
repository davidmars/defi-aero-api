<?php

namespace Models;

use RedBeanPHP\SimpleModel;

/**
 * Modèle de base
 * @property integer $id Identifiant unique dans la BDD
 * @property string $date_created Date de création YYYY-MM-DD hh:mm:ss
 * @property string $date_modified Date de modification YYYY-MM-DD hh:mm:ss
 */
class BaseModel extends SimpleModel
{
    function update(){
        if(!$this->id){
            $this->date_created=date("Y-m-d H:i:s");
        }
        $this->date_modified=date("Y-m-d H:i:s");
    }

    /**
     * Le nom du modèle
     * @return string
     */
    protected function getModelName(){
        return $this->bean->getMeta("type");
    }


    /**
     * Obtenir les données au format API
     * @return array
     */
    public function getApiData(){
        $r=[];
        $r=$this->unbox()->jsonSerialize();
        unset($r["bean"]);

        $r["modelName"]=$this->getModelName();
        $r["id"]=$this->id*1.0;
        $r["date_created"]=$this->date_created;
        $r["date_modified"]=$this->date_modified;

        return $r;
    }
}