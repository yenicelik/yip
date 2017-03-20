<?php	

ini_set('display_errors', 'On');
error_reporting(E_ALL | E_STRICT);


include("/var/www/include/config.php");
//include("../../include/StatAnalysis.php");

//$obj_statA = new StatAnalysis;
	
//$obj_statA->get_total_views();

$chars = "-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
$pdo;
	/**
	* Creates PDO for database communication
	*/
	try {
		$pdo_local = new PDO(C_DB_PDODRIVER . ":host=" . C_DB_HOST . ";dbname=" . C_DB_DATABASE . ";port=3306", C_DB_USERNAME, C_DB_PASSWORD);
		$pdo = $pdo_local;
	} catch (\PDOException $e) {
		echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
		header("Location: error.html");
		exit;
	}



	try {

		$word_pairs = get_word_pairs();
		foreach($word_pairs as $row) {
		    $wp = $row['word_pair'];
		    
		    $su_total_views = get_total_views($wp);
			$su_unique_views = get_unique_visitors($wp);
			$su_browser = base64_encode(serialize(get_visits_browser($wp)));
			$su_os = base64_encode(serialize(get_visits_platform($wp)));
			$su_ref = base64_encode(serialize(get_visits_referrers($wp)));
			$su_view_table = base64_encode(serialize(get_views_table($wp)));

			$query = "INSERT INTO table_analysis 
						(a_word_pair, a_total_views, a_table_views, a_unique_views, a_browser, a_os, a_referrers)
					VALUES 
						(:a_word_pair, :a_total_views, :a_table_views, :a_unique_views, :a_browser, :a_os, :a_referrers)
					ON DUPLICATE KEY UPDATE
						`a_total_views`= :a_total_views , 
						`a_table_views`= :a_table_views , 
						`a_unique_views`= :a_unique_views ,
						`a_browser`= :a_browser , 
						`a_os`= :a_os , 
						`a_referrers`= :a_referrers ;";
			
			$stm = $pdo->prepare($query);
			
			$para = array(
				"a_word_pair"=>$wp, 
				"a_total_views"=>$su_total_views, 
				"a_unique_views"=>$su_unique_views,
				"a_table_views"=>$su_view_table,  
				"a_browser"=>$su_browser, 
				"a_os"=>$su_os, 
				"a_referrers"=>$su_ref
				);
			
			$stm->execute($para);
			
		}
	} catch (Exception $e) {
			//be sure to take out these exception messages afterwards
			echo "Failure!";
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			echo "False";
	}


	function get_word_pairs() {
		global $pdo;
		$query = "SELECT word_pair FROM table_main;";
		$stm = $pdo->prepare($query);
		$stm->execute();
		$row = $stm->fetchAll();
    	return $row;
	}
	// Select total views
	/********************CONTROLLER SPECIFIC FUNCTIONS START HERE********************/
	function get_views_table($word_pair) {
		global $pdo;
		try {
			$query = "SELECT 
						YEAR(visit_datetime) as year, 
						MONTH(visit_datetime) as month, 
						DAYOFMONTH(visit_datetime) as mday, 
						COUNT(*) as num 
					FROM 
  						(SELECT * 
  						FROM table_visits 
  						WHERE visit_db_words = :word_pair 
  						AND visit_datetime >= ( curdate() - INTERVAL DAYOFWEEK(curdate())+60 DAY) ) 
					tmp 
					GROUP BY DATE(visit_datetime) 
					ORDER BY visit_datetime ASC;";
			$stm = $pdo->prepare($query);
			$para = array("word_pair"=>$word_pair);
			$stm->execute($para);
			$result = $stm->fetchAll();

			$out_arr = array();
			foreach ($result as $row) {
				switch ($row['month']) {
				    case '1': $row['month'] = "Jan"; break;
				    case '2': $row['month'] = "Feb"; break;
				    case '3': $row['month'] = "Mar"; break;
				    case '4': $row['month'] = "Apr"; break;
				    case '5': $row['month'] = "May"; break;
				    case '6': $row['month'] = "Jun"; break;
				    case '7': $row['month'] = "Jul"; break;
				    case '8': $row['month'] = "Aug"; break;
				    case '9': $row['month'] = "Sep"; break;
				    case '10': $row['month'] = "Oct"; break;
					case '11': $row['month'] = "Nov"; break;
				   	case '12': $row['month'] = "Dec"; break;
				    default: break;
				}
				$out_arr[] = array( $row['mday'] . " ". $row['month'] . " ". $row['year'], $row['num']);
			}

			return $out_arr;

		} catch (Exception $e) {
			//be sure to take out these exception messages afterwards
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			return false;
		}
	}

	/**
	 * Returns a word-id that was is not yet used up
	 * @return string
     */
	function get_total_views($word_pair) {
		global $pdo;
		try {
			$query = "SELECT COUNT(*) FROM table_visits WHERE visit_db_words = :word_pair;";
			$stm = $pdo->prepare($query);
			$para = array("word_pair"=>$word_pair);
			$stm->execute($para);
			$result = $stm->fetch();
			return $result[0];
		} catch (Exception $e) {
			//be sure to take out these exception messages afterwards
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			return false;
		}
	}

	// Select unique views
	function get_unique_visitors($word_pair) {
		global $pdo;
		try {
			$query = "SELECT COUNT(*) FROM (SELECT * FROM table_visits WHERE visit_db_words = :word_pair GROUP BY visit_ip) tmp;";
			$stm = $pdo->prepare($query);
			$para = array("word_pair"=>$word_pair);
			$stm->execute($para);
			$result = $stm->fetch();
			return $result[0];
		} catch (Exception $e) {
			//be sure to take out these exception messages afterwards
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			return false;
		}
	}

	// Select browsers
	/**
	 * Insert URL into DB and return word-pair if taken, otherwise false
	 * @param $long_url
	 * @return string
     */
	function get_visits_browser($word_pair) {
		global $pdo;
		try {
			$query = "SELECT visit_browser, count(*) as num FROM (SELECT * FROM table_visits WHERE visit_db_words = :word_pair) tmp group by visit_browser";
			$stm = $pdo->prepare($query);
			$para = array("word_pair"=>$word_pair);
			$stm->execute($para);
			$result = $stm->fetchAll();

			$out_arr = array();

			foreach ($result as $row) {
				$out_arr[] = array($row['visit_browser'], $row['num']);
			}

			return $out_arr;

		} catch (Exception $e) {
			//be sure to take out these exception messages afterwards
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			return false;
		}
	}

	// Select platform
	/**
	 * Insert URL into DB, returns True is successfull
	 * @param $w_id
	 * @param $long_url
	 * @param $word_pair
	 * @return bool
     */
	function get_visits_platform($word_pair) {
			//If URL is taken double, be sure to give the same word-pair ... just not yet
		
			global $pdo;
			try {
				$query = "SELECT visit_platform, count(*) as num FROM (SELECT * FROM table_visits WHERE visit_db_words = :word_pair) tmp group by visit_platform;";
				$stm = $pdo->prepare($query);
				$para = array("word_pair"=>$word_pair);
				$stm->execute($para);
				$result = $stm->fetchAll();
				
				$out_arr = array();

				foreach ($result as $row) {
					$out_arr[] = array($row['visit_platform'], $row['num']);
				}

				return $out_arr;
			} catch (Exception $e) {
				//be sure to take out these exception messages afterwards
				echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
				return false;
			}
		}

	// Select referrers
	/**
	 * Insert URL into DB, returns True is successfull
	 * @param $w_id
	 * @param $long_url
	 * @param $word_pair
	 * @return bool
     */

	function get_visits_referrers($word_pair) {
			//If URL is taken double, be sure to give the same word-pair ... just not yet
			//$Url should be sanitized before, or within; !
			// write a sanitizer function!
			global $pdo;
			try {
				$query = "SELECT visit_referrer, count(*) as num FROM (SELECT * FROM table_visits WHERE visit_db_words = :word_pair) tmp group by visit_referrer;";
				$stm = $pdo->prepare($query);
				$para = array("word_pair"=>$word_pair);
				$stm->execute($para);
				$result = $stm->fetchAll();

				$out_arr = array();

				foreach ($result as $row) {
					$out_arr[] = array( $row['visit_referrer'], $row['num']);
				}

				return $out_arr;
			} catch (Exception $e) {
				//be sure to take out these exception messages afterwards
				echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
				return false;
			}
	}




