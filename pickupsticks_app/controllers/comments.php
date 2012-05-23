<?php
class Comments_Controller extends Site_Controller {

	//public $template = 'template';
	//public $auto_render = FALSE;

	function __construct() {
		parent::__construct();
		$this->template->title = 'Reply';
	}
	
	//AJAX call
	public function submit()
	{
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');	

			$post = $this->input->post();
			
			$topicid = intval($post['topicid']);

            $text = trim($post['value']);
			if(isset($post['ckBody']))
				$text = $post['ckBody'];

            if ($topicid > 0 && $user->id > 0)
            {
                $comment = new Comment_Model();
                $comment->topic_id = $topicid;
                $comment->user_id = $user->id;
                $comment->commentRaw = $text;

				$purifier = new HTMLPurifier();
				$comment->comment = $purifier->purify($comment->commentRaw);

				$topic = new Topic_Model;
				$topic->Load($topicid);
				$isBanned = false;
				$result = $this->db->query('SELECT user_id from boards_banned where board_id = ? and user_id = ?', array($topic->board_id, $user->id));
				if($result->count() > 0)
					$isBanned = true;

				if(!$topic->locked && !$isBanned && (time() - $user->last_post) > 2) //flood limit
				{
					$result = $this->db->query('SELECT * from comments where topic_id = ? and user_id = ? AND date_added > ?', array($topic->id, $user->id, time()-300)); //5 minutes
					if($result->count() > 5)//5 posts in last 5 minutes
					{
						$topic->locked = true; //user flooding locks topic
						$topic->Save();
					}
					$diff = time() - $user->last_post;
					if($diff < 30)
					{
						$user->last_post = time()+30-$diff;
					}
					else
						$user->last_post = time();
					$comment->Save();
					$user->save();
				}
				else if($this->isAjax)
				{
					echo json_encode(array('returnCode'=>5));
					return;
				}
            }
			else if($this->isAjax)
			{
				echo json_encode(array('returnCode'=>6));
					return;
			}
            $_SESSION['cinterval'] = 10000;
            //return success
            echo json_encode(array('returnCode'=>1));
		}
		else		
		{
			//Log::add('error', 'atttempt to comment not logged in');
			//return fail
			echo json_encode(array('returnCode'=>7));
		}
	}

	public function edit($commentId)
	{
		$commentId = intval($commentId);

		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');
			if($commentId == 0)
			{
				$post = $this->input->post();
				$post = Validation::factory($post)
					->pre_filter('trim');
				if ($post->validate()){
					$commentId = intval($post['commentId']);
					$comment = $post['comment'];
					if($commentId > 0)
					{
						$comm = new Comment_Model();
						$comm->Load($commentId);

						if($comm->user_id == $user->id || $this->isAdmin)
						{
							$comm->commentRaw = $comment;
							$purifier = new HTMLPurifier();
							$comm->comment = $purifier->purify($comm->commentRaw);
							$comm->Save();
							url::redirect('topics/'.$comm->topic_id);
						}
					}
				}
				url::redirect('topics/');
			}
			else
			{
				$comm = new Comment_Model();
				$comm->Load($commentId);
				if($comm->user_id == $user->id || $this->isAdmin)
				{
					$topic = new Topic_Model;
					$topic->Load($comm->topic_id);
					
					$view = new View('topics/comment_edit');
					$view->comment=$comm;
					$view->topic = $topic;
					$this->template->content = $view;
				}
				else
					url::redirect('topics/');
			}
		}
		else
		{
			url::redirect('topics/');
		}
 	}

	public function editsubmit()
	{
		if ($this->isLoggedIn)
		{
			$user = Session::instance()->get('auth_user');
			$post = $this->input->post();
			$post = Validation::factory($post)
				->pre_filter('trim');
			if ($post->validate()){
				$commentId = intval($post['commentId']);
				$comment = $post['comment'];
				if($commentId > 0)
				{
					$comm = new Comment_Model();
					$comm->Load($commentId);

					if($comm->user_id == $user->id || $this->isAdmin)
					{
						$comm->commentRaw = $comment;
						$purifier = new HTMLPurifier();
						$comm->comment = $purifier->purify($comm->commentRaw);
						$comm->Save();
						url::redirect('topics/'.$comm->topic_id);
					}
				}
			}
			url::redirect('topics/');
		}
		else
		{
			url::redirect('topics/');
		}
 	}

    public function get()
    {
        $last = 0;
        $interval = 0;
        $ids= array();
        $get = $this->input->get();
        if(isset($get['last']))
            $last = intval($get['last']);
        if(isset($get['ids']))
        {
            $ids = explode(',',$get['ids']);
            array_walk($ids, 'intval');
        }
        else if(isset($get['topicid']))
            $ids[] = intval($get['topicid']);
        if(isset($get['interval']))
            $interval = intval($get['interval']);
        $interval = ($interval < 10000) ? 10000 : $interval;

		$censor = 0;
        if ($this->isLoggedIn)
        {
            $user = $this->session->get('auth_user');
			$censor = $user->censor;
        }

		$comment = new Comment_Model;
		if ($this->isAjax && count($ids) > 0 && $ids[0] > 0) {
            $comments = $comment->FindByTopicsSince($ids, $last);
            if(sizeof($comments) > 0)
                $interval = 10000;
            else if($interval < 60000)
                $interval = $interval*1.5;

            $output = array();
            foreach($comments as $comment)
            {
                $comment->format($censor);
                $output[] = $comment;
            }
            echo $this->ajaxData($output,  $interval);
			return;
		}
        else
        {
            $interval = $interval*1.5;
            echo $this->ajaxData(array(),  $interval);
        }
            //echo $this->ajaxReply($replyCode = 500,$replyText = 'error');
    }

	public function delete($commentId)
	{
		//check for valid conditions
		if($commentId == 0 || !$this->isAjax || !$this->isLoggedIn)
		{
			echo json_encode(array('returnCode'=>0));
			return;
		}

		$user = Session::instance()->get('auth_user');

		$comment = new Comment_Model;
		$comment->Load($commentId);
		$candelete = false;
		if($user->id == $comment->user_id)
			$candelete = true;
		else
		{
			$topic = new Topic_Model;
			$topic->Load($comment->topic_id);
			if($user->id == $topic->user_id)
				$candelete = true;
			else
			{
				$board = new Board_Model;
				$board->Load($topic->board_id);
				if($user->id == $board->owner_id)
					$candelete = true;
			}
		}

		//check if allowed to delete
		if($candelete || $this->isAdmin)
		{
			$comment->DeleteAndUpdateTopic();
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