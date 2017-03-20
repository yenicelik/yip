<?php
class Stats extends RetrieveSU {
	/**
	* Implenet functions that input the statistics into the database!
	* Implement CRON-job to analyze url's once every day/hour
	*/

	public function record_stats($word_pair) {
		$v_ip = $this->get_ip();
		$v_browser = $this->get_brow();
		$v_referrer = strval($this->get_referrer());
		$v_os = $this->get_os();

		try {
			$query = "INSERT INTO table_visits 
						(visit_ip, visit_referrer, visit_browser, visit_platform, visit_db_words) 
					VALUES 
						(:visit_ip, :visit_referrer, :visit_browser, :visit_platform, :visit_db_words);";
			$stm = $this->pdo->prepare($query);
			$para = array("visit_ip"=>ip2long($v_ip), 
						"visit_referrer"=>$v_referrer, 
						"visit_browser"=>$v_browser, 
						"visit_platform"=>$v_os, 
						"visit_db_words"=>$word_pair);
			$stm->execute($para);
			return true;
		} catch (Exception $e) {
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			return false;
		}
	}
	
	/**
	* Returns a word_pair if url exists, otherwise returns newly generated word_pair
	* @return string
	*/
	protected function get_ip() {
		$v_ip = "";

		if(!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$v_ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$v_ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} elseif (!empty($_SERVER['REMOTE_ADDR'])) {
			$v_ip = $_SERVER['REMOTE_ADDR'];
		}
		if (filter_var($v_ip, FILTER_VALIDATE_IP)) {
			return $v_ip;
		} else {
			return 0;
		}	
	}

	/**
	* Returns a word_pair if url exists, otherwise returns newly generated word_pair
	* @return string
	*/
	protected function get_brow() {
		$user_agent = $_SERVER['HTTP_USER_AGENT']; 
		
    	$browser_array = array(
                            '/msie/i'       =>  'Internet Explorer',
                            '/firefox/i'    =>  'Firefox',
                            '/chrome/i'     =>  'Chrome',
                            '/edge/i'       =>  'Edge',
                            '/opera/i'      =>  'Opera',
                            '/iemobile/i' => 'IE Mobile',
							'/android/i' => 'Android',
							'/iPhone/i' => 'iPhone',
                            '/netscape/i'   =>  'Netscape',
                            '/maxthon/i'    =>  'Maxthon',
                            '/konqueror/i'  =>  'Konqueror',
                            '/safari/i'     =>  'Safari',
							'/mobile/i'     =>  'Handheld Browser',
                        );

	    foreach ($browser_array as $regex => $value) { 
	        if (preg_match($regex, $user_agent)) {
	            $browser = $value;
	            break;
	        } else {
	        	$browser = "Unknown Browser";
	        }
	    }
	    return $browser;
	}

	/**
	* Returns a word_pair if url exists, otherwise returns newly generated word_pair
	* @return string
	*/
	protected function get_os() {
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
    	$os_array       =   array(
                            '/windows nt 10/i'     =>  'Windows 10',
                            '/windows nt 6.3/i'     =>  'Windows 8.1',
                            '/windows nt 6.2/i'     =>  'Windows 8',
                            '/windows nt 6.1/i'     =>  'Windows 7',
                            '/windows nt 6.0/i'     =>  'Windows Vista',
                            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
                            '/windows nt 5.1/i'     =>  'Windows XP',
                            '/windows xp/i'         =>  'Windows XP',
                            '/windows nt 5.0/i'     =>  'Windows 2000',
                            '/windows me/i'         =>  'Windows ME',
                            '/win98/i'              =>  'Windows 98',
                            '/win95/i'              =>  'Windows 95',
                            '/win16/i'              =>  'Windows 3.11',
                            '/macintosh|mac os x/i' =>  'Mac OS X',
                            '/mac_powerpc/i'        =>  'Mac OS 9',
                            '/linux/i'              =>  'Linux',
                            '/ubuntu/i'             =>  'Ubuntu',
                            '/iphone/i'             =>  'iPhone',
                            '/ipod/i'               =>  'iPod',
                            '/ipad/i'               =>  'iPad',
                            '/android/i'            =>  'Android',
                            '/blackberry/i'         =>  'BlackBerry',
                            '/webos/i'              =>  'Mobile'
                        );
	    foreach ($os_array as $regex => $value) { 
	        if (preg_match($regex, $user_agent)) {
	            $os_platform    =   $value;
	            break;
	        } else {
	        	$os_platform    =   "Unknown OS Platform";
	        }
	    }   
	    return $os_platform;
	}

	/**
	* Returns a word_pair if url exists, otherwise returns newly generated word_pair
	* @return string
	*/
	protected function get_referrer() {
		if ((empty($_SERVER['HTTP_REFERER']))) {
			return "none";
		} else {
			if ( $parts = parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST) ) {
				return $parts;	    
			}
		}
	}

}
