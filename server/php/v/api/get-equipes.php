<?php
$vv=new \Utils\ApiResponse();
$vv->usePayload();
$howMany=$vv->testAndGetRequest("howMany",null,true);
$etapeMin=$vv->testAndGetRequest("etapeMin",null,true);

if($vv->success){
   $r=[];
   $equipes=db()->find("equipe","etape >= ? ORDER BY date_modified DESC LIMIT 0,?",[$etapeMin,$howMany]);
   foreach ($equipes as $eq){
       $r[]=$eq->getApiData();
   }
   $vv->addToBody("equipes",$r);
}
if($vv->success){
    $stats=[
    "BIOCA"=>0,
    "KSYNTH"=>0,
    "HYDRO"=>0,
    "ELEC"=>0,
    ];
    $query="select carburant, count(*) as total from equipe where carburant not null AND carburant != '' group by carburant";
    $res=db()->getAll($query);
    foreach ($res as $carb){
        $stats[$carb['carburant']]=$carb['total']*1.0;
    }
    $vv->addToBody("stats",$stats);
}
$view->inside("json", $vv);
