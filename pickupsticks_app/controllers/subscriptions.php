<?php
class Subscriptions_Controller extends Site_Controller {

	public $template = 'template';

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'Subscriptions';
	}

	//default app listing
	function index() {
	}

    public function get()
    {
		if ($this->isAjax) {

            if ($this->isLoggedIn) //'admin'
            {
                $get = $this->input->get();
                $q = "''";
                if(isset($get['query']))
                    $q = mysql_real_escape_string('%'.$get['query'].'%');
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query("SELECT coalesce(bu.title, g.title, r.title, 'no name') as board_title, s.board_id from subscriptions s
JOIN boards r on r.id = s.board_id 
LEFT JOIN users bu ON bu.board_id = r.id
LEFT JOIN games g ON g.board_id = r.id
where s.user_id = ? AND (bu.title LIKE ? OR g.title LIKE ? OR r.title LIKE ?) ", array($user->id, $q, $q, $q));
                echo $this->ajaxData($result,0);
                return;
            }
		}
        //echo $this->ajaxReply($replyCode = 500,$replyText = 'error');
    }

    public function add($board_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn) //'admin'
            {
                $board_id = intval($board_id);

				$board = new Board_Model();
				$board->Load($board_id);
				$pending = ($board->privacy == 2) ? 1 : 0;

                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT board_id from subscriptions where board_id = ? AND user_id = ?', array($board_id, $user->id));
                if($result->count() == 0)
                {
                    $this->db->query('INSERT INTO subscriptions (board_id,user_id,pending) values (?,?,?)', array($board_id,$user->id,$pending));

					$log = new Eventlog_Model();
					$log->user_id = $user->id;
					$log->target_id = $board_id;
					if($board->type != 'g')
						$log->user2_id = $board->owner_id;
					$log->type = Eventlog_Model::SubBoard;
					$log->Save();
                }
                return;
            }
		}
    }

    public function addfriend($poster_id)
    {
    	$this->auto_render = FALSE;
		//if ($this->isAjax) {
            if ($this->isLoggedIn) //'admin'
            {
                $poster_id = intval($poster_id);

				//$board = new Board_Model();
				//$board->Load($board_id);
				//$pending = ($board->privacy == 2) ? 1 : 0;
				$pending = 0;
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT poster_id from subscription_users where poster_id = ? AND user_id = ?', array($poster_id, $user->id));
                if($result->count() == 0)
                {
                    $this->db->query('INSERT INTO subscription_users (poster_id,user_id,pending) values (?,?,?)', array($poster_id,$user->id,$pending));

					$log = new Eventlog_Model();
					$log->user_id = $user->id;
					$log->target_id = $poster_id;
					$log->user2_id = $poster_id;
					$log->type = Eventlog_Model::SubUser;
					$log->Save();
                }
                return;
            }
		//}
    }
        
    public function del($board_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn) //'admin'
            {
                $board_id = intval($board_id);
				$board = new Board_Model();
				$board->Load($board_id);
				if($board_id == 0)
					return;
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $this->db->query('DELETE FROM subscriptions WHERE board_id = ? AND user_id = ?', array($board_id,$user->id));

				$log = new Eventlog_Model();
				$log->user_id = $user->id;
				$log->target_id = $board_id;
				if($board->type != 'g')
					$log->user2_id = $board->owner_id;
				$log->type = Eventlog_Model::UnsubBoard;
				$log->Save();
                return;
            }
		}
    }

	public function delSub()
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn) //'admin'
            {
				$post = $this->input->post();
				$post = Validation::factory($post)
					->pre_filter('trim');
				if ($post->validate()){
					$board_id = intval($post['boardid']);
					$user_id = intval($post['userid']);
					$this->db = Database::instance();
					$user = Session::instance()->get('auth_user');
					$board = new Board_Model();
					$board->Load($board_id);
					if($board->owner_id == $user->id  || $this->isAdmin)
					{
						$this->db->query('DELETE FROM subscriptions WHERE board_id = ? AND user_id = ?', array($board_id,$user_id));
					}
				}
                return;
            }
		}
    }

    public function addExcludeBoard($board_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
                $board_id = intval($board_id);
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT board_id from exclude_boards where board_id = ? AND user_id = ?', array($board_id, $user->id));
                if($result->count() == 0)
                {
                    $this->db->query('INSERT INTO exclude_boards (board_id,user_id) values (?,?)', array($board_id,$user->id));
                }
                return;
            }
		}
    }
    public function delExcludeBoard($board_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
                $board_id = intval($board_id);
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $this->db->query('DELETE FROM exclude_boards WHERE board_id = ? AND user_id = ?', array($board_id,$user->id));
                return;
            }
		}
    }

        public function addExcludeUser($poster_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
                $poster_id = intval($poster_id);
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT poster_id from exclude_users where poster_id = ? AND user_id = ?', array($poster_id, $user->id));
                if($result->count() == 0)
                {
                    $this->db->query('INSERT INTO exclude_users (poster_id,user_id) values (?,?)', array($poster_id,$user->id));
                }
                return;
            }
		}
    }
    public function delExcludeUser($poster_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
                $poster_id = intval($poster_id);
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $this->db->query('DELETE FROM exclude_users WHERE poster_id = ? AND user_id = ?', array($poster_id,$user->id));
                return;
            }
		}
    }

	public function addExcludeTopic()
    {
		if ($this->isAjax) {
            if ($this->isAdmin)
            {
				$post = $this->input->post();
				$post = Validation::factory($post)
					->pre_filter('trim');
				if ($post->validate()){
					$topicId = intval($post['topicId']);
					$topic = new Topic_Model();
					$topic->Load($topicId);
					$topic->hidePublic = true;
					$topic->Save();
				}
				return;
            }
		}
    }

    public function addBlock($board_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
                $board_id = intval($board_id);
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $result = $this->db->query('SELECT board_id from blocks where board_id = ? AND user_id = ?', array($board_id, $user->id));
                if($result->count() == 0)
                {
                    $this->db->query('INSERT INTO exclusions (board_id,user_id) values (?,?)', array($board_id,$user->id));
                }
                return;
            }
		}
    }
    public function delBlock($board_id)
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
                $board_id = intval($board_id);
                $this->db = Database::instance();
                $user = Session::instance()->get('auth_user');
                $this->db->query('DELETE FROM blocks WHERE board_id = ? AND user_id = ?', array($board_id,$user->id));
                return;
            }
		}
    }

	public function allow()
	{
		$boardid = 0;
		$userid = 0;
		$post = $this->input->post();
		if(isset($post['boardid']))
        {
            $boardid = intval($post['boardid']);
        }
		if(isset($post['userid']))
        {
            $userid = intval($post['userid']);
        }

		//check for valid conditions
		if(($boardid == 0 || $userid == 0) || !$this->isAjax || !$this->isLoggedIn)
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
			$this->db = Database::instance();
			$this->db->query('UPDATE subscriptions SET pending = 0 WHERE board_id = ? AND user_id = ?', array($boardid,$userid));
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