<?php

/**
 * ContestsFactory
 *
 * @author joemmanuel
 */
require_once 'UserFactory.php';

class ContestsFactory {

	/**
	 * Returns a Request object with complete context to create a contest
	 * 
	 * @param string $title
	 * @param string $public
	 * @param Users $contestDirector
	 * @return Request
	 */
	public static function getRequest($title = null, $public = 1, Users $contestDirector = null) {

		if (is_null($contestDirector)) {
			$contestDirector = UserFactory::createUser();
		}

		if (is_null($title)) {
			$title = Utils::CreateRandomString();
		}

		// Set context
		$r = new Request();
		$r["title"] = $title;
		$r["description"] = "description";
		$r["start_time"] = Utils::GetPhpUnixTimestamp() - 60 * 60;
		$r["finish_time"] = Utils::GetPhpUnixTimestamp() + 60 * 60;
		$r["window_length"] = null;
		$r["public"] = 1;
		$r["alias"] = substr($title, 0, 20);
		$r["points_decay_factor"] = ".02";
		$r["partial_score"] = "0";
		$r["submissions_gap"] = "0";
		$r["feedback"] = "yes";
		$r["penalty"] = 100;
		$r["scoreboard"] = 100;
		$r["penalty_time_start"] = "contest";
		$r["penalty_calc_policy"] = "sum";

		return array (
			"request" => $r,
			"director" => $contestDirector);
	}

	public static function createContest($title = null, $public = 1, Users $contestDirector = null) {		
		
		// Create a valid contest Request object
		$contestData = ContestsFactory::getRequest($title, $public, $contestDirector);
		$r = $contestData["request"];
		$contestDirector = $contestData["director"];

		// Log in the user and set the auth token in the new request
		$r["auth_token"] = OmegaupTestCase::login($contestDirector);

		// Call the API
		$response = ContestController::apiCreate($r);		
		
		return array(
			"director" => $contestData["director"],
			"request" => $r,
			);
	}

}

