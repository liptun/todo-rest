<?php
require_once '../../app/loader.php';

try {
  $dbConnection = new PDO(sprintf('mysql:host=%s;dbname=%s', DBHOST, DBNAME), DBUSER, DBPASS);
  $dbConnection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
  Response::json(['error' => $e->getMessage()], 503);
}

// d($_SERVER['REQUEST_URI']);
// response(['Connected'], 200);
// d($dbConnection);