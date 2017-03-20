<?php  
ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);
require_once("../include/config.php");
require_once("../include/StatAnalysis.php");

$inp;
$bool = true;
$inf;
$wp;
$t_count;
$u_count;
$table_count;
$brow;
$os;
$ref;


if( !isset($_POST['wp']) || empty($_POST['wp']) ) 
{
  $inp = C_DEF_STATS;
  $bool = false;
} else {
  $_POST['wp'] = strtolower($_POST['wp']);
  $arr = explode("/", $_POST['wp']);

  foreach ($arr as $pos) {
    if( preg_match('/^[a-z\-]+$/i', $pos) ) {
      $inp = $pos;
    }
  }
  if(!isset($inp)) {
    $inp = C_DEF_STATS;
    $bool = false;
  } 
}

if(isset($inp)) {
    $obj_a_stats = new StatAnalysis();
    $result = $obj_a_stats->get_info($inp);

    if(empty($result)) {
      $inp = C_DEF_STATS;
      $bool = false;
    }

    $result = $obj_a_stats->get_info($inp);

    foreach ($result as $row) {
      $wp = $row['wp'];
      $t_count = $row['t_count'];
      $u_count = $row['u_count'];

      $table_count = (unserialize(base64_decode($row['table_count'])));
      $brow = (unserialize(base64_decode($row['brow'])));
      $os = (unserialize(base64_decode($row['os'])));
      $ref = (unserialize(base64_decode($row['ref'])));
    }
    $c_table_count = count($table_count);
    $c_brow = count($brow);
    $c_os = count($os);
    $c_ref = count($ref);
  }  

//Retrieve array-information through CRON-Job

