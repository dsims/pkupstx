<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Auth module demo controller. This controller should NOT be used in production.
 * It is for demonstration purposes only!
 *
 * $Id: auth_demo.php 3267 2008-08-06 03:44:02Z Shadowhand $
 *
 * @package    Auth
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Activity_Controller extends Site_Controller {

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'Event Log';
	}
	
	function index() {
		$view = new View('activity/list');
		$results = new Eventlog_Model();
		$results = $results->FindFinal(0, 30, 'type <= 9');
		$output = array();
		foreach($results as $log)
		{
			$log->format(true);
			$output[] = $log;
		}
		$view->logs = $output;

		$this->template->content = $view;
	}
		
	public function view($id)
	{
		$view = new View('activity/user');
		$id = intval($id);

		$user = ORM::factory('user', $id);
		
		$results = new Eventlog_Model();
		$results = $results->FindFinal(0, 20, 'type <= 9 AND user_id = '.$id);
		$output = array();
		foreach($results as $log)
		{
			$log->format();
			$output[] = $log;
		}
		$view->logs = $output;


		$db = Database::instance();
		$result = $db->query('SELECT COUNT(*) as count FROM topics WHERE user_id = '.$user->id);
		$view->posts = $result[0]->count;
		$result = $db->query('SELECT COUNT(*) as count FROM comments WHERE user_id = '.$user->id);
		$view->replies = $result[0]->count;
		$result = $db->query('SELECT COUNT(*) as count, type FROM awards_users WHERE user_id = ? GROUP BY type ORDER BY type',$user->id);
		$view->awardcounts = $result;


		$tabs = array(); $selectedTab = 0;
		$tabs['Posts'] = 'topics/user/'.$user->id.'/'.slug::format($user->title);;
		$tabs['Board'] = 'topics/board/'.$user->board_id.'/'.slug::format($user->title);
		$tabs['Activity'] = 'activity/'.$user->id.'/'.slug::format($user->title);
		$tabs['Profile'] = 'profile/'.$user->username;
		$selectedTab = 3;
		$view->tabs = $tabs;
		$view->selectedTab = $selectedTab;
		$view->user=$user;

		$this->template->content = $view;
	}
}