<?php  
//Retrieve array-information through CRON-Job
class StatAnalysis
{

	protected static $chars = "-abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
	protected $pdo;

	/**
	* Creates PDO for database communication
	*/
	public function __construct() { //Check if public
		ini_set('display_errors', 'On');
		error_reporting(E_ALL | E_STRICT);
		try {
			$pdo_local = new PDO(C_DB_PDODRIVER . ":host=" . C_DB_HOST . ";dbname=" . C_DB_DATABASE . ";port=3306", C_DB_USERNAME, C_DB_PASSWORD);
			$this->pdo = $pdo_local;
		} catch (\PDOException $e) {
			header("Location: error.html");
			exit;
		}
	}

	// Select total views
	/********************CONTROLLER SPECIFIC FUNCTIONS START HERE********************/
	/**
	 * Returns a word-id that was is not yet used up
	 * @return string
     */
	public function get_info($word_pair) {
		try {
			$query = "SELECT 
						a_word_pair as wp, 
						a_total_views as t_count,
						a_unique_views as u_count,
						a_table_views as table_count,
						a_browser as brow,
						a_os as os,
						a_referrers as ref
					FROM table_analysis 
					WHERE a_word_pair = :word_pair;";

			$stm = $this->pdo->prepare($query);
			$para = array("word_pair"=>$word_pair);
			$stm->execute($para);
			$result = $stm->fetchAll(PDO::FETCH_ASSOC);
			return $result;
		} catch (Exception $e) {
			//be sure to take out these exception messages afterwards
			echo "Exception caught within 'insertUrlInDb': \n\n" , $e->getMessage() , "\n";
			return false;
		}
	}
}

