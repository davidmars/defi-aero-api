<?php

$vv=new \Utils\ApiResponse();
$vv->usePayload();
$vv->addMessage("Hello world");
$view->inside("json", $vv);
