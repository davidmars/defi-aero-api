<?php
$vv=new \Utils\ApiResponse();
$vv->usePayload();
$equipeJson=$vv->testAndGetRequest("equipe");
/** @var \Models\Equipe $equipe */
$equipe=null;
if($vv->success){
    $equipeJson=json_decode($equipeJson,true);
    $equipe=\Models\Equipe::getByCode($equipeJson["code"]);
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
