<?php
class SubmitSU extends ShortUrl {
	
	/**
	* Returns a word_pair if url exists, otherwise returns newly generated word_pair
	* @param $long_url
	* @return string
	* @throws Exception
	*/
	public function shorten_url($long_url) {
		
		if (empty($long_url)) {
			throw new \Exception("No URL was supplied. If you have supplied a URL but receive this message please contact us!");
		}
		if ($this->verify_url_format($long_url) == false) {
			throw new \Exception("The URL does not have a valid format, or is of this domain");
		}
		if(!$this->verify_url_exists($long_url)) {
			throw new \Exception("The URL does not appear to exist. We get a 404 (dead link)!");
		}
		if($this->send_response($long_url) == false) {
			throw new \Exception("The URL is classified as malicious!");
		}

		$word_pair = $this->url_exists_in_db($long_url);

		if( ($word_pair == false) ) {
			$word_pair = $this->gen_word_pair($long_url);
			$this->insert_url_in_db($long_url, $word_pair);
		}
		return $word_pair;
	}

	/**
	 * Function for sending a HTTP GET Request
	 * to the Google Safe Browsing Lookup API
	 */
	protected function get_data($url) {
	        $ch = curl_init ();
	        curl_setopt ( $ch, CURLOPT_URL, $url );
	        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
	        curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
	        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	        
	        $data = curl_exec ( $ch );
	        $httpStatus = curl_getinfo ( $ch, CURLINFO_HTTP_CODE );
	        curl_close ( $ch );
	        
	        return array (
	            'status' => $httpStatus,
	            'data' => $data 
	        );
	}

	/**
	 * Function for analyzing and paring the
	 * data received from the Google Safe Browsing Lookup API 
	 */
	public function send_response($input) {
	        if (! empty ( $input )) {
	                $urlToCheck = urlencode ( $input );

	                $url = 'https://sb-ssl.google.com/safebrowsing/api/lookup?client=' . CLIENT . '&apikey=' . API_KEY . '&appver=' . APP_VER . '&pver=' . PROTOCOL_VER . '&url=' . $urlToCheck;
	                
	                $response = $this->get_data ( $url );

	                if ($response ['status'] == 200) {
	                        return false;
	                } else {
	                        return true;
	                }
	        } else {
	                return true;
	        }
	}

}
