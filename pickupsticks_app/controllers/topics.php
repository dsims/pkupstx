<?php
class Topics_Controller extends Site_Controller {

	//public $template = 'template';

	function __construct() {
		parent::__construct();
		$this->template->title = 'Posts';
	}

	//default topic listing
	function index() {
		$topic = new Topic_Model;
		$topic->isAdmin = ($this->isAdmin) ? 1 : 0;
        $this->session = Session::instance();

        $userid = 0;
		$censor = 0;
        if ($this->isLoggedIn)
        {
            $user = $this->session->get('auth_user');
            $userid = $user->id;
			$censor = $user->censor;
        }

        $boardid = 0;
		$userpostsid=0;
        $listid = 0;
        $all = 0;
		$archive = 0;
		$isBanned = false;
		$isAllowed = false;

        $get = $this->input->get();

		if(isset($get['archive']))
		{
			$archive = intval($get['archive']);
		}
		if(isset($get['boardid']))
		{
			$boardid = intval($get['boardid']);
		}
		if(isset($get['userpostsid']))
		{
			$userpostsid = intval($get['userpostsid']);
		}
        if(isset($get['listid']))
        {
            $listid = intval($get['listid']);
        }
        if(isset($get['all']))
        {
            $all = intval($get['all']);
        }
        //if($all == 1)
            //$userid = 0;

        $type = '';
        $where2 = '';
		if($this->isLoggedIn && !isset($get['type']) && $boardid == 0 && $listid == 0 && $userpostsid == 0)
		{
			//on home screen
			if($user->hometype == '')
				$type = 'user';
			elseif(intval($user->hometype) > 0)
			{
				$listid = intval($user->hometype);
			}
			else
				$type = $user->hometype;
		}

		if($listid > 0)
        {
            //get boards
            $board = new Board_Model;
            $boards = $board->FindByList($listid);
        }

		//default visitors to see just user posts (transitioning gaming to digi.vg)
		if($type == '' && !isset($get['type']))
		{
			$type = 'user';			
		}
        if($type != '' || (isset($get['type']) && strlen($get['type'])))
        {
			if($type == '')
				$type = $get['type'];
			if($type == "dm" && $this->isLoggedIn)
			{
				$all = 2;
				$where2 = "t.type = 'u' AND ((r.id <> ".intval($user->board_id)." AND t.user_id = ".$user->id.") OR (r.id = ".intval($user->board_id)." AND t.user_id <> ".$user->id.'))';
				if($user->newmsg == 1) //update users message count
					$result = $this->db->query('UPDATE users SET newmsg = 0 WHERE id = ?', array($user->id));
			}
			else if($type == 'user')
			{
				$all = 4;
			}
			else if($type == 'all')
			{
				$all = 1;
			}
			else if($type != 's')
				$where2 = "t.type = '$type'";
        }
		else
			$all = 1; //show everything to general public
        if($listid > 0)
        {
            if(strlen($where2) > 0)
                $where2 .= ' AND ';
            $where2 .= ' t.board_id IN (0';
            foreach($boards as $b)
            {
                $where2 .= ','.$b->id;
            }
            $where2 .= ')';
        }
		if($this->subdomain != 'digibutter' && sizeof($boards) > 0)
		{
			if(strlen($where2) > 0)
                $where2 .= ' AND ';
            $where2 .= ' t.board_id IN (0';
            foreach($boardids as $b)
            {
                $where2 .= ','.$b;
            }
            $where2 .= ')';
		}
		if($userpostsid > 0)
		{
			if(strlen($where2) > 0)
                $where2 .= ' AND ';
            $where2 .= ' t.user_id = '.$userpostsid;
		}
        
        if ($this->isAjax) {
            $last = 0;
            $interval = 0;
            $ids = array();
            if(isset($get['ids']))
            {
                $ids = explode(',',$get['ids']);
                array_walk($ids, 'intval');
            }
            if(isset($get['last']))
                $last = intval($get['last']);

			if($boardid == 0 && !isset($user))
			{
				if(strlen($where2) > 0)
					$where2 .= ' AND ';
				$where2 .= ' t.hidePublic = 0 ';
				$where2 .= ($type != 'gaming') ? ' AND r.hidden = 0 ' : ''; //gaming view is really slow if conditinal on board columns
			}

            if(count($ids) > 0 && $ids[0] > 0)
            {
                if(strlen($where2) > 0)
                    $where2 .= ' AND ';
                $where2 .= 't.date_submit > '.$last;
                $topics = $topic->FindFinal(0,$boardid,$userid,$all,10,'t.id','NOT IN',$ids, $where2, ($type == 'gaming'));
            }
            else
                $topics = $topic->FindFinal(0,$boardid,$userid,$all,10,'t.date_submit','>',$last, $where2, ($type == 'gaming'));
            if(isset($get['interval']))
                $interval = intval($get['interval']);
            $interval = ($interval == 0) ? 10000 : $interval;
            if(sizeof($topics) > 0)
                $interval = 10000;
            else
			{
				if($interval < 60000)
                $interval = $interval*1.5;
			}

            $output = array();
            foreach($topics as $topic)
            {
                $topic->format($censor);
                $output[] = $topic;
            }
            echo $this->ajaxData($output, $interval);
            return;
        }//end ajax

		$view->style = 'new';
		if($this->isMobile)
			$view = new View('topics/list_m');


		if($archive > 0)
		{
			$view->oldtimeformat = $topic->formatTime($archive);
			if(strlen($where2) > 0)
				$where2 .= ' AND ';
			$where2 .= ' t.date_submit < '.$archive.' ';
		}

        $board = new Board_Model;
        if($boardid > 0)
        {
            $board->Load($boardid);
			$all = 3;
        }
		if(($boardid > 0 && $board->type == 'u') || $userpostsid > 0) //get user profile info
		{
			$boarduser = ORM::factory('user')->find($boardid > 0 ? $board->owner_id : $userpostsid);
			$view->boarduser = $boarduser;

			$this->db = Database::instance();
			$result = $this->db->query('SELECT COUNT(*) as count FROM topics WHERE user_id = '.$boarduser->id);
			$view->posts = $result[0]->count;
			$result = $this->db->query('SELECT COUNT(*) as count FROM comments WHERE user_id = '.$boarduser->id);
			$view->replies = $result[0]->count;
			$result = $this->db->query('SELECT COUNT(*) as count, type FROM awards_users WHERE user_id = ? GROUP BY type ORDER BY type',$boarduser->id);
			$view->awardcounts = $result;
		}
        $view->board = $board;
		$view->userpostsid = $userpostsid;

        $list = new Boardlist_Model;
        if($listid > 0)
        {
            $list->Load($listid);
        }
        $view->list = $list;

        if($board->id > 0 && isset($user))
        {
            $this->db = Database::instance();
            $user = Session::instance()->get('auth_user');
            $result = $this->db->query('SELECT board_id from subscriptions where board_id = ? AND user_id = ?', array($board->id, $user->id));
            if($result->count() == 0)
                $view->hasboard = false;
            else
                $view->hasboard = true;
            $result = $this->db->query('SELECT board_id from exclude_boards where board_id = ? AND user_id = ?', array($board->id, $user->id));
            if($result->count() == 0)
                $view->boardexcluded = false;
            else
                $view->boardexcluded = true;
            if($board->type == 'u')
            {
                $result = $this->db->query('SELECT poster_id from exclude_users where poster_id = ? AND user_id = ?', array($board->owner_id, $user->id));
                if($result->count() == 0)
                    $view->userexcluded = false;
                else
                    $view->userexcluded = true;
            }

			$result = $this->db->query('SELECT user_id from boards_banned where board_id = ? and user_id = ?', array($board->id, $user->id));
			if($result->count() > 0)
				$isBanned = true;

			if($board->privacy == 2)
			{
				$result = $this->db->query('SELECT user_id from subscriptions where board_id = ? and user_id = ? AND pending = 0', array($board->id, $user->id));
				$isAllowed = ($result->count() > 0);
			}

        }
        else
        {
            $view->hasboard = true;
            $view->isexcluded = false;

			if(isset($user))
			{
				//get list of boards for dd
				$ddboard = new Board_Model();
				if($type == 'dm')
					$ddboards = $ddboard->FindFinal(0, 0, 'type', '=', 'u', "(bu.last_login > ".(time()-2592000)." AND bu.title <> 'n00b' AND bu.title <> '')", 'title ASC, id ASC');//$ddboards = $ddboard->FindSubscribedByUser($user->id, 'u');
				else
					$ddboards = $ddboard->FindSubscribedByUser($user->id, '', 'u');
				$view->ddboards = $ddboards;
			}
			else
			{
				if(strlen($where2) > 0)
					$where2 .= ' AND ';
				$where2 .= ' t.hidePublic = 0 ';
				$where2 .= ($type != 'gaming') ? ' AND r.hidden = 0 ' : '';
			}
        }
        //$id=0, $boardid=0, $userid=0, $count=0, $property='', $comparison='', $value='', $where2=''
        $topics = $topic->FindFinal(0,$board->id,$userid,$all,30, '', '', '', $where2, ($type == 'gaming'));
        $output = array();
        foreach($topics as $topic)
        {
            $topic->format($censor);
            $output[] = $topic;
        }

        if(isset($user))
            $view->user = $user;

        $typename = '';
        if($type == 'u')
            $typename = 'Friends';
		else if($type == 'user')
            $typename = 'Friends';
        else if($type == 'g')
            $typename = 'Products';
		else if($type == 'gaming')
            $typename = 'Products';
		else if($type == 'dm')
            $typename = 'Direct Messages';
		else if($type == 's')
            $typename = 'Subscriptions';
        else if($type == 'f')
            $typename = 'Friends';
		else if($this->subdomain != 'digibutter' and $this->subdomain != 'digi')
			$typename = ucfirst($this->subdomain);
		$this->template->title = $typename.' Posts';
		if($typename == '' && $board->title != '')
		{
			$typename = $board->title;
			$this->template->title = $typename.' Posts';
		}

		$tabs = array(); $selectedTab = 0;
		if($board->type == 'u' || $userpostsid > 0)
		{
			$tabs['Posts'] = 'topics/user/'.$boarduser->id.'/'.slug::format($boarduser->title);;
			if($boarduser->board_id > 0){
				//$tabs['Board'] = 'topics/board/'.$boarduser->board_id.'/'.slug::format($boarduser->title);
				$tabs['Activity'] = 'activity/'.$boarduser->id.'/'.slug::format($boarduser->title);
				$tabs['Profile'] = 'profile/'.$boarduser->username;
			}
			if($userpostsid > 0)
				$this->template->title = $boarduser->title.' Posts';
			$selectedTab = $userpostsid > 0 ? 1 : 2;
		}
		else if(false && $boardid == 0 && $all > 0 && $this->subdomain == 'digibutter')
		{
			$tabs['User Posts'] = 'topics/?type=user';
			$tabs['Gaming'] = 'topics/?type=gaming';
			$tabs['Everything'] = 'topics/?type=all';
			if($this->isLoggedIn)
				$tabs['Lists'] = 'lists';
			$selectedTab = ($type == 'all' || $type == '') ? 3 : (($type == 'user') ? 1 : (($listid > 0) ? 4 : (($type == 'gaming') ? 2 : 0)));
		}
		$view->tabs = $tabs;
		$view->selectedTab = $selectedTab;

		$view->isArchive = ($archive > 0);
		$view->isBanned = $isBanned;
		$view->isAllowed = $isAllowed;
        $view->all = $all;
        $view->type = $type;
        $view->typename = $typename;
        $view->topics = $output;//$topic->Find(0,20);
		$this->template->content = $view;
		if($archive > 0)
			$this->template->title .= ' Archive';
	}

