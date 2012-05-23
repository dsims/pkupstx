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
class Site_Controller extends Template_Controller {

	public $isAjax = false;
	public $isAdmin = false;
	public $isLoggedIn = false;
	public $isMobile = 0;
	public $isMobileSet = 0;
	public $subdomain = 'digibutter';

	function __construct() {

		$this->subdomain = 'digibutter';
		$this->session = Session::instance();

		parent::__construct();

		
		$this->template->title = 'pickupsticks';
		
		if (request::is_ajax() || isset($_GET['isAjax'])) {
			//$this->isAjax = true;
			//$this->auto_render = FALSE;
		}

		if($this->session->get('mobile') === FALSE)
			$this->isMobileSet = 0;
		else
			$this->isMobileSet = 1;
		$this->isMobile = $this->session->get('mobile');
		$this->isMobile = true;
		
		//if($this->isMobile)
		$this->template = new View('template_m');
			
		$this->template->noindex = false;
		if(isset($_GET['archive']))
			$this->template->noindex = true;

		$isLoggedIn = false;
		if (Auth::instance()->logged_in())
		{
			$this->isLoggedIn = true;
		}
		//only auto_login if not an ajax call (I think we are getting session race conditions)
		if(!$this->isAjax && !$isLoggedIn && Auth::instance()->auto_login())
		{
			$this->isLoggedIn = true;
		}

		$this->db = Database::instance();

		if ($this->isLoggedIn)
		{
			if(!$this->isAjax)
			{
				$user = Session::instance()->get('auth_user');
				$this->template->user=$user;
			}
			$this->isAdmin = (Auth::instance()->logged_in('admin') ? true : false);
		}
		if(!$this->isAjax)
		{
			$gametags = '';
			$subs = $this->db->query('SELECT b.id as id, COALESCE(u.title, g.title, b.title, \'no name\') as title FROM boards b LEFT JOIN games g ON g.id = b.owner_id AND b.type = \'g\' LEFT JOIN users u ON u.id = b.owner_id AND b.type = \'u\' AND u.title != \'n00b\' ORDER BY b.date_submit DESC LIMIT 5');
			$gsubs = $this->db->query('SELECT b.id as id, b.owner_id as owner_id, COALESCE(g.title, \'no name\') as title FROM boards b JOIN games g ON g.id = b.owner_id where b.type = \'g\'  ORDER BY b.date_submit DESC LIMIT 5');
			if(strlen($gametags))
				$gsubs = $this->db->query('SELECT g.id as owner_id, COALESCE(g.title, \'no name\') as title FROM games g join `game_statuses` gs ON gs.game_id = g.id join boards b ON b.owner_id = g.id JOIN topics t ON t.board_id = b.id JOIN boards_tags bt ON bt.board_id = b.id WHERE owned = 2 AND bt.tag_id IN ('.$gametags.') AND (t.date_added > UNIX_TIMESTAMP() - 2629743) GROUP BY g.id  ORDER BY (COUNT(t.id)) DESC LIMIT 10');
			else if($this->subdomain == 'digi')
				$gsubs = $this->db->query('SELECT g.id as owner_id, COALESCE(g.title, \'no name\') as title FROM games g join `game_statuses` gs ON gs.game_id = g.id join boards b ON b.owner_id = g.id JOIN topics t ON t.board_id = b.id WHERE owned = 2 AND (t.date_added > UNIX_TIMESTAMP() - 2629743) GROUP BY g.id  ORDER BY (COUNT(t.id)) DESC LIMIT 10');
			else
				$gsubs = $this->db->query('SELECT g.id as owner_id, COALESCE(g.title, \'no name\') as title FROM games g join `game_statuses` gs ON gs.game_id = g.id join boards b ON b.owner_id = g.id JOIN topics t ON t.board_id = b.id WHERE owned = 2 AND (t.date_added > UNIX_TIMESTAMP() - 2629743) GROUP BY g.id  ORDER BY (COUNT(t.id)) DESC LIMIT 5');
			$usubs = $this->db->query('SELECT * FROM users u where title != \'n00b\' ORDER BY u.created DESC LIMIT 5');
			$this->template->subs = $subs;
			$this->template->gsubs = $gsubs;
			$this->template->usubs = $usubs;

			$asubs = $this->db->query('SELECT b.id as id, b.title as title from boards b where b.type = \'a\' ORDER BY b.date_submit DESC LIMIT 5');
			$this->template->asubs = $asubs;
		}
		$this->template->set_global('isAdmin', $this->isAdmin);
		$this->template->set_global('isLoggedIn', $this->isLoggedIn);
		$this->template->set_global('subdomain', $this->subdomain);
	}
} // End Auth Controller