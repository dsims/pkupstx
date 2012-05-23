<?php defined('SYSPATH') or die('No direct script access.');
class Test_Controller extends Template_Controller {

	// Disable this controller when Kohana is set to production mode.
	// See http://docs.kohanaphp.com/installation/deployment for more details.
	//const ALLOW_PRODUCTION = FALSE;

	// Set the name of the template to use
	public $template = 'test';

	public function index()
	{
		echo "TESTING LOGIN<br />";

		if(Auth::instance()->auto_login())
		{
			echo "AUTOLOGGEDIN<br />";
		}
		if (Auth::instance()->logged_in())
		{
			echo "LOGGEDIN<br />";
		}

	}
} // End Welcome Controller