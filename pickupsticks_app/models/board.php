<?php
class Board_Model extends Base_Model {

    var $title = '';
    var $slug = '';
    var $description = '';
    var $date_added = 0;
    var $date_submit = 0;
    var $charid = '';
    var $owner_id = 0;
    var $owner = '';
    var $comments = 0;
    var $type = '';
    var $status = 0;
    var $subscribers = 0;
	var $privacy = 0;

    function __construct()
    {
	$this->dbTable = "boards";
        parent::__construct();
    }

	function getValues()
	{
		$record = $this->getValuesForSave();
        $record["owner"] = $this->owner;
        $record["subscribers"] = $this->subscribers;
		return $record;
	}

    function getValuesForSave()
    {
        $record = array();
		$record["id"] = $this->id;
		$record["title"] = $this->title;
		$record["slug"] = $this->slug;
		$record["description"] = $this->description;
        $record["owner_id"] = $this->owner_id;
        $record['type'] = $this->type;
        $record['status'] = $this->status;
		$record['privacy'] = $this->privacy;

		//set if not set
		if($this->date_added == 0)
			$this->date_added = time();
		if($this->date_submit == 0)
			$this->date_submit = time();

		$record['date_added'] = $this->date_added;
        $record['date_submit'] = $this->date_submit;

		return $record;
    }

	function setValues($record)
	{
		$this->id = $record->id;//$record["id"];
		$this->title = $record->title;//$record["name"];
		$this->slug = $record->slug;
		$this->description = $record->description;
		$this->date_added = $record->date_added;
        $this->date_submit = $record->date_submit;
        $this->owner_id = $record->owner_id;
        $this->type = $record->type;
        $this->status = $record->status;
		$this->privacy = $record->privacy;

        if(isset($record->owner))
        $this->owner = $record->owner;
        $this->subscribers = $record->subscribers;
	}

    function Save()
	{
		$isnew = false;
        if(strlen($this->title) == 0 && $this->type != 'u')
            return false;
		if($this->id == 0)
		{
			$isnew = true;
			$this->date_submit = time();
		}

		parent::Save();

		if($isnew && $this->type == '')
		{
			$log = new Eventlog_Model();
			$log->user_id = $this->owner_id;
			$log->target_id = $this->id;
			$log->type = Eventlog_Model::NewBoard;
			$log->Save();
		}
	}

    function Find($start=0, $count=0, $sort=null, $dir='desc')
    {
        return $this->FindFinal($start,$count);
    }

    function Load($id)
    {
        $result = $this->FindFinal($id, 0);
        if(!isset($result[0]))
            return false;
        $this->setValues($result[0]);
    }

    function FindSince($since=0, $count=0, $property='', $comparison='', $value='')
    {
        $since = intval($since);
        $where2 = ' AND r.date_submit > '.$since;
        return $this->FindFinal(0, $count, $property, $comparison, $value, $where2);
    }

    function FindActive($count, $type)
    {
        $orderby = ' r.date_submit DESC, title ASC ';
		if($type == '')
			return $this->FindFinal(0, $count, '', '', '', '', $orderby);
        return $this->FindFinal(0, $count, 'type', '=', $type, '', $orderby);
    }
    function FindNewest($count, $type)
    {
        $orderby = ' r.date_added DESC, title ASC ';
		if($type == '')
			return $this->FindFinal(0, $count, '', '', '', '', $orderby);
		return $this->FindFinal(0, $count, 'type', '=', $type, '', $orderby);
    }
    function FindPopular($count, $type)
    {
        $orderby = ' r.subscribers, title ASC ';
        return $this->FindFinal(0, $count, 'type', '=', $type, '', $orderby);
    }

