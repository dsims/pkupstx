<?php defined('SYSPATH') or die('No direct script access.');
class Users_Controller extends Site_Controller {

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'Users';
	}
	
	function index() {
		$view = new View('users/list');
		$users = ORM::factory('user')->find_all(20);
		$view->users = $users;
		$this->template->content = $view;
		$this->template->title = 'Users';
	}
		
	public function view($userID)
	{
		$user = ORM::factory('user', $userID);
        //url::redirect('topics/board/'.$user->board_id.'/'.slug::format($user->title));
/*
		$topic = new Topic_Model();
		$topics = $topic->FindFinal(0,0,0,2,3,'t.user_id','=',$user->id);
		$output = array();
        foreach($topics as $topic)
        {
            $topic->format();
            $output[] = $topic;
        }
*/
		if($user->board_id == 0) //bot
		{
			url::redirect('topics/user/'.$user->id.'/'.slug::format($user->title));
		}

		$view = new View('users/view');

		$db = Database::instance();
		$result = $db->query('SELECT title,game_id as id FROM game_statuses s JOIN games g on id=game_id WHERE owned = 1 AND s.user_id = '.$user->id);
		$view->own = $result;
		$result = $db->query('SELECT title,game_id as id FROM game_statuses s JOIN games g on id=game_id WHERE owned = 2 AND s.user_id = '.$user->id);
		$view->want = $result;
		$result = $db->query('SELECT g.title,game_id as id, t.id as topic_id, t.rating FROM game_statuses s JOIN games g on g.id=game_id LEFT JOIN topics t on t.rating_game_id = g.id AND t.user_id = '.$user->id.' WHERE played = 1 AND s.user_id = '.$user->id);
		$view->played = $result;
		$result = $db->query('SELECT g.title,game_id as id, t.id as topic_id, t.rating FROM game_statuses s JOIN games g on g.id=game_id LEFT JOIN topics t on t.rating_game_id = g.id AND t.user_id = '.$user->id.' WHERE played = 2 AND s.user_id = '.$user->id);
		$view->beat = $result;

		$result = $db->query('SELECT id,title, description, type FROM awards a JOIN awards_users au on au.award_id = a.id WHERE au.user_id = ? ORDER BY awarded',$user->id);
		$view->awards = $result;
		$result = $db->query('SELECT COUNT(*) as count, type FROM awards_users WHERE user_id = ? GROUP BY type ORDER BY type',$user->id);
		$view->awardcounts = $result;

		$result = $db->query('SELECT COUNT(*) as count FROM topics WHERE user_id = '.$user->id);
		$view->posts = $result[0]->count;
		$result = $db->query('SELECT COUNT(*) as count FROM comments WHERE user_id = '.$user->id);
		$view->replies = $result[0]->count;
		
		$view->isfollowing = false;
		if ($this->isLoggedIn){
		$myuser = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT poster_id from subscription_users where poster_id = ? AND user_id = ?', array($user->id, $myuser->id));
		
                if($result->count() == 0)
                    $view->isfollowing = false;
                else
                    $view->isfollowing = true;
		}

		$tabs = array(); $selectedTab = 0;
		$tabs['Posts'] = 'topics/user/'.$user->id.'/'.slug::format($user->title);;
		$tabs['Activity'] = 'activity/'.$user->id.'/'.slug::format($user->title);
		$tabs['Profile'] = 'profile/'.$user->username;
		$selectedTab = 4;
		$view->tabs = $tabs;
		$view->selectedTab = $selectedTab;

		//$view->topics = $output;
		$view->user=$user;
		$view->id = $user->id;
		$this->template->content = $view;
		$this->template->title = 'User: '.$user->title;
	}

	public function edit()
	{
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');

			$post = $this->input->post();

			if(sizeof($post) > 0)
			{

				$post = Validation::factory($post)
					->pre_filter('trim')
					->add_rules('email', 'email')
					//->add_rules('username', 'required','length[3,32]','alpha_dash')
					->add_rules('password', 'length[4,40]');
				if ($post->validate() && !stristr($post['username'], 'fuck'))
				{
					//if($user->username == $post['username'] || $user->old_username == $post['username'] || !$user->username_exists($post['username']))
					//{
						//$postdata_array = $post->safe_array();
						$postdata_array = $post;
						if(strlen($post['title']) == 0)
							$user->title = 'n00b';
						else
							$user->title = $post['title'];
						if(isset($post['hometype']) && strlen($post['hometype']))
							$user->hometype = $post['hometype'];
						else
							$user->hometype = 'user';
						if(isset($post['censor']) && isset($post['censor']) && $post['censor'] == 1)
							$user->censor = 1;
						else
							$user->censor = 0;
						$user->email = $post['email'];
						//$user->username = $post['username'];
						if(strlen($post['password']))
							$user->password = $post['password'];
						$user->save();
						$this->template->success = 'Changes Saved';
					//}
					//else
					//	$this->template->error = 'Sorry, that username is already taken or reserved.';
				}
				else
				{
					$this->template->error = 'Invalid fields';
				}
			}

			$list = new Boardlist_Model();
			$lists = $list->FindWhere('user_id', $user->id, 0);
			$listarr = array();
			$listtemp = new Boardlist_Model();
			$listtemp->id = 'all'; $listtemp->title = 'All';
			array_push($listarr, $listtemp);
			$listtemp = new Boardlist_Model();
			$listtemp->id = 'gaming'; $listtemp->title = 'All Gaming';
			array_push($listarr, $listtemp);
			$listtemp = new Boardlist_Model();
			$listtemp->id = 'user'; $listtemp->title = 'All User Posts';
			array_push($listarr, $listtemp);
			$listtemp = new Boardlist_Model();
			$listtemp->id = 's'; $listtemp->title = 'Subscriptions';
			array_push($listarr, $listtemp);
			foreach($lists as $list)
			{
				array_push($listarr, $list);
			}

			$user = ORM::factory('user', $user->id);
			if($user->hometype == '')
				$user->hometype = 'user'; //default setting
			$view = new View('users/edit');
			$view->openids = $user->user_identities;
			$view->user=$user;
			$view->lists = $listarr;
			$this->template->title = 'Edit Profile';
			$this->template->content = $view;
		}
		else
		{
			url::redirect('auth/login');
		}
	}
	
	public function logout()
	{
		// Force a complete logout
		Auth::instance()->logout(TRUE);

		// Redirect back to the login page
		url::redirect('topics/');
	}

	public function setMobile($val)
	{
		$get = $_GET;
		$redir = '';
		//Parse the query paramters
		if(isset($get['redir']))
		{
			$redir = $get['redir'];
		}
		$this->session->set('mobile', $val);
		url::redirect($redir);
	}

    private function ajaxData($data, $totalcount)
	{
		return $this->ajaxReply(201, 'data', $data, $totalcount);
	}
	private function ajaxReply($replyCode = 200,$replyText = 'ok', $result=null, $totalcount=0) {
		if($result != null && !is_array($result) && ($result instanceof Mysql_Result))
			$result = $result->result_array();
		if(is_array($result))
		{
			$arr = array();
			$arr['data'] = $result;
            $totalcount = intval($totalcount);

			$result= array(
                "replyCode"=>$replyCode,
                "replyText"=>$replyText,
                "totalRecords"=>$totalcount
            ) + $arr;
		}
		else
			$result= array("replyCode"=>$replyCode, "replyText"=>$replyText);

		$result = json_encode($result);
		return $result;
	}
}
