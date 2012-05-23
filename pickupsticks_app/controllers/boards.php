<?php
class boards_Controller extends Site_Controller {

	public $template = 'template';

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'boards';
	}

	private function merge($arr1, $arr2)
	{
		$return = array();
		foreach($arr1 as $b1)
		{
			foreach($arr2 as $b2)
			{
				if($b2->date_submit > $b1->date_submit)
				{
					if(!array_key_exists($b2->id, $return))
					{
						$return[$b2->id] = $b2;
						array_shift($b2);
					}
				}
				else
					break;
			}
			if(!array_key_exists($b1->id, $return))
				$return[$b1->id] = $b1;
		}
		foreach($arr2 as $b2)
		{
			if(!array_key_exists($b2->id, $return))
			{
				$return[$b2->id] = $b2;
			}
		}
		return $return;
	}

	function index() {
		$view = new View('boards/directory');
        $view->loggedin = false;

		$b = new Board_Model();
		$this->db = Database::instance();

		$lists = array();

		$asubs = $this->db->query('SELECT b.id as id, b.date_submit, b.description, COALESCE(b.title, \'no name\') as title FROM boards b where b.type = \'a\' ORDER BY b.date_submit DESC LIMIT 2');
		$subs = $this->db->query('SELECT b.id as id, b.date_submit, b.description, COALESCE(b.title, \'no name\') as title FROM boards b where b.type = \'\' ORDER BY b.date_submit DESC LIMIT 5');
		$gsubs = $this->db->query('SELECT b.id as id, b.date_submit, b.description, COALESCE(g.title, \'no name\') as title FROM boards b JOIN games g ON g.id = b.owner_id where b.type = \'g\'  ORDER BY b.date_submit DESC LIMIT 5');
		$usubs = $this->db->query('SELECT b.id as id, b.date_submit, b.description, COALESCE(u.title, \'no name\') as title FROM boards b JOIN users u ON u.id = b.owner_id where b.type = \'u\'  ORDER BY b.date_submit DESC LIMIT 5');


		$topic = new Topic_Model();
		$output = array();
		foreach($asubs as $board)
		{
			$board->date_submit_format = $topic->formatTime($board->date_submit);
			$output[$board->id] = $board;
		}$asubs = $output; $output = array();

		foreach($subs as $board)
		{
			$board->date_submit_format = $topic->formatTime($board->date_submit);
			$output[$board->id] = $board;
		}$subs = $output; $output = array();
		foreach($gsubs as $board)
		{
			$board->date_submit_format = $topic->formatTime($board->date_submit);
			$output[$board->id] = $board;
		}$gsubs = $output; $output = array();
		foreach($usubs as $board)
		{
			$board->date_submit_format = $topic->formatTime($board->date_submit);
			$output[$board->id] = $board;
		}$usubs = $output;


		if ($this->isLoggedIn) //'admin'
		{
			$view->loggedin = true;

			$user = Session::instance()->get('auth_user');
			$subs2 = $this->db->query('SELECT s.board_id as id, r.date_submit, r.title as title, r.description as description from subscriptions s JOIN boards r on r.id = s.board_id where type = \'\' AND s.user_id = ? LIMIT 5', array($user->id));
			$gsubs2 = $this->db->query('SELECT s.board_id as id, r.date_submit,  g.title as title, r.description as description from subscriptions s JOIN boards r on r.id = s.board_id JOIN games g ON g.id = r.owner_id where type = \'g\' AND s.user_id = ? LIMIT 5', array($user->id));
			$usubs2 = $this->db->query('SELECT s.board_id as id, r.date_submit, u.title as title, r.description as description from subscriptions s JOIN boards r on r.id = s.board_id JOIN users u ON u.id = r.owner_id where type = \'u\' AND s.user_id = ? LIMIT 5', array($user->id));

			$output = array();
			foreach($subs2 as $board)
			{
				$board->date_submit_format = $topic->formatTime($board->date_submit);
				$output[$board->id] = $board;
			}$subs2 = $output; $output = array();
			foreach($gsubs2 as $board)
			{
				$board->date_submit_format = $topic->formatTime($board->date_submit);
				$output[$board->id] = $board;
			}$gsubs2 = $output; $output = array();
			foreach($usubs2 as $board)
			{
				$board->date_submit_format = $topic->formatTime($board->date_submit);
				$output[$board->id] = $board;
			}$usubs2 = $output;


			$subs = $this->merge($subs, $subs2);
			$gsubs = $this->merge($gsubs, $gsubs2);
			$usubs = $this->merge($usubs, $usubs2);

			$list = new Boardlist_Model();
			$lists = $list->FindWhere('user_id', $user->id, 0);

			//$view->hiddenboards = $this->db->query('SELECT e.board_id as id, b.title from exclude_boards e JOIN boards b on b.id = e.board_id where type = \'\' AND e.user_id = ?', array($user->id));
			//$view->hiddengboards = $this->db->query('SELECT e.board_id as id, g.title from exclude_boards e JOIN boards b on b.id = e.board_id JOIN games g ON g.id = b.owner_id where type = \'g\' AND e.user_id = ?', array($user->id));
			//$view->hiddenuboards = $this->db->query('SELECT e.board_id as id, u.title from exclude_boards e JOIN boards b on b.id = e.board_id JOIN users u ON u.id = b.owner_id where type = \'u\' AND e.user_id = ?', array($user->id));
			//$view->hiddenusers = $this->db->query('SELECT poster_id as id, title, board_id from exclude_users JOIN users u on u.id = poster_id where user_id = ?', array($user->id));
		}

		$view->asubs = $asubs;
		$view->subs = $subs;
		$view->gsubs = $gsubs;
		$view->usubs = $usubs;
		$view->lists = $lists;

		$board = new Board_Model;
		//$view->boards = $board->Find(20);
		$this->template->content = $view;
		$this->template->title = 'board Directory';
	}

	function go(){
		$get = $this->input->get();
		if(isset($get['boardid']) && intval($get['boardid']) > 0)
		{
		$boardid = intval($get['boardid']);
		url::redirect('topics/board/'.$boardid);
		}
		else
			url::redirect('boards/browse');
	}

	public function manage($boardid)
	{
		$boardid = intval($boardid);

		if ($this->isLoggedIn) //'admin'
		{
			$user = Session::instance()->get('auth_user');

			$board = new Board_Model;
			$board->Load($boardid);

			//check if allowed to manage
			if($user->id == $board->owner_id || $this->isAdmin)
			{
				$view = new View('boards/manage');
				$users = $this->db->query('SELECT u.id, u.title as title, u.username as username from boards_banned b JOIN users u ON u.id = b.user_id where b.board_id = ?', array($boardid));
				$susers = $this->db->query('SELECT u.id, u.title as title, u.username as username from subscriptions s JOIN users u ON u.id = s.user_id where s.pending = 0 AND s.board_id = ?', array($boardid));
				$pusers = $this->db->query('SELECT u.id, u.title as title, u.username as username from subscriptions s JOIN users u ON u.id = s.user_id where s.pending = 1 AND s.board_id = ?', array($boardid));
				$view->users = $users;
				$view->susers = $susers;
				$view->pusers = $pusers;
				$view->board = $board;
				$this->template->content = $view;
				$this->template->title = 'Manage Board';
            }
		}
	}

    public function subscriptions()
    {
		if ($this->isAjax) {

            if ($this->isLoggedIn) //'admin'
            {
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT s.board_id, r.title as board_title from subscriptions s JOIN boards r on r.id = s.board_id where s.user_id = ?', array($user->id));
                echo $this->ajaxData($result);
                return;
            }
		}
        //echo $this->ajaxReply($replyCode = 500,$replyText = 'error');
    }

    function create() {
		$view = new View('boards/edit');
		$board = new Board_Model;
        $view->board = $board;
		$this->template->content = $view;
		$this->template->title = 'Create board';
	}

    function browse($type='') {

		$view = new View('boards/list');

        switch($type)
        {
        case 'users':
            $type = 'u';
            $area = 'Users';
            break;
        case 'games':
            $type = 'g';
            $area = 'Products';
            break;
        case 'products':
            $type = 'g';
            $area = 'Products';
            break;            
        default:
            $type = '';
            $area = 'All';
        }

        $get = $this->input->get();
        $board = new Board_Model;
        $newboards = $board->FindNewest(20, $type);
        $actboards = $board->FindActive(20, $type);

		$wanted = array();
		if($type == 'g')
		{
			$db = Database::instance();
			$result = $db->query('SELECT COUNT(1) as count, g.title, NULL as description, b.id as id, g.id as owner_id FROM game_statuses s JOIN games g on g.id=s.game_id JOIN boards b on b.id = g.board_id WHERE owned = 2 GROUP by s.game_id ORDER BY count DESC LIMIT 0,5');
			$wanted = $result;
		}
		$view->wanted = $wanted;

		$view->loggedin = false;
		if ($this->isLoggedIn) //'admin'
		{
			$view->loggedin = true;

			$user = Session::instance()->get('auth_user');
			$view->subs = $board->FindSubscribedByUser($user->id, $type, '', 'date_submit DESC');
			$view->hiddenboards = $board->FindHiddenByUser($user->id, $type, '', 'date_submit DESC');
			if($type == 'u')
			{
				$view->hiddenusers = $board->FindHiddenUsersByUser($user->id);
			}
			//$subs = $this->db->query('SELECT s.board_id as id, r.date_submit, r.title as title, r.description as description from subscriptions s JOIN boards r on r.id = s.board_id where type = \'\' AND s.user_id = ?', array($user->id));
			//$gsubs = $this->db->query('SELECT s.board_id as id, r.date_submit,  g.title as title, r.description as description from subscriptions s JOIN boards r on r.id = s.board_id JOIN games g ON g.id = r.owner_id where type = \'g\' AND s.user_id = ?', array($user->id));
			//$usubs = $this->db->query('SELECT s.board_id as id, r.date_submit, u.title as title, r.description as description from subscriptions s JOIN boards r on r.id = s.board_id JOIN users u ON u.id = r.owner_id where type = \'u\' AND s.user_id = ?', array($user->id));

			/*
			$view->hiddenboards = $this->db->query('SELECT e.board_id as id, b.title from exclude_boards e JOIN boards b on b.id = e.board_id where type = \'\' AND e.user_id = ?', array($user->id));
			$view->hiddengboards = $this->db->query('SELECT e.board_id as id, g.title from exclude_boards e JOIN boards b on b.id = e.board_id JOIN games g ON g.id = b.owner_id where type = \'g\' AND e.user_id = ?', array($user->id));
			$view->hiddenuboards = $this->db->query('SELECT e.board_id as id, u.title from exclude_boards e JOIN boards b on b.id = e.board_id JOIN users u ON u.id = b.owner_id where type = \'u\' AND e.user_id = ?', array($user->id));
			$view->hiddenusers = $this->db->query('SELECT poster_id as id, title, board_id from exclude_users JOIN users u on u.id = poster_id where user_id = ?', array($user->id));
			*/
		}


        $output = array();
        foreach($newboards as $board)
        {
            $board->format();
            $output[] = $board;
        }
        $newboards = $output;
        $output = array();
        foreach($actboards as $board)
        {
            $board->format();
            $output[] = $board;
        }
        $actboards = $output;

        if(isset($get['boardid']))
        {
            $boardid = intval($get['boardid']);
        }

		$board = new Board_Model;
        $view->area = $area;
        $view->type = $type;
        $view->newboards = $newboards;
        $view->actboards = $actboards;
		$this->template->content = $view;
		$this->template->title = $area.' Directory';
	}

    function search() {
        $type = '';
        $terms = '';
        $get = $this->input->get();
        if(isset($get['terms']))
        {
            $terms = $get['terms'];
        }
        if(isset($get['type']))
        {
            $type = $get['type'];
        }

        switch($type)
        {
        case 'u':
            $type = 'u';
            $area = 'Users';
            break;
        case 'g':
            $type = 'g';
            $area = 'Games';
            break;
        default:
            $type = 'g';
            $area = 'Products';
        }

        $board = new Board_Model;
        $boards = $board->Search(20, $type, $terms);

		$view = new View('boards/searchresults');
        $view->area = $area;
        $view->type = $type;
        $view->terms = $terms;
		$board = new Board_Model;
        $view->boards = $boards;
		$this->template->content = $view;
		$this->template->title = 'board Directory';
	}

    public function submit()
	{
		if ($this->isLoggedIn) //'admin'
		{
            $user = Session::instance()->get('auth_user');
			$board = new Board_Model();

			$db = Database::instance();
			$result = $db->query('SELECT * from boards where owner_id = ? AND date_added > ?', $user->id, time()-86400);
			if($result->count() > 3)
			{
				url::redirect('topics');
			}

			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');

			if ($post->validate())
			{
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
				if(isset($postdata_array['title']))
					$board->title = $postdata_array['title'];
				if(isset($postdata_array['description']))
					$board->description = $postdata_array['description'];
                $board->owner_id = $user->id;
				$board->Save();
			}
            else if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>0));
                return;
            }
            //return success
            if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>1,'id'=>$board->id));
                return;
            }

            url::redirect('topics/?boardid='.$board->id);
		}
		else
		{
			url::redirect('boards/');
		}
	}

	public function edit()
	{
		if ($this->isLoggedIn) //'admin'
		{
            $user = Session::instance()->get('auth_user');

			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');

			if ($post->validate())
			{
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
				$boardid = 0;
				$privacy = 0;
				if(isset($postdata_array['boardid']))
					$boardid = intval($postdata_array['boardid']);
				if(isset($postdata_array['privacy']))
					$privacy = intval($postdata_array['privacy']);
				$board = new Board_Model();
				$board->Load($boardid);
				if($board->owner_id == $user->id || $this->isAdmin)
				{
					$board->privacy = $privacy;
					$board->Save();
				}
			}

            url::redirect('boards/manage/'.$board->id);
		}
		else
		{
			url::redirect('boards/');
		}
	}

	public function ban()
	{
		$tid = 0;
		$cid = 0;
		$bannedid = 0;
		$post = $this->input->post();
		if(isset($post['tid']))
        {
            $tid = intval($post['tid']);
        }
		if(isset($post['cid']))
        {
            $cid = intval($post['cid']);
        }

		//check for valid conditions
		if(($tid == 0 && $cid == 0) || !$this->isAjax || !$this->isLoggedIn)
		{
			echo json_encode(array('returnCode'.$tid=>0));
			return;
		}

		$user = Session::instance()->get('auth_user');

		$boardid = 0;
		if($cid > 0)
		{
			$comment = new Comment_Model;
			$comment->Load($cid);
			$tid = $comment->topic_id;
			$bannedid = $comment->user_id;
		}
		$topic = new Topic_Model;
		$topic->Load($tid);
		$bannedid = ($bannedid == 0) ? $topic->user_id : $bannedid;

		$board = new Board_Model;
		$board->Load($topic->board_id);

		//check if allowed to delete
		if($user->id == $board->owner_id || $this->isAdmin)
		{
			$board->BanUser($bannedid);
		}
		else
		{
			echo json_encode(array('returnCode2'=>0));
			return;
		}

		//return success
		echo json_encode(array('returnCode'=>1));
		return;
	}

	public function delBan()
	{
		$userid = 0;
		$boardid = 0;
		$post = $this->input->post();
		if(isset($post['userid']))
        {
            $userid = intval($post['userid']);
        }
		if(isset($post['boardid']))
        {
            $boardid = intval($post['boardid']);
        }

		//check for valid conditions
		if($userid == 0 || $boardid == 0 || !$this->isAjax || !$this->isLoggedIn)
		{
			echo json_encode(array('returnCode'.$tid=>0));
			return;
		}

		$user = Session::instance()->get('auth_user');

		$board = new Board_Model;
		$board->Load($boardid);

		//check if allowed to delete
		if($user->id == $board->owner_id || $this->isAdmin)
		{
			$board->UnBanUser($userid);
		}
		else
		{
			echo json_encode(array('returnCode'=>0));
			return;
		}

		//return success
		echo json_encode(array('returnCode'=>1));
		return;
	}

	public function ddselect()
	{
		$q = strtolower($_GET["q"]);
		if (!$q) return;
		$board = new Board_Model;
        $boards = $board->Search(20, 'any', $q);
		foreach($boards as $board)
		{
			echo $board->title.'|'.$board->id."\n";
		}
	}

	public function ddselectany()
	{
		$q = strtolower($_GET["q"]);
		if (!$q) return;
		$board = new Board_Model;
        $boards = $board->Search(20, 'all', $q);
		foreach($boards as $board)
		{
			$username = ($board->type == 'u') ? ' ('.$board->owner.')' : '';
			echo $board->title.$username.'|'.$board->id."\n";
		}
	}

    private function ajaxData($data, $interval)
	{
		return $this->ajaxReply(201, 'data', $data, $interval);
	}
	private function ajaxReply($replyCode = 200,$replyText = 'ok', $result=null, $interval=0) {
		if($result != null && !is_array($result) && ($result instanceof Mysql_Result))
			$result = $result->result_array();
		if(is_array($result))
		{
			$arr = array();
			$arr['data'] = $result;
            $interval = intval($interval);

			$result= array(
                "replyCode"=>$replyCode,
                "replyText"=>$replyText,
                "interval"=>$interval
            ) + $arr;
		}
		else
			$result= array("replyCode"=>$replyCode, "replyText"=>$replyText);

		$result = json_encode($result);
		return $result;
	}
}
?>