    function Search($count, $type, $terms)
    {
		switch($type)
		{
			case 'u';
				return $this->FindFinal(0, $count, 'type', '=', $type, " bu.title LIKE '%".mysql_real_escape_string($terms)."%' ");
				break;
			case 'g';
				return $this->FindFinal(0, $count, 'type', '=', $type, " g.title LIKE '%".mysql_real_escape_string($terms)."%' ");
				break;
			case 'any';
				return $this->FindFinal(0, $count, 'type', '<>', 'u', " ( g.title LIKE '%".mysql_real_escape_string($terms)."%' OR r.title LIKE '%".mysql_real_escape_string($terms)."%' )");
				break;
			case 'all';
				return $this->FindFinal(0, $count, '', '', '', " ( (r.type = 'u' AND u.title LIKE '%".mysql_real_escape_string($terms)."%') OR (r.type = 'g' AND g.title LIKE '%".mysql_real_escape_string($terms)."%') OR (r.type = '' AND r.title LIKE '%".mysql_real_escape_string($terms)."%') )");
				break;
			default;
				return $this->FindFinal(0, $count, 'type', '=', $type, " r.title LIKE '%".mysql_real_escape_string($terms)."%' ");
				break;
		}
    }

    function FindFinal($id=0, $count=0, $property='', $comparison='', $value='', $where2='', $orderby = 'title ASC')
    {
        $id = intval($id);
        if($id == 0 && $count > 0)
        {
            $count = intval($count);
            $limit = "LIMIT 0, $count";
        }
        else
            $limit = '';

            $where = ' WHERE status>=0 ';
        if($id > 0)
        {
            $where .= ' AND r.id = '.$id;
        }
        if(strlen($where2))
            $where .= " AND $where2 ";
        if(strlen($property) > 0 && strlen($comparison) > 0)
        {
            if($comparison == 'IN' && is_array($value))
            {
                $ids = implode(",",$value);
                $where .= " AND $property IN ($ids)";
            }
            else
                $where .= " AND $property $comparison '$value'";
        }


        $q = 'SELECT r.id, coalesce(bu.title, g.title, r.title, \'noname\') as title, r.description, r.slug, r.date_added, r.date_submit, r.owner_id, r.type, r.privacy,
                u.username AS owner, (SELECT COUNT(board_id) as subscribers from subscriptions where board_id = r.id) as subscribers
                FROM '.$this->dbTable.' r
                LEFT JOIN users u ON u.id = r.owner_id
				LEFT JOIN users bu ON bu.board_id = r.id
				LEFT JOIN games g ON g.board_id = r.id
                '.$where.' ORDER BY '.$orderby.' '.$limit;

        $query = $this->db->query($q);
        return $query->result(TRUE, 'Board_Model');
    }

    function FindByList($listid, $count=0)
	{
		$q = 'SELECT b.id, coalesce(bu.title, g.title, b.title, \'noname\') as title, b.description, b.slug, b.date_added, b.date_submit, b.owner_id, b.type, b.privacy,
		u.username AS owner, (SELECT COUNT(board_id) as subscribers from subscriptions where board_id = b.id) as subscribers
		FROM '.$this->dbTable.' b
		JOIN boardlists_boards bl ON bl.board_id = b.id
		LEFT JOIN users u ON u.id = b.owner_id
		LEFT JOIN users bu ON bu.board_id = b.id
		LEFT JOIN games g ON g.board_id = b.id
		WHERE bl.boardlist_id = %1$d ';
		$q .= ' ORDER BY title ASC ';
		$q = sprintf($q, $listid);
        $query = $this->db->query($q);
        return $query->result();

		/*
		$list = new Boardlist_Model;

		$format = '%1$s.id AS id, %1$s.title as title, %1$s.slug as slug, %1$s.description AS description';
		$select = sprintf($format, $this->dbTable);

		$this->db->select($select);
		$this->db->from($this->dbTable);
		$this->db->join('boardlists_boards', 'boardlists_boards.board_id = '.$this->dbTable.'.id');
		$this->db->where('boardlists_boards.boardlist_id', $listid);
		return $this->db->get();
		 */
	}

	function FindIdByTags($tagIds)
	{
		$q = 'SELECT b.id
		FROM '.$this->dbTable.' b
		JOIN boards_tags tag ON tag.board_id = b.id
		WHERE tag.tag_id IN ';
            $where = '(0';
            foreach($tagIds as $tagid)
            {
                $where .= ','.$tagid;
            }
            $where .= ')';
		$q = $q.$where;
        $query = $this->db->query($q);
        return $query->result();
	}

