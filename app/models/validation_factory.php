<?php
class validation_factory {
	/**
	 * check whether the email string is a valid email address using a regular expression
	 * @param $emailStr - the input email string
	 * @return boolean indicating whether it is a valid email or not
	 */
	public function isEmailValid($emailStr){
		$regex = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/i";
		if(!preg_match($regex, $emailStr)) return (false);
		else return (true);
	}
	/**
	 * @param $string - the input string
	 * @param $maxchars - the maximum length of the input string
	 * @return boolean indicating whether it is a valid string of the right max length
	 */
	public function isLengthStringValid($string, $maxchars){
		if (is_string($string))
			if (strlen($string)<=$maxchars) return (true);	
		return (false);
	}

	public function isDateValid($cDate) {
		$cDate = explode('-', $cDate);
		$month = $cDate[0];
		$day   = $cDate[1];
		$year  = $cDate[2];
		$regex = "/(?:(?:19|20)[0-9]{2})/";
		if (preg_match($regex, $month)) {
		    return (true);
		} else {
			return (false);
		}
	}
}
?>