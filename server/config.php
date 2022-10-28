<?php

the()->passwordIs("tonTonsFl@sheurs");
the()->env->dev=false;

the()->layout->meta->language="fr";
//the()->layout->theme->setFavicon("favicon.ico");
the()->layout->theme->setColor("222222");


//mailer
/*
 * non utilisé
the()->mailer->defaultMailTo="david@pixelvinaigrette.com";
// @note gmail common errors...
// @see http://www.google.com/accounts/DisplayUnlockCaptcha
// @see https://myaccount.google.com/lesssecureapps
the()->mailer->host = "xxxx.xxxx.com";
the()->mailer->password = "xxxxxxxx";
the()->mailer->username = "xxxx@xxxx.com";
the()->mailer->displayName = "noreply";
the()->mailer->SMTPAuth = true;
the()->mailer->port = "465";
the()->mailer->SMTPSecure = "ssl";
*/

//bdd par défaut
the()->dbConf->configSqlLite();