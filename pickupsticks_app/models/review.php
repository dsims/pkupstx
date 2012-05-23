<?php
class Review_Model extends Topic_Model {
	var $game_id = 0;
	var $rating = 0;    
	
    function __construct()
    {
	$this->dbTable = "topics";
        parent::__construct();
    }

    
	function getValues()
	{
		$record = $this->getValuesForSave();
		parent::getValues();
        	$record["rating"] = $this->rating;
		$record['game_id'] = $this->game_id;
		return $record;
	}

    function getValuesForSave()
    {
	$record = parent::getValuesForSave();
		$record['rating'] = $this->rating;
		$record['game_id'] = $this->game_id;
		return $record;
    }

	function setValues($record)
	{
		$this->id = $record->id;//$record["id"];
		$this->title = $record->title;//$record["name"];
		$this->slug = $record->slug;
		$this->body = $record->body;
		$this->bodyRaw = $record->bodyRaw;
		$this->date_added = $record->date_added;
        $this->date_submit = $record->date_submit;
		$this->url = $record->url;
        $this->charid = $record->charid;
        $this->user_id = $record->user_id;
        $this->board_id = $record->board_id;
		$this->hidePublic = $record->hidePublic;
		$this->isPrivate = $record->isPrivate;
		$this->urls = $record->urls;
		$this->type = $record->type;
		$this->like1 = $record->like1;
		$this->like2 = $record->like2;
		$this->like3 = $record->like3;
		$this->locked = $record->locked;

        $this->user = $record->user;
		$this->username = $record->username;
		$this->avatar = $record->avatar;
        $this->user_board_id = $record->user_board_id;
        $this->comments = $record->comments;
        $this->comment_first_id = $record->comment_first_id;
        $this->comment_last_id = $record->comment_last_id;
        $this->comment_first = $record->comment_first;
        $this->comment_last = $record->comment_last;
        $this->comment_first_user = $record->comment_first_user;
        $this->comment_last_user = $record->comment_last_user;
		$this->comment_first_username = $record->comment_first_username;
        $this->comment_last_username = $record->comment_last_username;
		$this->comment_first_avatar = $record->comment_first_avatar;
        $this->comment_last_avatar = $record->comment_last_avatar;
        $this->board = $record->board;
		$this->boardtype = $record->boardtype;
	}

    function Save()
	{
        if($this->board_id == 0)
            return;

        if(strlen($this->title) == 0)
            return false;
		//$this->date_submit = time();

        if(!$this->isUrl($this->url))
            $this->url = '';
        $isnew = ($this->id == 0);

		$this->slug = url::title($this->filter($this->title), '-');
		if(strlen($this->slug) > 80)
			$this->slug = substr($this->slug, 0, 80);

		parent::Save();

		if($isnew)
        {
            $this->charid = Topic_Model::dec2any($this->id);
            parent::Save();
			if(!$this->isPrivate) //dont bump board if a PM
			{
				$board = new Board_Model();
				$board->Load($this->board_id);
				$board->AddTopic();
			}

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::NewTopic;
			$log->Save();
        }
		else
		{
			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::EditTopic;
			$log->Save();
		}
	}

	function Delete()
	{
		if($this->id > 0)
		{
			//delete comments
			$comment = new Comment_Model();
			$comments = $comment->FindByTopic($this->id);
			foreach($comments as $comment)
			{
				$comment->Delete();
			}
			//delete urls
			$query = $this->db->query('DELETE FROM topics_urls WHERE topic_id = ?',$this->id);

			parent::Delete();

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->location_id = $this->board_id;
			$log->type = Eventlog_Model::DeleteTopic;
			$log->Save();
		}
	}

    function Find($start=0, $boardid=0, $count=0, $sort=null, $dir='desc')
    {
        return $this->FindFinal($start,$boardid,0,0,$count);
    }

    function Load($id)
    {
        //$result = $this->FindFinal($id, 0);
		$result = $this->FindSingle($id);
		//echo Kohana::debug($result[0]);die();
		if(isset($result[0]))
			$this->setValues($result[0]);
    }
    function LoadWhere($id, $board_id, $property, $value, $where2)
    {
        $result = $this->FindFinal($id, $board_id, 0, 0, 1, $property, '=', mysql_real_escape_string($value), $where2);
        if(isset($result[0]))
            $this->setValues($result[0]);
    }

