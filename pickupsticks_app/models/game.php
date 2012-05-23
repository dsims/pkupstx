<?php
class Game_Model extends Base_Model {
    
    var $title = '';
    var $slug = '';
    var $description = '';
    var $date_added = 0;
    var $date_submit = 0;
    var $date_released = 0;
    var $temp = 0;
    var $user_id = 0;
    var $user = '';
    var $mc_url = '';
    var $mc_score = 0;
    var $mc_image = '';
    var $gr_url = '';
    var $gs_url = '';
    var $gt_url = '';
    var $gb_url = '';
    var $ign_url = '';
    var $board_id = 0;
    var $board = '';
	var $slugalt1 = '';
	var $slugalt2 = '';
	var $code = '';
    
    var $date_released_format = '';
	var $color = '#CCCCCC';

    function __construct()
    {
	$this->dbTable = "games";
        parent::__construct();
    }

    function format()
	{
        $this->title = $this->filter(html::specialchars($this->title));
        $this->description = $this->htmlify($this->filter(html::specialchars($this->description)));
        $this->board = $this->filter(html::specialchars($this->board));
        if($this->isUrl($this->mc_url))
        {
            $this->mc_url = html::specialchars($this->mc_url);
        }
        else{
            $this->mc_url = '';
        }

        $this->date_released_format = gmdate("Y-m-d g:i", $this->date_released);
	}
        
	function getValues()
	{
		$record = $this->getValuesForSave();
        $record["user"] = $this->user;
        $record["board"] = $this->board;
		return $record;
	}

    function getValuesForSave()
    {
        $record = array();
		$record["id"] = $this->id;
		$record["title"] = $this->title;
		$record["slug"] = $this->slug;
		$record["description"] = $this->description;
		$record['mc_url'] = $this->mc_url;
        $record['mc_score'] = $this->mc_score;
        $record['mc_image'] = $this->mc_image;
        $record["user_id"] = $this->user_id;
        $record["board_id"] = $this->board_id;
        $record["date_released"] = $this->date_released;
        $record["temp"] = $this->temp;
        $record['gr_url'] = $this->gr_url;
        $record['gs_url'] = $this->gs_url;
        $record['gt_url'] = $this->gt_url;
        $record['gb_url'] = $this->gb_url;
        $record['ign_url'] = $this->ign_url;
		$record['slugalt1'] = $this->slugalt1;
		$record['slugalt2'] = $this->slugalt2;
		$record['code'] = $this->code;

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
		$this->id = $record->id;
		$this->title = $record->title;
		$this->slug = $record->slug;
		$this->description = $record->description;
		$this->date_added = $record->date_added;
        $this->date_submit = $record->date_submit;
        $this->date_released = $record->date_released;
		$this->mc_url = $record->mc_url;
        $this->mc_score = $record->mc_score;
        $this->mc_image = $record->mc_image;
        $this->user_id = $record->user_id;
        $this->board_id = $record->board_id;
        $this->temp = $record->temp;
        $this->gr_url = $record->gr_url;
        $this->gs_url = $record->gs_url;
        $this->gt_url = $record->gt_url;
        $this->gb_url = $record->gb_url;
        $this->ign_url = $record->ign_url;
		$this->slugalt1 = $record->slugalt1;
		$this->slugalt2 = $record->slugalt2;
		$this->code = $record->code;

        $this->user = $record->user;
        $this->board = $record->board;

		if($this->mc_score > 74)
			$this->color = '#62C746';
		else if($this->mc_score > 59)
			$this->color = '#FBB803';
		else
			$this->color = '#CC0000';

	}

    function Save()
	{
        if(strlen($this->title) == 0)
            return false;
		if(strlen($this->slug) == 0)
		{
			$this->slug = slug::format($this->title);
		}

        if(!$this->isUrl($this->mc_url))
            $this->mc_url = '';
        $isnew = ($this->id == 0);
		parent::Save();
        if($isnew)
        {
            $board = new Board_Model();
            $board->owner_id = $this->id;
            $board->type = 'g';
            $board->title = $this->title;
            $board->Save();
            if($board->id > 0)
            {
                $this->board_id = $board->id;
                $this->save();
            }

			$log = new Eventlog_Model();
			$log->user_id = $this->user_id;
			$log->target_id = $this->id;
			$log->type = Eventlog_Model::NewGame;
			$log->Save();
        }
	}

    function Find($id=0, $count=0, $sort=null, $dir='desc')
    {
        return $this->FindFinal($id,$count);
    }

    function Load($id)
    {
        $result = $this->FindFinal($id, 0);
		if(isset($result[0]))
			$this->setValues($result[0]);
    }
    function LoadWhere($property, $value, $where2='')
    { 
        $result = $this->FindFinal(0, 1, $property, '=', $value, $where2);
        if(isset($result[0]))
            $this->setValues($result[0]);
    }

    function FindSince($since=0, $count=0, $property='', $comparison='', $value='')
    {
        $since = intval($since);
        $where2 = ' AND t.date_submit > '.$since;
        return $this->FindFinal(0, $count, $property, $comparison, $value, $where2);
    }
   	function FindByCode($code)
    {
        return $this->FindFinal(0, 1, 'code', '=', $code);
    }
    
    function FindFinal($id=0, $count=0, $property='', $comparison='', $value='', $where2='', $orderby='g.date_submit DESC')
    {
        $id = intval($id);
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
            $where .= ' AND g.id = '.$id;
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

        $q = 'SELECT g.id, g.title, g.description, g.slug, g.date_added, g.date_submit, g.date_released, g.mc_url, g.mc_score, g.mc_image, g.user_id, g.ign_url, g.slugalt1, g.slugalt2,
                u.username AS user, 
                r.id AS board_id, r.title as board
                FROM '.$this->dbTable.' g
                JOIN boards r ON r.id = g.board_id
                LEFT JOIN users u ON u.id = g.user_id
                '.$where.' ORDER BY '.$orderby.' '.$limit;

        $query = $this->db->query($q);
        return $query->result(TRUE, 'Game_Model');
    }

    function AddTag($tag)
    {
        $query = $this->db->query('INSERT IGNORE INTO boards_tags (board_id, tag_id) VALUES (?,?)',array($this->board_id ,$tag));
    }
}