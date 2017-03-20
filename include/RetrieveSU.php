<?php
class RetrieveSU extends ShortUrl {

	/**
	* Returns the long_link for the corresponding word_pair
	* @return string
	*/
	public function retrieve_url($word_pair) {
		
		if(empty($word_pair)) {
			throw new \Exception("No words were supplied");
		}
		
		if($this->verify_word_pair($word_pair) == false) {
			throw new \Exception("Short code does not have a valid format.");
		}
		$obj_stats = new Stats;
		
		$urlRow = $this->url_from_word_pair($word_pair);

		if(empty($urlRow)) {
			throw new \Exception("Short code does not appear to exist");
		}

		return $urlRow["long_url"];
	}


	
}