    function FindSince($since=0, $boardid=0, $count=0, $property='', $comparison='', $value='')
    {
        $since = intval($since);
        $where2 = ' AND t.date_submit > '.$since;
        return $this->FindFinal(0, $boardid, 0, 0, $count, $property, $comparison, $value, $where2);
    }
    function FindFinal($id=0, $boardid=0, $userid=0, $all=0, $count=0, $property='', $comparison='', $value='', $where2='', $ignorePrivacy=false)
    {
        $id = intval($id);
		$userid = intval($userid);
        $boardid = intval($boardid);
        if($id == 0 && $count > 0)
        {
            $count = intval($count);
            $limit = "LIMIT 0, $count";
        }
        else
            $limit = '';

            $where = ' WHERE 1=1';
        if($id > 0)
        {
            $where .= ' AND t.id = '.$id;
        }
        if(strlen($where2))
            $where .= " AND $where2 ";
        if(strlen($property) > 0 && strlen($comparison) > 0 && (is_array($value) || strlen($value) > 0))
        {
            if(($comparison == 'IN' || $comparison == 'NOT IN') && is_array($value))
            {
                $ids = implode(",",$value);
                $where .= " AND $property $comparison ($ids)";
            }
            else
                $where .= " AND $property $comparison '$value'";
        }

        $subs = '';
		if($userid == 0 && !$ignorePrivacy && $boardid > 0)
		{
			$where .= ' AND r.privacy <> 2 ';
		}
		else if($userid == 0 && !$ignorePrivacy)
		{
			$where .= ' AND r.privacy < 2 ';
		}

        if($boardid > 0){ //get a specific board
            $where .= ' AND r.id = '.$boardid;
			//hide topics you can't see
			if($userid > 0)
			{
				$subs = ' LEFT JOIN subscriptions s ON s.board_id = r.id AND s.user_id = '.$userid.' AND (r.privacy = 2 AND s.pending = 0) ';
				$where .= ' AND (1='.$this->isAdmin.' OR r.privacy < 2 OR r.privacy = 3 OR t.user_id = '.$userid.' OR (r.privacy = 2 AND s.board_id IS NOT NULL)) ';
			}
        }
        else if($userid > 0 && $all == 0) //join to get only boards subscribed to
        {
            $subs = ' JOIN subscriptions s ON s.board_id = r.id AND s.user_id = '.$userid.' AND (r.privacy < 2 || r.privacy = 3 || (r.privacy = 2 AND s.pending = 0)) ';
        }
        else if($userid > 0)//get all boards, excluding some
        {
			$subs = ' LEFT JOIN subscriptions s ON s.board_id = r.id AND s.user_id = '.$userid.' AND (r.privacy = 2 AND s.pending = 0) ';
            $subs .= ' LEFT JOIN exclude_boards eb ON eb.board_id = r.id AND eb.user_id = '.$userid;
            $subs .= ' LEFT JOIN exclude_users eu ON eu.poster_id = t.user_id AND eu.user_id = '.$userid;
			$subs .= ' LEFT JOIN boards_banned bb ON bb.board_id = r.id AND bb.user_id = '.$userid;
            $where .= ' AND (1='.$this->isAdmin.' OR r.privacy < 2 OR t.user_id = '.$userid.' OR (r.privacy >= 2 AND s.board_id IS NOT NULL)) AND eb.board_id IS NULL AND eu.poster_id IS NULL AND bb.board_id IS NULL ';
        }
		if($all == 4)
		{
			$bot0 = Kohana::config('nerr.newsbot');
			$bot1 = Kohana::config('nerr.ign_bot');
			$bot2 = Kohana::config('nerr.gt_bot');
			$bot3 = Kohana::config('nerr.gon_bot');
			$bot4 = Kohana::config('nerr.joy_bot');
			$bot5 = Kohana::config('nerr.kot_bot');
			$bot6 = Kohana::config('nerr.psb_bot');
			$where .= " AND (t.comments > 0 OR t.like1 > 0 OR t.like2 > 0 OR t.like3 > 0 OR t.user_id NOT IN ( $bot0, $bot1, $bot2, $bot3, $bot4, $bot5, $bot6 )) ";
		}
		$privacy = ' AND (t.isPrivate = 0 OR (t.isPrivate = 1 AND (t.user_id = '.$userid.' OR r.owner_id = '.$userid.')))';
		if($ignorePrivacy)
			$privacy = '';
		//GROUP_CONCAT(url.url) AS urls,  LEFT JOIN topics_urls url ON url.topic_id = t.id
        $q = 'SELECT t.id, t.title, t.body, t.slug, t.date_added, t.date_submit, t.charid, t.user_id, t.url, t.comments, t.comment_first_id,  t.comment_last_id, t.hidePublic, t.isPrivate, t.bodyRaw, t.like1, t.like2, t.like3, t.locked,
                u.title AS user, u.username as username, u.avatar as avatar, u.board_id as user_board_id,
                c1.comment AS comment_first, c2.comment AS comment_last,
                u1.title AS comment_first_user, u1.id AS comment_first_user_id, u1.username AS comment_first_username, u1.avatar AS comment_first_avatar, u2.id AS comment_last_user_id, u2.title AS comment_last_user,u2.avatar AS comment_last_avatar, u2.username AS comment_last_username,
                r.id AS board_id, coalesce(bu.title, g.title, r.title, \'noname\') as board, r.type as boardtype,
				t.urls, r.type
                FROM '.$this->dbTable.' t
                STRAIGHT_JOIN users u ON u.id = t.user_id
                STRAIGHT_JOIN boards r ON r.id = t.board_id
                LEFT JOIN comments c1 ON c1.id = t.comment_first_id
                LEFT JOIN comments c2 ON c2.id = t.comment_last_id
                LEFT JOIN users u1 ON u1.id = c1.user_id
                LEFT JOIN users u2 ON u2.id = c2.user_id
				LEFT JOIN users bu ON bu.board_id = r.id
				LEFT JOIN games g ON g.board_id = r.id
                '.$subs.'
                '.$where.$privacy.' ORDER BY t.date_submit DESC  '.$limit;
        //echo Kohana::debug($q);die();
        $query = $this->db->query($q);
        return $query->result(TRUE, 'Topic_Model');
    }

	function FindSingle($id)
    {
        $id = intval($id);
        $q = 'SELECT t.id, t.title, t.body, t.slug, t.date_added, t.date_submit, t.charid, t.url, t.user_id, t.url, t.comments, t.comment_first_id,  t.comment_last_id, t.hidePublic, t.isPrivate, t.bodyRaw, t.like1, t.like2, t.like3, t.locked,
                u.title AS user, u.username AS username, u.avatar as avatar, u.board_id as user_board_id,
                c1.comment AS comment_first, c2.comment AS comment_last,
                u1.title AS comment_first_user, u1.id AS comment_first_user_id, u1.username AS comment_first_username, u1.avatar AS comment_first_avatar, u2.id AS comment_last_user_id, u2.title AS comment_last_user,u2.avatar AS comment_last_avatar, u2.username AS comment_last_username,
                r.id AS board_id, coalesce(bu.title, g.title, r.title, \'noname\') as board, r.type as boardtype,
				t.urls, t.type
                FROM '.$this->dbTable.' t
                JOIN users u ON u.id = t.user_id
                JOIN boards r ON r.id = t.board_id
                LEFT JOIN comments c1 ON c1.id = t.comment_first_id
                LEFT JOIN comments c2 ON c2.id = t.comment_last_id
                LEFT JOIN users u1 ON u1.id = c1.user_id
                LEFT JOIN users u2 ON u2.id = c2.user_id
				LEFT JOIN users bu ON bu.board_id = r.id
				LEFT JOIN games g ON g.board_id = r.id
                WHERE t.id = '.$id.' GROUP BY t.id ORDER BY t.date_submit DESC LIMIT 1';
        //echo Kohana::debug($q);die();
        $query = $this->db->query($q);
        return $query->result(TRUE, 'Topic_Model');
    }

	function FindByCategory($catid, $count=0)
	{
		$cat = new Category_Model;
		
		$format = '%1$s.id AS id, %1$s.name as name, %1$s.slug as slug, %1$s.description AS description, %1$s.date_added AS date_added, %1$s.website AS website, %1$s.twitter_name AS twitter_name, %1$s.image_url AS image_url';
		$select = sprintf($format, $this->dbTable);
		
		$this->db->select($select);
		$this->db->from($this->dbTable);
		$this->db->join('app_cats', 'app_cats.app_id = '.$this->dbTable.'.id');
		$this->db->join($cat->dbTable, $cat->dbTable.'.id = app_cats.cat_id');
		$this->db->where($cat->dbTable.'.id', $catid);
		return $this->db->get();
	}
	
	function AddCategory($catid)
	{
		$result = $this->db->insert('app_cats', array('app_id' => $this->id, 'cat_id' => $catid));
		return count($result);
	}

    function AddURLs($urls)
	{
		$urlstr = '';
        foreach($urls as $url)
        {
			$url = trim($url);
			if(strlen($url) > 0 && strlen($urls.','.$url) <= 500)
			{
				$query = $this->db->query('INSERT IGNORE INTO topics_urls (url, topic_id) VALUES (?,?)',array($url ,$this->id));
				$urlstr .= (strlen($urlstr) > 0) ? ','.$url : $url;
			}
        }
		if(strlen($urlstr) > 0)
		{
			$this->urls = $urlstr;
			$this->Save();
		}
	}

	function AddLike($type, $user_id)
	{
		if($this->db->where(array('user_id'=> $user_id, 'topic_id'=>$this->id))->count_records('topics_likes'))
			return;
		$logtype = 0;
		$updatedate = false;
		switch($type)
		{
			case 1:
				$logtype = Eventlog_Model::Like1;
				break;
			case 2:
				$logtype = Eventlog_Model::Like2;
				break;
			case 3:
				if($this->db->where(array('user_id'=> $user_id, 'type'=>3, 'date_liked >'=>time()-96400))->count_records('topics_likes'))
				{
					return;
					//$this->db->query('SELECT id FROM topics_likes where user_id = ? AND type = 3 AND date_liked < ?',$user_id, time()-86400);
				}					
				$logtype = Eventlog_Model::Like3;
				$updatedate = true;
				break;
			default:
				return;
		}
		$query = $this->db->query('INSERT IGNORE INTO topics_likes (type, user_id, topic_id, date_liked) VALUES (?,?,?,?)',array($type, $user_id ,$this->id, time()));
		$update = 'UPDATE `'.$this->dbTable.'` SET like'.$type.' = like'.$type.'+1';
		if($updatedate)
			$update .= ', date_submit = '.time();
		$query = $this->db->query($update.' WHERE `id` = ?',array($this->id)); //date_submit

		$log = new Eventlog_Model();
		$log->user_id = $user_id;
		$log->target_id = $this->id;
		$log->user2_id = $this->user_id;
		$log->type = $logtype;
		$log->Save();

	}
	
	function AddComment($id)
	{
        if($this->comments == 0){
            $query = $this->db->query('UPDATE `'.$this->dbTable."` SET date_submit = ?, comments = comments+1, comment_first_id = ? WHERE `id` = ?",array(time() ,$id, $this->id));
        }
        else{
            $query = $this->db->query('UPDATE `'.$this->dbTable."` SET date_submit = ?, comments = comments+1, comment_last_id = ? WHERE `id` = ?",array(time(), $id, $this->id));
        }
	}
	function UpdateComments()
	{
		$query = $this->db->query('UPDATE `'.$this->dbTable."` SET comments = (SELECT COUNT(1) AS comments FROM comments WHERE topic_id = ?), comment_first_id = (SELECT id FROM comments WHERE topic_id = ? ORDER BY date_added ASC LIMIT 1), comment_last_id = (SELECT id FROM comments where topic_id = ? ORDER BY date_added DESC LIMIT 1) WHERE `id` = ?",$this->id,$this->id,$this->id,$this->id);
	}
}
