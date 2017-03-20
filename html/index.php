<?php
if (session_status() === PHP_SESSION_NONE){session_start();}
?>
<html lang="en"> 
  <head> 
      <meta charset="utf-8"> 
      <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
      <meta name="viewport" content="width=device-width, initial-scale=1"> 
      <meta name="description" content="Shorten and Remember your URL with easily memorizable Words, not random character digits! This is the best URL-Shortener! Safe, Fast and Accessible from anywhere"> 
      <meta name="msvalidate.01" content="AAD4402022A51DCFD306832C9D05C9AE" /> 
      <meta name="google-site-verification" content="kdGUajaYBt-2T9btfd5z4XTBABZXy2KBoYZ5GAd6svU" />	
      <meta name="author" content="A. Kemal David Yenicelik"> 
      
      <title> yip - Shorten and Remember your URL! </title> 
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="APIKEY" crossorigin="anonymous">
      <link rel="alternate" href="http://example.com" hreflang="en-us" />
      <link rel="icon" href="favicon.ico"> 
      <link href="creative.css" rel="stylesheet">
      <link href='//fonts.googleapis.com/css?family=Open+Sans:700|Merriweather:400' rel='stylesheet' type='text/css'>
  </head>

  <body id="page-top">

    <nav id="mainNav" class=" navbar-default navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand page-scroll" href="/"> Shorten your URL! </a>
        </div>
      </div>
    </nav> 

    <header style="text-align:center;">
      <form class="header-content" action="submit.php" method="POST" style="text-align:center;">
        <div class="header-content-inner">
          <h1>Yip.bz</h1>
          <p style="font-size:1.4em; text-outline: 2px 2px #000000;">Shorten your long URL and remember it easily! Your link will be safe, stable, fast and accessible from anywhere!
          </p>
          <p style="font-size:1.2em; color: #f05f40; text-outline: 2px 2px #000000;"> 
<?php 
    if (! ( session_status() === PHP_SESSION_NONE) ) { 
      echo($_SESSION['error_message']);
      session_destroy();
    }
?>
          </p>
        </div>
        <input type="text" name="url" id="url" class="form-control" placeholder="Enter long URL Here, and click 'Enter' or the button below to shorten" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Enter long URL Here, and click Enter or the button below to shorten'"  style="text-align: center;  font-family: 'Open Sans','Helvetica Neue',Arial,sans-serif;">
        <input type="submit" class="btn btn-primary btn-xl page-scroll" value="Shorten URL" style="margin-top: 40px;">
      </form>
    </header>

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
    
    <script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'APIKEY', 'auto');
  ga('send', 'pageview');

</script>
    
  </body>
</html>