?>
<html lang="en"> 
  <head> 

    <meta charset="utf-8"> 
      <meta http-equiv="X-UA-Compatible" content="IE=edge"> 
      <meta name="viewport" content="width=device-width, initial-scale=1"> 
    
      <meta name="description" content="Shorten your URL with easily memorizable Words, not random character digits! This is the best URL-Shortener! Safe, Fast and Accessible from anywhere"> 
      <meta name="author" content="A. Kemal David Yenicelik"> 
      <title> yip - Remember your URL! </title> 
    
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" integrity="APIKEY" crossorigin="anonymous">
    
      <link rel="icon" href="favicon.ico"> 
      <link href="creative.css" rel="stylesheet">
      <link href='http://fonts.googleapis.com/css?family=Open+Sans:400,700' rel='stylesheet' type='text/css'>
      <link href='http://fonts.googleapis.com/css?family=Merriweather:400,300' rel='stylesheet' type='text/css'>
      
    <!--/// All Styling Tags (Icon, CSS, Fonts) -->

        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script type="text/javascript">
            google.charts.load('current', {packages: ['corechart', 'geochart', 'line', 'bar']});
            google.charts.setOnLoadCallback(drawChart);
            
            function drawChart() {
              
              // Total Number of Clicks over the last month
              var data = new google.visualization.DataTable();
                           
              data.addColumn('string', 'Day');
              data.addColumn('number', 'Clicks');
              data.addRows([ 
                <?php
                for($i=0;$i<$c_table_count;$i++){
                    echo "['" . $table_count[$i][0] . "'," . $table_count[$i][1] . "],";
                }?> 
                ]);

              // Referrers
              var data2 = new google.visualization.DataTable();
              data2.addColumn('string', 'Referrer');
              data2.addColumn('number', 'Total Clicks');
              data2.addRows([

                <?php
                for($i=0;$i<$c_ref;$i++){
                    echo "['" . $ref[$i][0] . "'," . $ref[$i][1] . "],";
                } 
                ?>

              ]);

              // Browsers
              var data3 = google.visualization.arrayToDataTable([
                [ {label: '', id: 'Browser'},
                 {id: 'Clicks', type: 'number'} ],
                <?php
                for($i=0;$i<$c_brow;$i++){
                    if ($i == ($c_brow - 1)) {
                      echo "['" . $brow[$i][0] . "'," . $brow[$i][1] . "]";
                    } else {
                      echo "['" . $brow[$i][0] . "'," . $brow[$i][1] . "],";
                    }
                } 
                ?>
              ], false);

              // Platforms
              var data4 = google.visualization.arrayToDataTable([
                [ {label: '', id: 'Platform'},
                 {id: 'Clicks', type: 'number'} ],
                <?php
                for($i=0;$i<$c_os;$i++){
                    if ($i == ($c_os - 1)) {
                      echo "['" . $os[$i][0] . "'," . $os[$i][1] . "]";
                    } else {
                      echo "['" . $os[$i][0] . "'," . $os[$i][1] . "],";
                    }
                } 
                ?>
              ], false);

              // Country
              /*
              var data5 = google.visualization.arrayToDataTable(
                <?php echo $inf['a_os']; ?>

                /*[
                ['Country', 'Popularity'],
                ['Germany', 200],
                ['United States', 300],
                ['Brazil', 400],
                ['Canada', 500],
                ['France', 600],
                ['RU', 700]
              ]*//* ); */


            // Set chart options
              var options = {
                axes: {
                  x: {
                    0: {side: 'top'}
                  }
                },
                crosshair: {
                  color: '#000',
                  trigger: 'selection'
                },
                width: 900,
                height: 450,
                hAxis: {
                  title: 'Time'
                },
                vAxis: {
                  title: 'Total Views'
                }
                //hAxis: { textPosition: 'none'},
                //chartArea: {top:'7%', left:'7%', width: '90%', height: '85%'}
              };

              // Set chart options
              var options2 = {pieHole: 0.38,
                              height: 350,
                              width: 550,
                              'legend':'left',
                              hAxis: { textPosition: 'none', format: 'none' },
                            };
              // Set chart options
              var options3 = {
                              hAxis: {
                                minValue: 0,
                              },
                              bars: 'horizontal',
                              height: 270,
                              width: 400,
                              legend: { position: "none" },
                              hAxis: { textPosition: 'none', format: 'none' }
                            };
 
              var options4 = {
                              hAxis: {
                                minValue: 0,
                              },            
                              height: 270,
                              width: 400,
                              bars: 'horizontal',
                              legend: { position: "none" },
                              hAxis: { textPosition: 'none', format: 'none' }
                            };

             /* var options5 = {}; */

              // Instantiate and draw our chart, passing in some options.
              var chart = new google.visualization.LineChart(document.getElementById('graph_visitors_count'));
              chart.draw(data, options);
              var chart2 = new google.visualization.PieChart(document.getElementById('graph_referrers'));
              chart2.draw(data2, options2);
             var chart3 = new google.charts.Bar(document.getElementById('graph_browsers'));
              chart3.draw(data3, options3);
            var chart4 = new google.charts.Bar(document.getElementById('graph_platforms'));
              chart4.draw(data4, options4);
            /*  var chart5 = new google.visualization.GeoChart(document.getElementById('graph_countries'));
              chart5.draw(data5, options5); */
            }
        </script>

  </head>



  <body id="page-top">

    <nav id="mainNav" class=" navbar-default navbar-fixed-top" >
      <div class="container-fluid" style="float: right">
        <div class="navbar-header">
          <a class="navbar-brand page-scroll" href="/" style="color: #f05f40"> Shorten your URL! </a>
        </div>
      </div>
    </nav> 

    <div class="page-header">
        <form action="stats.php" method="POST" style="text-align:center; vertical-align: middle;">
        
        <h2 id="timeline" style="display:inline; margin-right: 2%; padding-bottom: 3%;">Showing results for '<?php echo($wp); ?>'.</h2>
        <input type="text" class="form-control" id="usr" name="wp" style="width: 30%; text-align: center; display:inline; margin-right: 2%; padding:10px;" placeholder="Enter Word-Pair or Yip-Link to show results for">
        <input type="submit" class="btn btn-primary" value="Get Short-Code Analysis" style="width:20%; display:inline;">
        <?php 
        if (!($bool)) { ?>
        <h3 style="color:#f05f40;"> No results found for the given Word-Pair, thus we are showing results for the upper word-pair instead! Please enter a new word-pair, check spelling, or contact us if something went wrong! </h3>
                    <?php } ?>
      </form>
    
    </div>

    <div stlye="margin-bottom: 2%; margin-left: 10%;">
      <h2 class="section-heading" style="width:30%;"> Clicks over the last 60 days </h2>
      <div id="graph_visitors_count" style=""></div>
    </div>

    <hr class="primary">

    <div stlye="margin-bottom: 2%; margin-left: 10%;">
      <div style="display: inline-block; width:30%;">
        <h2 class="section-heading" style="display: inline-block; margin-top: 10%; width:30%;"> Referrers </h2>
        <div id="graph_referrers" style="display: inline-block; padding-right: 2%; float:left;"></div>
      </div>
      <div style="display: inline-block; width:30%; margin-left: 10%;">
        <div  class="text-center" style="display: inline-block; margin-left: 10%; padding-left: 5%; vertical-align: top; ">
          <h3 class="section-heading" style="margin-top: 10%; "> </h3>
          <h3 class="section-heading" style="margin-top: 30%; "> Total views: <?php echo $t_count; ?> </h3>
          <h3 class="section-heading" style="margin-top: 30%; "> Unique visitors: <?php echo $u_count; ?> </h3>
        </div>
      </div>
    </div>

    <hr class="primary">

    <div style="margin-top: 2%;">
      <div style="display: inline-block; width:45%;">
        <h2 class="section-heading" style=""> Browsers  </h2>
        <div id="graph_browsers" style="display: inline-block; padding-right: 5%;"> </div> 
      </div>
      <div style="display: inline-block; width:45%;">
        <h2 class="section-heading" style=""> Platforms  </h2>
        <div id="graph_platforms" style="display: inline-block; padding-left: 5%;"> </div>
      </div>
    </div >

    <section id="contact">
      <div class="container">
        <div class="row">
          <div class="col-lg-8 col-lg-offset-2 text-center">
            <h2 class="section-heading">Let's Get In Touch!</h2>
            <hr class="primary">
            <p>Have Questions or Suggestions? That's great! Send us an email at <a href="mailto:yedavid@student.ethz.ch">yedavid@student.ethz.ch</a> and we will get back to you!</p>
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
