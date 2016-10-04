<?php

$db = parse_url(getenv("CLEARDB_DATABASE_URL"));
$sms_api_key = getenv("SMS_API_KEY");

$container->setParameter("database_host", $db["host"]);
$container->setParameter("database_name", substr($db["path"], 1));
$container->setParameter("database_user", $db["user"]);
$container->setParameter("database_password", $db["pass"]);
$container->setParameter("app.sms.api_key", $sms_api_key);