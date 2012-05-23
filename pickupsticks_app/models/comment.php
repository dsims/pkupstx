<?php
class Comment_Model extends Base_Model {
    
    var $comment = '';
    var $user_id = 0;
    var $topic_id = 0;
    var $date_submit = 0;
    var $date_added = 0;
    var $user = '';
	var $username = '';
	var $commentRaw = '';
	var $date_added_format = '';
	var $date_added_iso = '';

    function __construct()
    {
	$this->dbTable = "comments";
        parent::__construct();
    }

    function format($censor=0)
	{
		if(!stristr($this->commentRaw, '<p>'))
			$this->comment = $this->htmlify($this->filter($this->comment, $censor)); //removed html::specialchars since we call purify
		else
			$this->comment = $this->filter($this->comment, $censor);
		$this->commentRaw = '';
		$this->date_added_format = gmdate('M d Y H:i T', $this->date_added);//$this->formatTime($this->date_added);
		$this->date_added_iso = gmdate("c", $this->date_added);//<abbr class="timeago" title="2008-07-17T09:24:17Z">July 17, 2008</abbr>;
	}
    
	function getValues()
	{
        $record = $this->getValuesForSave();
        $record['user'] = $this->user;
		$record['username'] = $this->username;
		return $record;
	}

    function getValuesForSave()
	{
        $record = parent::getValues();
		$record["comment"] = $this->comment;
		$record["user_id"] = $this->user_id;
        $record["topic_id"] = $this->topic_id;
		if($this->date_submit == 0)
			$this->date_submit = time();
		if($this->date_added == 0)
			$this->date_added = time();
		$record['date_submit'] = $this->date_submit;
		$record['date_added'] = $this->date_added;
		$record['commentRaw'] = $this->commentRaw;

		return $record;
	}

	function setValues($record)
	{
        parent::setValues($record);
        
		$this->comment = $record->comment;
		$this->user_id = $record->user_id;
        $this->topic_id = $record->topic_id;
		$this->date_submit = $record->date_submit;
		$this->date_added = $record->date_added;
        $this->user = $record->user;
		$this->username = $record->username;
		$this->commentRaw = $record->commentRaw;
	}
	function Load($id)
    {
        $result = $this->FindFinal(0, 0, 'c.id', '=', $id);
        $this->setValues($result[0]);
    }
	function FindByTopic($topicid, $count=0)
	{
		//return $this->FindWhere('topic_id', $topicid, $count);
        return $this->FindFinal($topicid, $count);
	}
    function FindByTopicsSince($ids, $last)
	{
        return $this->FindFinal(0, 0, 'c.topic_id', 'IN', $ids, "c.date_added > $last");
	}

    function FindFinal($topicid=0, $count=0, $property='', $comparison='', $value='', $where2='')
    {
        $topicid = intval($topicid);
        if($count > 0)
        {
            $count = intval($count);
            $limit = "LIMIT 0, $count";
        }
        else
            $limit = '';

        $where = ' WHERE 1=1';
        if(strlen($where2) > 0)
            $where .= " AND $where2 ";
        if($topicid > 0)
        {
            $where .= ' AND c.topic_id = '.$topicid;
        }
        if(strlen($property) > 0 && strlen($comparison) > 0 && (is_array($value) || strlen($value) > 0))
        {
            if($comparison == 'IN' && is_array($value))
            {
                $ids = implode(",",$value);
                $where .= " AND $property IN ($ids)";
            }
            else
                $where .= " AND $property $comparison '$value'";
        }

        $q = 'SELECT c.id, c.topic_id, c.comment, c.date_added, c.date_submit, c.user_id, c.commentRaw,
                u.title AS user, u.avatar as user_avatar, u.username as username
                FROM '.$this->dbTable.' c
                LEFT JOIN users u ON u.id = c.user_id
                '.$where.' ORDER BY c.date_added ASC '.$limit;

        $query = $this->db->query($q);
        return $query->result(TRUE, 'Comment_Model');
    }

	function Save()
	{
        $oldid = $this->id;
		$this->date_submit = time();

		if(!strlen(trim($this->comment)))
			return false;

		$topic = new Topic_Model();
		$topic->Load($this->topic_id);
		if($oldid == 0 && $topic->comments > 200)
			return false;

		parent::Save();
		if($this->id > 0 && $oldid == 0)
		{
			$topic->AddComment($this->id);

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->user2_id = $topic->user_id;
			$log->location_id = $this->topic_id;
			$log->type = Eventlog_Model::NewComment;
			$log->Save();
		}
		else
		{
			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->user2_id = $topic->user_id;
			$log->location_id = $this->topic_id;
			$log->type = Eventlog_Model::EditComment;
			$log->Save();
		}
	}

	function DeleteAndUpdateTopic()
	{
		parent::Delete();
		$topic = new Topic_Model();
		$topic->Load($this->topic_id);
		$topic->UpdateComments();

		$log = new Eventlog_Model();
		$log->user_id = $this->user_id;
		$log->target_id = $this->id;
		$log->location_id = $this->topic_id;
		$log->user2_id = $topic->user_id;
		$log->type = Eventlog_Model::DeleteComment;
		$log->Save();
	}
}