	function board($id)
	{
		if(intval($id) > 0)
		{
		$_GET['boardid'] = intval($id);
		return $this->index();
		}
		else
			url::redirect('boards/browse');
	}
	function user($id)
	{
		$_GET['userpostsid'] = intval($id);
		return $this->index();
	}

	function getsingle() {
		$topic = new Topic_Model;
        $this->session = Session::instance();

        $userid = 0;
		$censor = 0;
        if ($this->isLoggedIn)
        {
            $user = $this->session->get('auth_user');
            $userid = $user->id;
			$censor = $user->censor;
        }

        $get = $this->input->get();

		if(isset($get['topicId']))
		{
			$topicid = intval($get['topicId']);
		}
        $type = '';
        $where2 = '';
        if ($this->isAjax) {
			$topics = $topic->FindSingle($topicid);

            $output = array();
            foreach($topics as $topic)
            {
                $topic->format($censor);
                $output[] = $topic;
            }
            echo $this->ajaxData($output, 0);
            return;
        }//end ajax
		return;
	}

    function refresh() {
		$topic = new Topic_Model;
        $this->session = Session::instance();
		if ($this->isAjax) {
            $get = $this->input->get();
            $last = intval($get['last']);
            $boardid = 0;
            if(isset($get['boardid']))
                $boardid = intval($get['boardid']);
            $topicsToRefresh = $this->session->get('topicsToRefresh');
            $topics = array();
            if(is_array($topicsToRefresh) && sizeof($topicsToRefresh) > 0)
            {
                $topics = $topic->FindSince($last,$boardid,10,'t.id','IN',$topicsToRefresh);
            }
            echo $this->ajaxData($topics, 10000);
			return;
		}
        else
        {
            return;
        }
	}
	
