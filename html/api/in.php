<?php
//if (session_status() === PHP_SESSION_NONE){session_start();}
//Dev-Code only!! Set out in Production environment
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("../../include/config.php");
require_once("../../include/ShortUrl.php");
require_once("../../include/SubmitSU.php");
require_once("../../include/RetrieveSU.php");


if(!isset($_POST["url"])) {
  header('HTTP/1.1 400 No Resource given', true, 400);
  exit;
}

$long_link = htmlspecialchars($_POST["url"]);
//echo $long_link;
//echo "Hi";
if(empty($long_link)) {
	header('HTTP/1.1 400 No URL given', true, 400);
	exit;
}

//echo "Hi";
$obj_submitter = new SubmitSU();

//echo "Hi";
//Shorten URL and return double_words
try {
	$word_pair = $obj_submitter->shorten_url($long_link);
} catch (\Exception $e) {
  header('HTTP/1.1 400 Something went wrong! Please contact us!', true, 400);
	exit;
}

// echo "Hi";
//Define absolute URL
$url = C_SHORTURL_PREFIX . $word_pair;
//$statsurl = C_STATS_PREFIX . $word_pair;

// echo "Hi";
$data = $url;
header('Content-Type: application/json');
// echo $data;