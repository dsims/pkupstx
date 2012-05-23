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
class Awards_Controller extends Site_Controller {

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'Awards';
	}
	
	function index() {
		$view = new View('awards/list');

		$db = Database::instance();
		$result = $db->query('SELECT id,title, description FROM awards a order by id');
		$view->awards = $result;

		$this->template->content = $view;
		$this->template->title = 'Awards';
	}
		
	public function view($id)
	{
		$view = new View('awards/view');
		$id = intval($id);

		$db = Database::instance();
		
		$result = $db->query('SELECT id,title, description FROM awards a WHERE a.id = ?',$id);
		$view->award = $result->current();

		$result = $db->query('SELECT title, au.type FROM users u JOIN awards_users au on au.user_id = u.id WHERE au.award_id = ? ORDER BY awarded',$id);
		$view->users = $result;
		
		$this->template->content = $view;
		$this->template->title = 'Award: '.$view->award->title;
	}
}