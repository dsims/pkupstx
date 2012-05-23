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
class Games_Controller extends Site_Controller {

	function __construct() {
		parent::__construct();	
		
		$this->template->title = 'Games';
	}
	
	function index() {
        $game = new Game_Model;
		$view = new View('games/list');
        $games = $game->Find(0, 20);
		$view->games = $games;
		$this->template->content = $view;
	}
		
	public function view($gameID)
	{
        $game = new Game_Model;
        $game->Load($gameID);
		if($game->id == 0)
			url::redirect('boards/browse/games');
        //url::redirect('topics/?boardid='.$game->board_id);

		$topic = new Topic_Model();
		$topics = $topic->FindFinal(0,$game->board_id,0,2,3);
		$output = array();
        foreach($topics as $topic)
        {
            $topic->format();
            $output[] = $topic;
        }
		$user = Session::instance()->get('auth_user');

		$db = Database::instance();

		$q = 'SELECT tag_id FROM boards_tags WHERE board_id = '.$game->board_id;
		$tags = $db->query($q);
		$tagstr = '';
		foreach($tags as $tag)
		{
			if(strlen($tagstr) > 0)
				$tagstr.=', ';
			switch($tag->tag_id)
			{
				case 1: $tagstr .= 'Wii';break;
				case 2: $tagstr .= 'Xbox360';break;
				case 3: $tagstr .= 'PS3';break;
				case 4: $tagstr .= 'PC';break;
				case 5: $tagstr .= 'DS';break;
				case 6: $tagstr .= 'PSP';break;
				case 7: $tagstr .= 'Classic';break;
			}
		}

		$q = 'SELECT DISTINCT u.url from topics_urls u JOIN topics t ON t.board_id = '.$game->board_id.' WHERE u.topic_id = t.id AND (u.url LIKE \'%.jpg\' OR u.url LIKE \'%.png\' OR u.url LIKE \'%.gif\') ORDER BY t.id DESC LIMIT 9';
		$pics = $db->query($q);

		$q = 'SELECT DISTINCT u.url from topics_urls u JOIN topics t ON t.board_id = '.$game->board_id.' WHERE u.topic_id = t.id AND (u.url LIKE \'%youtube.com/watch%\' OR u.url LIKE \'%viddler%\' OR u.url LIKE \'%gametrailers%\' OR u.url LIKE \'%/17-%\' OR u.url LIKE \'%g4tv.com/lv3%\')  ORDER BY t.id DESC  LIMIT 2';
		$vids = $db->query($q);


		$view = new View('games/view');
		$view->uowned = 0;
		$view->uplayed = 0;
		if($this->isLoggedIn)
		{
			$q = 'SELECT owned, played FROM game_statuses WHERE user_id = '.$user->id.' AND game_id = '.$game->id;
			$result = $db->query($q);
			if(sizeof($result) > 0)
			{
				$result = $result->current();
				$view->uowned = $result->owned;
				$view->uplayed = $result->played;
			}
		}

		$result = $db->query('SELECT COUNT(*) as count FROM game_statuses WHERE owned = 1 AND game_id = '.$game->id);
		$view->owned = $result->current()->count;
		$result = $db->query('SELECT COUNT(1) as count FROM game_statuses WHERE owned = 2 AND game_id = '.$game->id);
		$view->want = $result->current()->count;
		$result = $db->query('SELECT COUNT(1) as count FROM game_statuses WHERE played = 1 AND game_id = '.$game->id);
		$view->played = $result->current()->count;
		$result = $db->query('SELECT COUNT(1) as count FROM game_statuses WHERE played = 2 AND game_id = '.$game->id);
		$view->beat = $result->current()->count;
		$result = $db->query('SELECT COUNT(1) as count FROM topics WHERE rating_game_id = '.$game->id);
		$view->reviewed = $result->current()->count;

		$view->topics = $output;
		$view->tagstr = $tagstr;
        $view->game = $game;
		$view->pics = $pics;
		$view->vids = $vids;
		$view->loggedin = $this->isLoggedIn;
		$this->template->title = $game->title . ' game info, screenshots, videos';
		$this->template->content = $view;
	}