	//view specific topic
	function view($topicId) {

		$view = new View('topics/view');
		if($this->isMobile)
			$view = new View('topics/view_m');
		$censor = 0;

        if ($this->isLoggedIn)
        {
            $user = Session::instance()->get('auth_user');
            $view->user = $user;
			$censor = $user->censor;
        }
        
		//get topic
		$topic = new Topic_Model;
		$topic->Load($topicId);
		if($topic->id == $topicId)
		{
			//check security
			$board = new Board_Model;
			$board->Load($topic->board_id);
			if($topic->isPrivate || $board->privacy == 2)
			{
				$isAllowed = false;
				if($this->isLoggedIn)
				{
					if($board->owner_id == $user->id)
						$isAllowed = 1;
					else if($topic->user_id == $user->id && $topic->isPrivate)
						$isAllowed = 1;
					else
					{
						$result = $this->db->query('SELECT user_id from subscriptions where board_id = ? and user_id = ? AND pending = 0', array($board->id, $user->id));
						$isAllowed = ($result->count() > 0);
					}
				}
				if(!$isAllowed)
					url::redirect('topics/');
			}

			$relatedTopics = $topic->FindRelated();
	
            $topic->formatView($censor);
			$this->template->title = ($board->type == 'u') ? $topic->title : $topic->title. ' - '.$board->title;
			$view->topic = $topic;
			$view->board = $board;
			$view->relatedTopics = $relatedTopics;

            $comment = new Comment_Model;
            $comments = $comment->FindByTopic($topicId);

            $output = array();
            foreach($comments as $comment)
            {
                $comment->format($censor);
                $output[] = $comment;
            }
            $view->comments = $output;
		}
		else
		{
			$view = new View('topics/notfound');
		}
		$this->template->content = $view;
	}
	
