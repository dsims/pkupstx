<?php
class Lists_Controller extends Site_Controller {

	public $template = 'template';

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'boards';
	}

	function index() {
        url::redirect('lists/boards');
	}

    function boards() {
        $view = new View('lists/boards');

        $lists = array();
        $view->loggedin = false;
        if ($this->isLoggedIn) //'admin'
		{
            $view->loggedin = true;

            $user = Session::instance()->get('auth_user');

            $list = new Boardlist_Model();
            $lists = $list->FindWhere('user_id', $user->id, 0);
        }
		else
			url::redirect('');
		$view->lists = $lists;
		$this->template->content = $view;
		$this->template->title = 'Edit Lists';
    }

    public function addlist()
	{
		if ($this->isLoggedIn) //'admin'
		{
            $user = Session::instance()->get('auth_user');
			$list = new Boardlist_Model();

			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');

			if ($post->validate() && isset($post['title']))
			{
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
                $list->title = $postdata_array['title'];
                $list->user_id = $user->id;
				$list->Save();
			}
            else if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>0));
                return;
            }
            else
            {
                echo Kohana::debug($post);
                return;
            }

            //return success
            if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>1,'id'=>$list->id));
                return;
            }

            url::redirect('lists/boards');
		}
		else
		{
            if(!$this->isAjax)
            {
                url::redirect('');
            }
		}
	}

	function delete()
    {
        if ($this->isLoggedIn)
		{
            $post = $this->input->post();
            $listid = intval($post['listid']);
            if($listid > 0)
            {
                $list = new Boardlist_Model();
                $list->id = $listid;
                $list->Delete();
            }
            url::redirect('lists/boards');
        }
        url::redirect('');
    }

    public function addboard()
	{
		if ($this->isLoggedIn) //'admin'
		{
            $user = Session::instance()->get('auth_user');
			$list = new Boardlist_Model();


			$post = $this->input->post();

            $listid = intval($post['listid']);
            $boardid = intval($post['boardid']);

            $list->Load($listid);

            if($list->id > 0)
            {
                $list->Addboard($boardid);
            }
            else if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>0));
                return;
            }
            //return success
            if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>1,'id'=>$list->id));
                return;
            }

            url::redirect('lists/boards');
		}
		else
		{
            if(!$this->isAjax)
            {
                url::redirect('');
            }
		}
	}
    function deleteboard()
    {
        if ($this->isLoggedIn) //'admin'
		{
            $post = $this->input->post();
            $listid = intval($post['listid']);
            $boardid = intval($post['boardid']);
            if($listid > 0 && $boardid > 0)
            {
                $list = new Boardlist_Model();
                $list->id = $listid;
                $list->Deleteboard($boardid);
            }
            url::redirect('lists/boards');
        }
        url::redirect('');
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