	function FindSubscribedByUser($user_id, $type='', $nottype='', $orderby='')
	{
		if($type != 'u' && $type != 'g' && $type != 'a' && $type != 'general')
			$type = '';
		if($nottype != 'u' && $nottype != 'g' && $nottype != 'a')
			$nottype = '';
		$q = 'SELECT r.id, coalesce(bu.title, g.title, r.title, \'noname\') as title, r.description, r.slug, r.date_added, r.date_submit, r.owner_id, r.type, r.privacy,
		u.username AS owner, (SELECT COUNT(board_id) as subscribers from subscriptions where board_id = r.id) as subscribers
		FROM '.$this->dbTable.' r
		JOIN subscriptions s ON s.board_id = r.id
		LEFT JOIN users u ON u.id = r.owner_id
		LEFT JOIN users bu ON bu.board_id = r.id
		LEFT JOIN games g ON g.board_id = r.id
		WHERE s.user_id = %1$d ';
		if($type != ''){
			if($type == 'general')
				$type = '';
			$q .= " AND r.type = '$type'";
		}
		if($nottype != ''){
			$q .= " AND r.type <> '$nottype'";
		}
		if($orderby == '')
			$q .= 'ORDER BY title ASC ';
		else
			$q .= 'ORDER BY '.$orderby;
		$q = sprintf($q, $user_id);
        $query = $this->db->query($q);
        return $query->result(TRUE, 'Board_Model');
	}

	function FindHiddenByUser($user_id, $type='', $nottype='', $orderby='')
	{
		if($type != 'u' && $type != 'g' && $type != 'a' && $type != 'general')
			$type = '';
		if($nottype != 'u' && $nottype != 'g' && $nottype != 'a')
			$nottype = '';
		$q = 'SELECT r.id, coalesce(bu.title, g.title, r.title, \'noname\') as title, r.description, r.slug, r.date_added, r.date_submit, r.owner_id, r.type, r.privacy,
		u.username AS owner, (SELECT COUNT(board_id) as subscribers from subscriptions where board_id = r.id) as subscribers
		FROM '.$this->dbTable.' r
		JOIN exclude_boards s ON s.board_id = r.id
		LEFT JOIN users u ON u.id = r.owner_id
		LEFT JOIN users bu ON bu.board_id = r.id
		LEFT JOIN games g ON g.board_id = r.id
		WHERE s.user_id = %1$d ';
		if($type != ''){
			if($type == 'general')
				$type = '';
			$q .= " AND r.type = '$type'";
		}
		if($nottype != ''){
			$q .= " AND r.type <> '$nottype'";
		}
		if($orderby == '')
			$q .= 'ORDER BY title ASC ';
		else
			$q .= 'ORDER BY '.$orderby;
		$q = sprintf($q, $user_id);
        $query = $this->db->query($q);
        return $query->result(TRUE, 'Board_Model');
	}

	function FindHiddenUsersByUser($user_id)
	{
		return $this->db->query('SELECT poster_id as id, title, board_id from exclude_users JOIN users u on u.id = poster_id where user_id = ?', array($user_id));
	}

    function AddTopic()
	{
        $query = $this->db->query('UPDATE `'.$this->dbTable."` SET date_submit = ?, topics = topics+1 WHERE `id` = ?",array(time() , $this->id));
	}

    function format()
	{
        $this->title = $this->filter(html::specialchars($this->title));
        $this->description = $this->filter(html::specialchars($this->description));
	}

    function AddTag($tag)
    {
        $query = $this->db->query('INSERT IGNORE INTO boards_tags (board_id, tag_id) VALUES (?,?)',array($this->id ,$tag));
    }
	function BanUser($userid)
    {
        $query = $this->db->query('INSERT IGNORE INTO boards_banned (board_id, user_id) VALUES (?,?)',array($this->id ,$userid));
    }
	function UnBanUser($userid)
    {
        $query = $this->db->query('DELETE FROM boards_banned WHERE board_id = ? AND user_id = ?',array($this->id ,$userid));
    }
}