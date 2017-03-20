<?php
if (session_status() === PHP_SESSION_NONE){session_start();}
ini_set('display_errors', 'On'); //Change during production
error_reporting(E_ALL | E_STRICT);
//Procedural redirect script
//Imports go here
require_once("../include/config.php");
require_once("../include/ShortUrl.php");
require_once("../include/SubmitSU.php");
require_once("../include/RetrieveSU.php");
include("../include/Stats.php");

$word_pair = strtolower(htmlspecialchars($_GET["c"]));

if (empty($word_pair)) {
	header("Location: index.php");
	exit;	
}

//$obj_retriever = new RetrieveSU(); 

$obj_stats = new Stats;

//Retrieve URL from user input ($query_db_words) and redirect to that Url
try {
	$long_url = $obj_stats->retrieve_url($word_pair);
	@ $obj_stats->record_stats($word_pair);
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: ". $long_url);
}
catch (\Exception $e) {
	$_SESSION['error_message'] = $e->getMessage();
	header("Location: index.php");
	exit;
}