	public function edit($topicId)
	{
		$topicId = intval($topicId);
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');
			if($this->isAjax)
			{
				if($topicId == 0)
				{
					$post = $this->input->post();
					$post = Validation::factory($post)
						->pre_filter('trim');
					if ($post->validate()){
						$topicId = intval($post['topicId']);
						$title = $post['title'];

						if(isset($post['body']))
							$bodyRaw = $post['body'];
						if(isset($post['ckBody']))
							$bodyRaw = $post['ckBody'];

						$purifier = new HTMLPurifier();
						$body = $purifier->purify($bodyRaw);

						if($topicId > 0)
						{
							$topic = new Topic_Model();
							$topic->Load($topicId);

							if($topic->user_id == $user->id || $this->isAdmin)
							{
								$topic->title = $title;
								$topic->body = $body;
								$topic->bodyRaw = $bodyRaw;
								$topic->Save();

								echo json_encode(array('returnCode'=>1,'id'=>$topic->id));
								return;
							}
						}
					}
					echo json_encode(array('returnCode'=>0));
					return;
				}
				else if($topicId > 0)
				{
					$topic = new Topic_Model();
					$topic->Load($topicId);

					echo '<form id="aForm">';
					echo '<textarea name="title" maxlength="255" onkeypress="this.value = this.value.substring(0, 255);return true;" style="width:680px;height:32px">'.$topic->title.'</textarea>';
					echo '<br />';
					echo '<div id="tempEditorArea"></div>';
					echo '<script type="text/javascript">createEditor('.json_encode($topic->body).');</script>';
					echo '<input type="hidden" name="topicId" value="'.$topicId.'">';
					echo '</form>';
					return;
				}
			}

			if($topicId > 0)
			{
				$topic = new Topic_Model();
				$topic->Load($topicId);

				if($topic->id != $topicId || ($topic->user_id != $user->id && !$this->isAdmin))
					url::redirect('topics/');

				$post = $this->input->post();

				$post = Validation::factory($post)
					->pre_filter('trim');

				$board = new Board_Model();
				if ($post->validate())
				{
					$dopost = true;
					if(isset($post['boardid']))
						$board_id = intval($post['boardid']);
					if(isset($post['boardid']) && $board_id != $topic->board_id && !$topic->isPrivate)
					{
						$board->Load($board_id);
						if($board->id == $board_id)
						{
							if($board->privacy == 1) //only owner can post
							{
								if($board->owner_id != $user->id)
								{
									$dopost = false;
								}
							}
							else if($board->privacy == 2) //only owner or approved subscribers can post
							{
								if($board->owner_id != $user->id)
								{
									$result = $this->db->query('SELECT user_id from subscriptions where board_id = ? and user_id = ? AND pending = 0', array($board_id, $user->id));
									if($result->count() == 0)
									{
										$dopost = false;
									}
								}
							}
							if($dopost)
							$topic->board_id = $board_id;
							$topic->type = $board->type;
						}
					}

					//$postdata_array = $post->safe_array();
					$postdata_array = $post;
					if(isset($postdata_array['title']))
						$topic->title = $postdata_array['title'];
					if(isset($postdata_array['body']))
						$topic->bodyRaw = $postdata_array['body'];
					if(isset($postdata_array['ckBody']))
						$topic->bodyRaw = $postdata_array['ckBody'];

					$purifier = new HTMLPurifier();
					$topic->body = $purifier->purify($topic->bodyRaw);

					$topic->Save();
					$topic->format(); //format to get slug
					url::redirect('topics/'.$topic->id.'/'.$topic->slug);
				}

				$board->Load($topic->board_id);
				$view = new View('topics/edit');
		if($this->isMobile)
			$view = new View('topics/edit_m');
				
				$ddboards = $board->FindSubscribedByUser($user->id, '', 'u');
				$view->ddboards = $ddboards;
				$view->board = $board;
				$view->topic=$topic;
				$view->user=$user;
				$this->template->content = $view;
			}
			else
				url::redirect('topics/');
		}
		else		
		{
			url::redirect('topics/');
		}
	}

	public function review($gameId)
	{
		$gameId = intval($gameId);
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');

			if($gameId > 0)
			{
				$game = new Game_Model();
				$game->Load($gameId);

				$view = new View('topics/review');
				$view->game=$game;
				$view->user=$user;
				$this->template->content = $view;
			}
			else
				url::redirect('topics/');
		}
		else		
		{
			url::redirect('topics/');
		}
	}

	public function startnew($boardid=0)
	{
		$boardid = intval($boardid);
		if($boardid > 0)
		{
			return $this->post($boardid);
		}
		$view = new View('topics/new_1');
		$this->template->content = $view;
	}
	
	public function post($boardid=0)
	{
		$boardid = intval($boardid);
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');

			if($boardid == 0)
				$boardid = $user->board_id;

			if($boardid > 0)
			{
				$board = new Board_Model();
				$board->Load($boardid);

				if($board->id != $boardid)
					url::redirect('topics/');

				$dopost = true;
				if($board->privacy == 1) //only owner can post
				{
					if($board->owner_id != $user->id)
					{
						$dopost = false;
					}
				}
				else if($board->privacy == 2) //only owner or approved subscribers can post
				{
					if($board->owner_id != $user->id)
					{
						$result = $this->db->query('SELECT user_id from subscriptions where board_id = ? and user_id = ? AND pending = 0', array($board_id, $user->id));
						if($result->count() == 0)
						{
							$dopost = false;
						}
					}
				}
				if($dopost)
				{
					$view = new View('topics/new_m');
					$view->board = $board;
					$view->user=$user;
					$this->template->content = $view;
				}
				else
					url::redirect('topics/');
			}
			else
				url::redirect('topics/');
		}
		else
		{
			url::redirect('topics/');
		}
	}

	
	public function xzingscan()
	{
		$get = $this->input->get();
		if(isset($get['code']))
			return $this->scan($get['code']);
		else 
			return $this->scan();
	}
	public function scan($code='')
	{
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');

			if(strlen($code) > 0)
			{
				$game = new Game_Model();
				$game->FindByCode($code);

				if($game->code != $code)
				{
					//didnt find it, so add it
					url::redirect('products/create/'.$code);
				}
				$board_id = $game->board_id;
				$board = new Board_Model();
				$board->Load($board_id);

				$dopost = true;
				if($board->privacy == 1) //only owner can post
				{
					if($board->owner_id != $user->id)
					{
						$dopost = false;
					}
				}
				else if($board->privacy == 2) //only owner or approved subscribers can post
				{
					if($board->owner_id != $user->id)
					{
						$result = $this->db->query('SELECT user_id from subscriptions where board_id = ? and user_id = ? AND pending = 0', array($board_id, $user->id));
						if($result->count() == 0)
						{
							$dopost = false;
						}
					}
				}
				if($dopost)
				{
					$view = new View('topics/new_m');
					$view->board = $board;
					$view->user=$user;
					$this->template->content = $view;
				}
				else
					url::redirect('topics/');
			}
			else
				url::redirect('topics/');
		}
		else
		{
			url::redirect('topics/');
		}
	}
		
	
	public function submit()
	{		
		if ($this->isLoggedIn)
		{
            $user = Session::instance()->get('auth_user');
			$topic = new Topic_Model();
			
			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');
			
			if ($post->validate())
			{
                $urls = array();
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
				if(isset($postdata_array['title']))
					$topic->title = $postdata_array['title'];
                if(isset($postdata_array['urls']))
                    $urls = $postdata_array['urls'];
				if(preg_match_all(
					'/(^|\s)((https?):\/\/[^<> \n\r]+)/i', $topic->title, $matches))
				{
					foreach($matches[0] as $match) {
						array_push($urls, $match);
					}
				}
                //echo Kohana::debug($postdata_array['urls']);
				if(isset($postdata_array['body']))
					$topic->bodyRaw = $postdata_array['body'];
				if(isset($postdata_array['ckBody']))
					$topic->bodyRaw = $postdata_array['ckBody'];

				$purifier = new HTMLPurifier();
				$topic->body = $purifier->purify($topic->bodyRaw);

				$isPrivate = 0;
				if(isset($postdata_array['isPrivate']))
					$isPrivate = intval($postdata_array['isPrivate']);
                $topic->user_id = $user->id;
                $board_id = 0;
				if(isset($postdata_array['boardiddefault']))
                {
					$board_id = $postdata_array['boardiddefault'];
                }
                if(isset($postdata_array['boardid']) && $postdata_array['boardid'] > 0)
                {
					$board_id = $postdata_array['boardid'];
                }
				if(isset($postdata_array['boardidselect']))
                {
					//$board_id = $postdata_array['boardidselect'];
                }

		if(isset($postdata_array['rating']) && isset($postdata_array['rating_game_id']))
                {
			$topic->rating = $postdata_array['rating'];
			$topic->rating_game_id = $postdata_array['rating_game_id'];
		}		
		
                if($board_id == 0)
				{
					if($this->isAjax)
						echo json_encode(array('returnCode'=>0));
					return;
                    //$board_id = $user->board_id;
				}

				//check privacy settings
				$board = new Board_Model();
				$board->Load($board_id);

				if($board->type != 'u') //only private posts allowed to users
					$isPrivate = 0;
					
				if(!$isPrivate) //anyone can post a PM
				{
					if($board->privacy == 1) //only owner can post
					{
						if($board->owner_id != $user->id)
						{
							if($board->type == 'u') //default to private
								$isPrivate = 1;
							else
							{
								if($this->isAjax)
									echo json_encode(array('returnCode'=>0));
								return;
							}
						}
					}
					if($board->privacy == 2) //only owner or approved subscribers can post
					{
						if($board->owner_id != $user->id)
						{
							$result = $this->db->query('SELECT user_id from subscriptions where board_id = ? and user_id = ? AND pending = 0', array($board_id, $user->id));
							if($result->count() == 0)
							{
								if($board->type == 'u') //default to private
									$isPrivate = 1;
								else
								{
									if($this->isAjax)
										echo json_encode(array('returnCode'=>0));
									return;
								}
							}
						}
					}
				}
				$topic->isPrivate = $isPrivate;
                $topic->board_id = $board_id;
				$topic->type = $board->type;

				$isBanned = false;
				$result = $this->db->query('SELECT user_id from boards_banned where board_id = ? and user_id = ?', array($board_id, $user->id));
				if($result->count() > 0)
					$isBanned = true;

				if(!$isBanned && (time() - $user->last_post) > 10) //flood limit
				{
					$topic->Save();
					$topic->AddURLs($urls);
					if((time() - $user->last_post) < 30)
					{
						if($board->owner_id == $user->id)
						{
							$result = $this->db->query('SELECT * from topics where board_id = ? and user_id = ? AND date_added > ?', array($board_id, $user->id, time()-300)); //5 minutes
							if($result->count() > 5)//5 posts by owner in last 5 minutes
							{
								$board->privacy = 3;
								$board->Save();
							}
						}
						$user->last_post = time()+30;
					}
					else
						$user->last_post = time();

					$user->save();
					if($board->type == 'u' && $board->owner_id != $user->id) //update users message count
						$result = $this->db->query('UPDATE users SET newmsg = 1 WHERE id = ?', array($board->owner_id));
				}
				else if($this->isAjax)
				{
					$user->last_post = time()+30; //make them wait a little longer
					$user->save();
					echo json_encode(array('returnCode'=>5));
					return;
				}
			}
            else if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>6));
                return;
            }

            //return success
            if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>1,'id'=>$topic->id));
                return;
            }

			url::redirect('topics/'.$topic->id);
		}
		else		
		{
			echo json_encode(array('returnCode'=>7));
			return;
		}
	}

	public function delete($topicId)
	{
		if(!$this->isAjax && $topicId > 0)
		{
			$topic = new Topic_Model();
			$topic->Load($topicId);
			$view = new View('topics/delete');
			$view->topic=$topic;
			$this->template->content = $view;
		}
		else
		{
			if($topicId == 0)
			{
				$post = $this->input->post();
				$post = Validation::factory($post)
					->pre_filter('trim');
				if ($post->validate()){
					$topicId = intval($post['topicId']);
				}
			}

			//check for valid conditions
			if($topicId == 0 || !$this->isLoggedIn)
			{
				echo json_encode(array('returnCode'=>0));
				return;
			}

			$user = Session::instance()->get('auth_user');

			$topic = new Topic_Model;
			$topic->Load($topicId);
			$board = new Board_Model;
			$board->Load($topic->board_id);

			//check if allowed to delete
			if($user->id == $topic->user_id || $user->id == $board->owner_id || $this->isAdmin)
			{
				$topic->Delete();
			}
			else
			{
				echo json_encode(array('returnCode'=>0));
				return;
			}

			//return success

			if($this->isAjax)
			{
				echo json_encode(array('returnCode'=>1));
				return;
			}
			else
			{
				url::redirect('topics/');
			}
		}
	}

		public function lock($topicId)
	{
		//check for valid conditions
		if($topicId == 0 || !$this->isAjax || !$this->isLoggedIn)
		{
			echo json_encode(array('returnCode'=>0));
			return;
		}

		$user = Session::instance()->get('auth_user');

		$topic = new Topic_Model;
		$topic->Load($topicId);
		$board = new Board_Model;
		$board->Load($topic->board_id);

		//check if allowed to delete
		if($user->id == $topic->user_id || $user->id == $board->owner_id || $this->isAdmin)
		{
			if($topic->locked == 0)
				$topic->locked = 1;
			else
				$topic->locked = 0;
			$topic->Save();
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

	public function addLike()
    {
		if ($this->isAjax) {
            if ($this->isLoggedIn)
            {
				$user = Session::instance()->get('auth_user');
				$post = $this->input->post();
				$post = Validation::factory($post)
					->pre_filter('trim');
				if ($post->validate()){
					$topicId = intval($post['topicId']);
					$type = intval($post['type']);
					$topic = new Topic_Model();
					$topic->Load($topicId);
					if($topic->user_id != $user->id)
						$topic->AddLike($type, $user->id);
				}
                return;
            }
		}
    }
	
	public function ajax($action)
	{
		$this->auto_render = FALSE;
		switch($action)
		{
			case 'list':
				$topic = new Topic_Model;
				$this->ajaxReply(201, 'data', $topic->Find(0));
			break	;
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
