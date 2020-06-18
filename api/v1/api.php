<?php
require_once '../../app/loader.php';

$app = new TodoApp;
$app->setDatabaseName(DBNAME);
$app->setDatabaseUser(DBUSER);
$app->setDatabasePass(DBPASS);
$app->setDatabaseHost(DBHOST);
$app->connectToDatabase();


// d($_SERVER['REQUEST_URI']);
// response(['Connected'], 200);
// d($dbConnection);