	public function userlist($type, $gameID)
	{
        $game = new Game_Model;
        $game->Load($gameID);
		if($game->id == 0)
			url::redirect('boards/browse/games');

		$user = Session::instance()->get('auth_user');

		$db = Database::instance();

		$view = new View('games/userlist');

		$view->uowned = 0;
		$view->uplayed = 0;
		if($this->isLoggedIn)
		{
			$q = 'SELECT owned, played FROM game_statuses WHERE user_id = '.$user->id.' AND game_id = '.$game->id;
			$result = $db->query($q);
			if(sizeof($result) > 0)
			{
				$result = $result->current();
				$view->uowned = $result->owned;
				$view->uplayed = $result->played;
			}
		}
		$result = array();
		switch($type)
		{
			case 'own':
			$result = $db->query('SELECT s.user_id, u.title, u.username, t.id as topic_id, t.rating FROM game_statuses s JOIN users u on u.id=s.user_id LEFT JOIN topics t on t.rating_game_id = s.game_id AND t.user_id = s.user_id WHERE owned = 1 AND game_id = '.$game->id);
			break;
			case 'want':
			$result = $db->query('SELECT s.user_id, u.title, u.username, t.id as topic_id, t.rating FROM game_statuses s JOIN users u on u.id=s.user_id LEFT JOIN topics t on t.rating_game_id = s.game_id AND t.user_id = s.user_id WHERE owned = 2 AND game_id = '.$game->id);
			break;
			case 'played':
			$result = $db->query('SELECT s.user_id, u.title, u.username, t.id as topic_id, t.rating FROM game_statuses s JOIN users u on u.id=s.user_id LEFT JOIN topics t on t.rating_game_id = s.game_id AND t.user_id = s.user_id WHERE played = 1 AND game_id = '.$game->id);
			break;
			case 'beat':
			$result = $db->query('SELECT s.user_id, u.title, u.username, t.id as topic_id, t.rating FROM game_statuses s JOIN users u on u.id=s.user_id LEFT JOIN topics t on t.rating_game_id = s.game_id AND t.user_id = s.user_id WHERE played = 2 AND game_id = '.$game->id);
			break;
		}

		$view->users = $result;
		$view->type = $type;
        $view->game = $game;
		$view->loggedin = $this->isLoggedIn;
		$this->template->title = $game->title.' - '.$type;
		$this->template->content = $view;
	}

public function reviews($gameID)
	{
		$game = new Game_Model;
		$game->Load($gameID);
		if($game->id == 0)
			url::redirect('boards/browse/games');

		$user = Session::instance()->get('auth_user');

		$db = Database::instance();

		$view = new View('games/reviews');

		$topic = new Topic_Model();
		$topics = $topic->FindFinal(0,$game->board_id,0,2,0,'rating','>','0');
		$output = array();
		foreach($topics as $topic)
		{
		    $topic->format();
		    $output[] = $topic;
		}
		$view->reviews = $output;
        	$view->game = $game;
		$view->loggedin = $this->isLoggedIn;
		$this->template->title = $game->title.' Reviews ';
		$this->template->content = $view;
	}

