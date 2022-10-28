<?php
$vv=new \Utils\ApiResponse();
$vv->usePayload();
$code=$vv->testAndGetRequest("code",null,true);

if($vv->success){
   $e=\Models\Equipe::getByCode($code);
   $vv->addToBody("equipe",$e->getApiData());
}
$view->inside("json", $vv);
