<?php
$vv=new \Utils\ApiResponse();
$vv->usePayload();
$vv->addMessage("pong");
$vv->addMessage(the()->fmkHref(true,true,""));
$vv->addMessage($_SERVER["REQUEST_URI"]);
$view->inside("json", $vv);
