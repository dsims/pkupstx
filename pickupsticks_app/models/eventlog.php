<?php
class Eventlog_Model extends Base_Model {
    
    var $user_id = 0;
    var $target_id = 0;
	var $location_id = 0;
	var $user2_id = 0;
    var $date_added = 0;
	var $type = 0;
    var $user = '';
	var $username = '';
    var $user2 = '';
	var $username2 = '';
	var $date_added_format = '';
	var $date_added_iso = '';
	var $readout = '';

	const NewTopic = 1;
    const EditTopic = 2;
	const DeleteTopic = 3;
	const NewComment = 4;
	const EditComment = 5;
	const DeleteComment = 6;
	const Like1 = 7;
	const Like2 = 8;
	const Like3 = 9;
	const SubBoard = 10;
	const UnsubBoard = 11;
	const NewGame = 12;
	const NewBoard = 13;
	const NewUser = 14;
	const ReviewGame = 15;
	const SubUser = 16;
	public static $Types = array(
		1=>'NewTopic',
		2=>'EditTopic',
		3=>'DeleteTopic',
		4=>'NewComment',
		5=>'EditComment',
		6=>'DeleteComment',
		7=>'Like1',
		8=>'Like2',
		9=>'Like3',
		10=>'SubBoard',
		11=>'UnsubBoard',
		12=>'NewGame',
		13=>'NewBoard',
		14=>'NewUser',
		15=>'ReviewGame',
		16=>'SubUser'
	);

    function __construct()
    {
	$this->dbTable = "event_log";
        parent::__construct();
    }

    function format($full=false)
	{
		$this->date_added_format = $this->formatTime($this->date_added);
		$this->date_added_iso = gmdate("c", $this->date_added);//<abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>;
		switch($this->type)
		{
			case Eventlog_Model::NewTopic:
				$this->readout = 'made a new post';
				break;
			case Eventlog_Model::EditTopic:
				$this->readout = 'edited a post';
				break;
			case Eventlog_Model::DeleteTopic:
				$this->readout = 'deleted a post';
				break;
			case Eventlog_Model::NewComment:
				$this->readout = 'replied to a post by '.$this->user2;
				break;
			case Eventlog_Model::EditComment:
				$this->readout = 'edited a reply';
				break;
			case Eventlog_Model::DeleteComment:
				$this->readout = 'deleted a reply';
				break;
			case Eventlog_Model::Like1:
				$this->readout = 'liked a post by '.$this->user2;
				break;
			case Eventlog_Model::Like2:
				$this->readout = 'disliked a post by '.$this->user2;
				break;
			case Eventlog_Model::Like3:
				$this->readout = 'gave '.$this->user2.' a cookie!';
				break;
			case Eventlog_Model::ReviewGame:
				$this->readout = 'reviewed a product';
				break;
		}
		if(strlen($this->readout))
		{
			if($full)
				$this->readout = $this->user.' '.$this->readout;
			$this->readout .= ' '.$this->date_added_format;
		}
	}
    
	function getValues()
	{
        $record = $this->getValuesForSave();
		$record['user'] = $this->user;
		$record['username'] = $this->username;
		$record['user2'] = $this->user2;
		$record['username2'] = $this->username2;
		return $record;
	}

    function getValuesForSave()
	{
        $record = parent::getValues();
		$record["user_id"] = $this->user_id;
		$record["user2_id"] = $this->user2_id;
        $record["target_id"] = $this->target_id;
		$record["location_id"] = $this->location_id;
		if($this->date_added == 0)
			$this->date_added = time();
		$record['date_added'] = $this->date_added;
		$record['type'] = $this->type;

		return $record;
	}

	function setValues($record)
	{
        parent::setValues($record);
        
		$this->user_id = $record->user_id;
		$this->user2_id = $record->user2_id;
        $this->target_id = $record->target_id;
		$this->location_id = $record->location_id;
		$this->date_added = $record->date_added;
		$this->type = $record->type;
        $this->user = $record->user;
		$this->username = $record->username;
	}
	function Load($id)
    {
        $result = $this->FindFinal($id);
        $this->setValues($result[0]);
    }

    function FindFinal($id=0, $count=0, $where2 = '')
    {
		$id = intval($id);
		$where = 'WHERE 1=1';
		if($id > 0)
		{
			$where = ' AND id = '.$id;
		}
        if($count > 0)
        {
            $count = intval($count);
            $limit = "LIMIT 0, $count";
        }
        else
            $limit = '';
		if($where2 != '')
			$where .= ' AND '.$where2;

        $q = 'SELECT c.id, c.target_id, c.date_added, c.user_id, c.user2_id, c.type, c.location_id,
                u.title AS user, u.avatar as user_avatar, u.username as username,
				u2.title AS user2, u2.avatar as user_avatar2, u2.username as username2
                FROM '.$this->dbTable.' c
                LEFT JOIN users u ON u.id = c.user_id
				LEFT JOIN users u2 ON u2.id = c.user2_id
                '.$where.' ORDER BY c.date_added DESC '.$limit;

        $query = $this->db->query($q);
        return $query->result(TRUE, 'Eventlog_Model');
    }

	function Save()
	{
		$bot0 = Kohana::config('nerr.newsbot') == $this->user_id;
		$bot1 = Kohana::config('nerr.ign_bot') == $this->user_id;
		$bot2 = Kohana::config('nerr.gt_bot') == $this->user_id;
		$bot3 = Kohana::config('nerr.gon_bot') == $this->user_id;
		$bot4 = Kohana::config('nerr.joy_bot') == $this->user_id;
		$bot5 = Kohana::config('nerr.kot_bot') == $this->user_id;

		$isbot = ($bot0 || $bot1 || $bot2 || $bot3 || $bot4 || $bot5);

		$set = '';
		$set2 = '';
		$this->type = intval($this->type);
		switch($this->type)
		{
			case Eventlog_Model::NewTopic;
				if($isbot) return;
				$set = 'topics = topics+1';
				break;
			case 2: //edit topic
				if($isbot) return;
				break;
			case 3: // delete topic
				break;
			case 4: // new reply
				$set = 'comments = comments+1';
				$set2 = 'getcomments = getcomments+1';
				break;
			case 5: // edit reply
				break;
			case 6: // delete reply
				break;
			case 7: // like1 topic
				$set = 'givelike1 = givelike1+1';
				$set2 = 'getlike1 = getlike1+1';
				break;
			case 8: // like2 topic
				$set = 'givelike2 = givelike2+1';
				$set2 = 'getlike2 = getlike2+1';
				break;
			case 9: // like3 topic
				$set = 'givelike3 = givelike3+1';
				$set2 = 'getlike3 = getlike3+1';
				break;
			case 10: // sub board
				break;
			case 11: // unsub board
				break;
			case 12: // new game
				$set = 'addgame = addgame+1';
				break;
			case 13: // new board
				if($isbot) return;
				$set = 'addboard = addboard+1';
				break;
			case 14: // new user
				break;
			case 15: // review game
				break; //todo
		}
		if($set != '')
			$this->db->query('UPDATE users_stats set '.$set.' where user_id = ?', $this->user_id);
		if($set2 != '')
			$this->db->query('UPDATE users_stats set '.$set2.' where user_id = ?', $this->user2_id);
		parent::Save();
	}
}
