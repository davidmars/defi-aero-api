<?php

namespace Models;

/**
 * @property string code Le code barre
 * @property string name Le nom donné à l'équipe
 * @property integer etape Le nom donné à l'équipe
 * @property string projet Le projet choisi (ENUM)
 * @property string $membresjson Les membres
 * @property string couleursjson Les membres
 */
class Equipe extends BaseModel
{



    public function update()
    {
        parent::update();
    }

    /**
     * @return string[]
     */
    public function getMembres(){
        $r = json_decode($this->membresjson);
        if(!is_array($r)){
            $r=[];
        }
        return $r;
    }
    /**
     * @return string[]
     */
    public function getCouleurs(){
        $r = json_decode($this->couleursjson,true);
        if(!is_array($r)){
            $r=[
                'aile'=>'#808080',
                'moteur'=>'#808080',
                'fuselage'=>'#808080',
                'empennage'=>'#808080',
            ];
        }
        return $r;
    }

    /**
     * @param string[] $membres
     * @return void
     */
    public function setMembres($membres){
        $this->membresjson =json_encode($membres);
    }
    /**
     * @param object $couleurs
     * @return void
     */
    public function setCouleur($couleurs){
        $this->couleursjson =json_encode($couleurs);
    }

    /**
     * Renvoie et crée au besoin une équipe à partir d'un code.
     * @param string $code
     * @param boolean $createIfNull
     * @return Equipe
     */
    public static function getByCode($code, $createIfNull=true)
    {
        /** @var Equipe $e */
        $e=db()->findOne("equipe","code = ?",[$code]);
        if(!$e && $createIfNull){
            $e=db()->dispense("equipe");
            $e->code=$code;
            db()->store($e);
        }
        return $e;
    }

    public function getApiData()
    {
        $r= parent::getApiData();
        unset($r["membresjson"]);
        unset($r["couleursjson"]);
        unset($r["modelname"]);
        $r["name"]=$this->name;
        $r["etape"]=$this->etape * 1.0;
        $r["projet"]=$this->projet;
        $r["membres"]=$this->getMembres();
        $r["couleurs"]=$this->getCouleurs();
        return $r;
    }


}