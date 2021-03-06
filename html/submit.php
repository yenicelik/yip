<?php
if (session_status() === PHP_SESSION_NONE){session_start();}
//Dev-Code only!! Set out in Production environment
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);

require_once("../include/config.php");
require_once("../include/ShortUrl.php");
require_once("../include/SubmitSU.php");
require_once("../include/RetrieveSU.php");


if(!isset($_POST["url"])) {
  header("Location: index.php");
  exit;
}

$long_link = $_POST["url"];

//echo $long_link;
if (!preg_match("~^(?:f|ht)tps?://~i", $long_link)) {
        $long_link = "http://" . $long_link;
}
//echo $long_link;
//exit;


$long_link = filter_var($_POST["url"], FILTER_VALIDATE_URL, FILTER_FLAG_PATH_REQUIRED);
//$long_link = $_POST["url"];
// $long_link = htmlspecialchars($_POST["url"]);

if($_SERVER["REQUEST_METHOD"] != "POST" || empty($long_link)) {
	header("Location: index.php");
	exit;
}

$obj_submitter = new SubmitSU();

//Shorten URL and return double_words
try {
	$word_pair = $obj_submitter->shorten_url($long_link);
} catch (\Exception $e) {
  $_SESSION['error_message'] = (string) $e->getMessage();
  header("Location: index.php");
	exit;
}

//Define absolute URL
$url = 'https://' . C_SHORTURL_PREFIX . $word_pair;
$statsurl = C_STATS_PREFIX . $word_pair;

?>
<html lang="en"> 
  <head> 
      <meta charset="utf-8"> 
      <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
      <meta name="viewport" content="width=device-width, initial-scale=1"> 
      <meta name="description" content="Shorten your URL with easily memorizable Words, 
      not random character digits! This is the best URL Shortener!">
      <meta name="msvalidate.01" content="APIKEY" />  <!-- replace with msvalidate API key-->
      <meta name="author" content="A. Kemal David Yenicelik"> 
      
      <title> yip.bz - Remember your URL! </title> 
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="APIKEY" crossorigin="anonymous">
      
      <link rel="icon" href="favicon.ico"> 
      <link href="creative.css" rel="stylesheet">
      <link href='//fonts.googleapis.com/css?family=Open+Sans:700|Merriweather:400' rel='stylesheet' type='text/css'>

      <script src="https://cdn.jsdelivr.net/clipboard.js/1.5.5/clipboard.min.js"></script>

  </head>



  <body id="page-top">

    <nav id="mainNav" class="navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand page-scroll" href="/" style="cursor: default"> shorten your URL! </a>
        </div>
      </div>
    </nav>

    <header style="position: relative; text-align: center ">
        <div class="header-content" >
            <div class="header-content-inner">
                <h1>Easy</h1>
                <br>
                <p style="font-size:1.4em; text-outline: 2px 2px #000000; width: 90%; vertical-align: middle;"><strong>Your Short URL:</strong>
<?php        
echo<<<ENDHTML
  <a target="_blank" id="to_copy" href="$url" style="display: inline;" >$url</a>
  <button class="btn btn-primary" style="display: inline; background-color: rgba(240, 95, 64, 0.4); color: white; border-color: #f05f40; border-radius: 3px; font-size: 0.67em; padding: 7px; margin-left: 10px;" >
    <span class="glyphicon glyphicon-download-alt"></span> Copy
  </button>
ENDHTML;
?>
                  
                  </p>
            </div>
        </div>
     <?php 
echo<<<ENDL
  <a target="_blank" href="$statsurl" style="position: absolute; bottom: 46px; width: 100%; left: 0%;">Statistics (available after one hour)</a>
ENDL;
?>   
    </header>

    <script>
      var clipboard = new Clipboard('.btn', {
          target: function() {
              return document.getElementById('to_copy');
          }
      });
      clipboard.on('success', function(e) {
          console.log(e);
      });
      clipboard.on('error', function(e) {
          console.log(e);
      });
    </script>

    <section id="contact">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-lg-offset-2 text-center">
            <h2 class="section-heading">Let's Get In Touch!</h2>
            <hr class="primary">
            <p>Ready to see your name or brand as a URL? That's great! Send us an email at <a href="mailto:yedavid@student.ethz.ch">yedavid@student.ethz.ch</a> and we will get back to you</p>
          </div>
        </div>
        <br>
        <p style="text-align: center; font-size: 0.9em;">
          <a href="/about.html" style="margin: 10%;"> About Us </a>
          <a href="/" style="margin: 10%;"> Main Page </a>
          <a href="/stats.php" style="margin: 10%;"> Statistics </a>
        </p>
      </div>
    </section>
    
  </body>
</html>

<?php