	public function refreshMetaScore($gameID)
	{
        $game = new Game_Model;
        $game->Load($gameID);
		if($game->id == 0)
			url::redirect('boards/browse/games');
		if(strlen($game->mc_url) > 0)
		{
			if(preg_match('#http://www.metacritic.com/games/platforms/([0-9A-Za-z]*)/([0-9A-Za-z]*)#is', $game->mc_url, $matches))
			{
				$mcplatform = $matches[1];
				$mcgame = $matches[2];
			}
		}
		else
		{
			$db = Database::instance();
			$q = 'SELECT tag_id FROM boards_tags WHERE board_id = '.$game->board_id;
			$tags = $db->query($q);
			$tagstr = '';
			foreach($tags as $tag)
			{
				if(strlen($tagstr) > 0)
					break;
				switch($tag->tag_id)
				{
					case 1: $tagstr .= 'wii';break;
					case 2: $tagstr .= 'xbox360';break;
					case 3: $tagstr .= 'ps3';break;
					case 4: $tagstr .= 'pc';break;
					case 5: $tagstr .= 'ds';break;
					case 6: $tagstr .= 'psp';break;
				}
			}
			$mcplatform = $tagstr;
			$title = preg_replace('/^The /', '', $game->title);
			$mcgame = str_replace('-','',url::title($title, '-'));
		}
		if(strlen($mcplatform) == 0)
			url::redirect('games/'.$game->id.'/'.slug::format($game->title));
		$url = 'http://pipes.yahoo.com/pipes/pipe.run?System='.$mcplatform.'&_id=206748885c2c76dfac38f700e22b0564&_render=json&game='.$mcgame;
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url) or die("Invalid cURL Handle Resouce");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Just return the data - not print the whole thing.
        curl_setopt($ch, CURLOPT_HEADER, false); //We need the headers
        //curl_setopt($ch, CURLOPT_NOBODY, !($options['return_body'])); //The content - if true, will not download the contents. There is a ! operation - don't remove it.
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch); //Some information on the fetch
		$response = json_decode($response, true);
        curl_close($ch);  //If the session option is not set, close the session.
		if(isset($response['value']['items'][0]['scorenum']))
		{
			$scorenum = $response['value']['items'][0]['scorenum'];

			$game->mc_score = $scorenum;
			if(strlen($game->mc_url == 0))
				$game->mc_url = 'http://www.metacritic.com/games/platforms/'.$mcplatform.'/'.$mcgame;
			$game->Save();
		}
		url::redirect('games/'.$game->id.'/'.slug::format($game->title));
	}

	public function submitSettings()
	{
		if ($this->isLoggedIn) //'admin'
		{
			$type = 'want';
            $user = Session::instance()->get('auth_user');
			$game = new Game_Model();

			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');

			if ($post->validate())
			{
				$db = Database::instance();
				
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
				$game_id = 0;
				if(isset($postdata_array['game_id']))
					$game_id = $postdata_array['game_id'];

				$game->Load($game_id);
				if($game->id > 0)
				{
					$owned = 0;
					$want = 0;
					$played = 0;
					$beat = 0;
					if(isset($postdata_array['own']))
						$owned = 1;
					if(isset($postdata_array['want']))
						$owned = 2;
					if(isset($postdata_array['played']))
						$played = 1;
					if(isset($postdata_array['beat']))
						$played = 2;
					if(isset($postdata_array['type']))
						$type = $postdata_array['type'];
					$q = 'DELETE FROM game_statuses WHERE user_id = ? AND game_id = ?';
					$db->query($q, $user->id, $game->id);
					$q = 'INSERT INTO game_statuses (user_id, game_id, owned, played) VALUES (?,?,?,?)';
					$db->query($q, $user->id, $game->id, $owned, $played);
				}
			}

            url::redirect('games/'.$type.'/'.$game->id.'/'.slug::format($game->title));
		}
		else
		{
			url::redirect('games/');
		}
	}

     function create($code='') {
     	
     	$view = new View('games/new');
		$game = new Game_Model;
     	$rpc_key = '123';
		if(strlen($code))
		{     	
			$r_time = microtime(true);
     		// Connect to webservice
			$client = new IXR_Client('http://www.upcdatabase.com/xmlrpc');
			$params = array( 
					'rpc_key' => $rpc_key,
					'upc' => $code,
					);
					$server_response = array();
				if (!$client->query('lookup', $params))
				{
					$server_response = array(
						'request_time' => $r_time,
						'response_time' => $r_time,
						'time' => $r_time,
						'data' => ' failed! - ' . $client->getErrorCode() . ' : ' . $client->getErrorMessage(),
						'error-code' => $client->getErrorCode(),
						'error-msg' => $client->getErrorMessage(),
					);
				}
				else
				{
					$server_response = array_merge(
						array(
							'request_time'=>$r_time,
							'response_time'=>microtime(true)
						),
						$client->getResponse()
					);
					//die(print_r($server_response));
				}
				
				// first check if we have XMLRPC error
				if (isset($server_response['status']))
				{					
					// If we get a transport error on a server, there's no point in trying to contact it again
					if ($server_response['status'] == 'fail')
						$game->title = '';
				}
				// else check $test_data against given conditions
				else
				{
					// Extract the value specified from the nested array
					$test_data = $server_response['data'];
					$game->title = $test_data;
				}
		}
     	
        $view->game = $game;
		$this->template->content = $view;
		$this->template->title = 'Add New';
    }
	function edit($id) {
		if($this->isAdmin)
		{
			$view = new View('games/edit');
			$game = new Game_Model;
			$game->Load($id);
			$view->game = $game;
			$this->template->content = $view;
			$this->template->title = 'Edit';
		}
    }

	public function admin()
	{
		if ($this->isAdmin) //'admin'
		{
			$gameid = 0;
			$get = $this->input->get();
			if(isset($get['gameId']))
				$gameid = intval($get['gameId']);

			$game = new Game_Model();
			$games = $game->FindFinal(0, 0, '', '', '', '', 'g.title ASC');
			$view = new View('games/admin');
			$view->games = $games;
			$view->gameid = $gameid;
			$this->template->content = $view;
		}
	}

	public function delete()
	{
		if ($this->isAdmin) //'admin'
		{
			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');
				$game = 0;
			if ($post->validate())
			{
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
				$gameId = intval($post['gameId']);
				$game2 = new Game_Model();
				$game2->Load($gameId);
				if($game2->id == 0)
					die();

				$query = $this->db->query('DELETE FROM boards_tags WHERE board_id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM game_statuses WHERE game_id = ?',$game2->id);
				$query = $this->db->query('DELETE FROM subscriptions WHERE board_id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM exclude_boards WHERE board_id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM boards_banned WHERE board_id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM boardlists_boards WHERE board_id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM boards WHERE id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM comments WHERE topic_id IN (SELECT id from topics WHERE board_id = ?)',$game2->board_id);
				$query = $this->db->query('DELETE FROM topics_urls WHERE topic_id IN (SELECT id from topics WHERE board_id = ?)',$game2->board_id);
				$query = $this->db->query('DELETE FROM topics WHERE board_id = ?',$game2->board_id);
				$query = $this->db->query('DELETE FROM games WHERE id = ?',$game2->id);
			}
			url::redirect('games/admin');
		}
	}

	public function mergedo()
	{
		if ($this->isAdmin) //'admin'
		{
			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');
				$game = 0;
			if ($post->validate())
			{
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;
				$game1Id = intval($post['game1Id']);
				$game2Id = intval($post['game2Id']);

				$game1 = new Game_Model();
				$game2 = new Game_Model();
				$game1->Load($game1Id);
				$game2->Load($game2Id);
				$board1 = new Board_Model();
				$board2 = new Board_Model();
				$board1->Load($game1->board_id);
				$board2->Load($game2->board_id);
				$db = Database::instance();
				$q = "SELECT COUNT(*) as count FROM topics where board_id = ".$game1->board_id;
				$g1topics = $db->query($q)->current()->count;
				$q = "SELECT COUNT(*) as count FROM topics where board_id = ".$game2->board_id;
				$g2topics = $db->query($q)->current()->count;

				//if($g1topics >= $g2topics)
				$this->mergegame($game1, $game2); //always make game1 the primary
				$game = $game1Id;
			}

            url::redirect('games/view/'.$game);
		}
		else
		{
			url::redirect('games/');
		}
	}

	protected function mergegame($game1, $game2)
	{
		//copy over g2 stats
		$game1->gt_url = (strlen($game2->gb_url) > 0) ? $game2->gt_url : $game1->gt_url;
		$game1->ign_url = (strlen($game2->gb_url) > 0) ? $game2->ign_url : $game1->ign_url;
		$game1->mc_url = (strlen($game2->mc_url) > 0) ? $game2->mc_url : $game1->mc_url;
		$game1->mc_score = (strlen($game2->mc_score) > 0) ? $game2->mc_score : $game1->mc_score;
		if($game2->slug != $game1->slug)
		{
			if(strlen($game1->slugalt1) == 0)
				$game1->slugalt1 = $game2->slug;
			else
				$game1->slugalt2 = $game2->slug;
		}
		$game1->Save();
		$query = $this->db->query('UPDATE topics SET board_id = ? WHERE board_id = ?',$game1->board_id, $game2->board_id);
		$query = $this->db->query('UPDATE IGNORE boards_tags SET board_id = ? WHERE board_id = ?',$game1->board_id, $game2->board_id);
		$query = $this->db->query('UPDATE IGNORE game_statuses SET game_id = ? WHERE game_id = ?',$game1->id, $game2->id);
		$query = $this->db->query('DELETE FROM boards_tags WHERE board_id = ?',$game2->board_id);
		$query = $this->db->query('DELETE FROM game_statuses WHERE game_id = ?',$game2->id);
		//delete board
		$query = $this->db->query('DELETE FROM subscriptions WHERE board_id = ?',$game2->board_id);
		$query = $this->db->query('DELETE FROM exclude_boards WHERE board_id = ?',$game2->board_id);
		$query = $this->db->query('DELETE FROM boards_banned WHERE board_id = ?',$game2->board_id);
		$query = $this->db->query('DELETE FROM boardlists_boards WHERE board_id = ?',$game2->board_id);
		$query = $this->db->query('DELETE FROM boards WHERE id = ?',$game2->board_id);
		$query = $this->db->query('DELETE FROM games WHERE id = ?',$game2->id);
	}

    public function submit()
	{
		if ($this->isLoggedIn) //'admin'
		{
            $user = Session::instance()->get('auth_user');
			$game = new Game_Model();

			$post = $this->input->post();

			$post = Validation::factory($post)
				->pre_filter('trim');

			if ($post->validate())
			{
				//$postdata_array = $post->safe_array();
				$postdata_array = $post;

				if(isset($postdata_array['gameid']))
				{
					if($this->isAdmin)
						$game->Load(intval($postdata_array['gameid']));
					else{
						echo json_encode(array('returnCode'=>0));
						return;
					}
				}

				if(isset($postdata_array['title']))
					$game->title = $postdata_array['title'];
                if(isset($postdata_array['mc_url']))
					$game->mc_url = $postdata_array['mc_url'];
                if(isset($postdata_array['gr_url']))
					$game->gr_url = $postdata_array['gr_url'];
                if(isset($postdata_array['gs_url']))
					$game->gs_url = $postdata_array['gs_url'];
                if(isset($postdata_array['gt_url']))
					$game->gt_url = $postdata_array['gt_url'];
                if(isset($postdata_array['gb_url']))
					$game->gb_url = $postdata_array['gb_url'];
				if(isset($postdata_array['slug']))
					$game->slug = $postdata_array['slug'];
				if(isset($postdata_array['slugalt1']))
					$game->slugalt1 = $postdata_array['slugalt1'];
				if(isset($postdata_array['slugalt2']))
					$game->slugalt2 = $postdata_array['slugalt2'];
					
				if(isset($postdata_array['code']))
					$game->code = $postdata_array['code'];

				if($game->id == 0)
					$game->user_id = $user->id;
				$game->Save();
			}
            else if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>0));
                return;
            }
            //return success
            if($this->isAjax)
            {
                echo json_encode(array('returnCode'=>1,'id'=>$game->id));
                return;
            }

            url::redirect('topics/post/'.$game->board_id);
		}
		else
		{
			url::redirect('products/');
		}
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
