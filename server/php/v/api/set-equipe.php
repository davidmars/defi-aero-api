<?php

use Models\Equipe;

$vv=new \Utils\ApiResponse();
$vv->usePayload();
$equipeJson=$vv->testAndGetRequest("equipe");
/** @var Equipe $equipe */
$equipe=null;
if($vv->success){
    $equipeJson=json_decode($equipeJson,true);
    if(!$equipeJson){
        $vv->addError("le JSON equipe envoyé n'est pas un JSON valide");
    }else{
        if(!$equipeJson["code"]){
            $vv->addError("le JSON equipe envoyé n'a pas de code");
        }
    }

}
if($vv->success){
    $equipe= Equipe::getByCode($equipeJson["code"],false);
    if(!$equipe){
        $vv->addError("L'équipe ".$equipeJson['code']." n'existe pas");
    }
}



if($vv->success){

    foreach ($equipeJson as $k=>$v){
        switch ($k){
            case "membres":
                $equipe->setMembres($v);
                break;

            case "couleurs":
                $equipe->setCouleur($v);
                break;

            //champs read only
            case "id":
            case "date_created":
            case "date_modified":
                break;
            default:
                $equipe->$k=$v;
        }
    }
    db()->store($equipe);


    $vv->addToBody("equipe",$equipe->getApiData());
}
$view->inside("json", $vv);
