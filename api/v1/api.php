<?php
require_once '../../app/loader.php';

$app = new TodoApp;
$app->setDatabaseName(DBNAME);
$app->setDatabaseUser(DBUSER);
$app->setDatabasePass(DBPASS);
$app->setDatabaseHost(DBHOST);

$app->run();
