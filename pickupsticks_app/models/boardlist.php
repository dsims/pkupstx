<?php
class Boardlist_Model extends Base_Model {

    var $title = '';
    var $slug = '';
    var $user_id = 0;

    function __construct()
    {
	$this->dbTable = "boardlists";
        parent::__construct();
    }

	function getValues()
	{
		$record = $this->getValuesForSave();
		return $record;
	}

    function getValuesForSave()
    {
        $record = array();
		$record["id"] = $this->id;
		$record["title"] = $this->title;
		$record["slug"] = $this->slug;
        $record["user_id"] = $this->user_id;

		return $record;
    }

	function setValues($record)
	{
		$this->id = $record->id;//$record["id"];
		$this->title = $record->title;//$record["name"];
		$this->slug = $record->slug;
        $this->user_id = $record->user_id;
	}

    function Save()
	{
        if(strlen($this->title) == 0)
            return false;

		parent::Save();
	}

	function Delete()
	{
		if($this->id > 0)
		{
			$query = $this->db->query('DELETE FROM boardlists_boards WHERE boardlist_id = ?',array($this->id));
			$query = $this->db->query('UPDATE users SET hometype = \'\' WHERE hometype = ?',array($this->id));
			parent::Delete();
		}
	}

    function FindWhere($property, $value, $count)
    {
        return parent::FindWhere($property, $value, $count)->result(TRUE, 'Boardlist_Model');
    }

    function format()
	{
        $this->title = $this->filter(html::specialchars($this->title));
	}

    function Addboard($board_id)
    {
        $query = $this->db->query('INSERT IGNORE INTO boardlists_boards (boardlist_id, board_id) VALUES (?,?)',array($this->id ,$board_id));
    }
    function Deleteboard($board_id)
    {
        $query = $this->db->query('DELETE FROM boardlists_boards WHERE boardlist_id = ? AND board_id = ?',array($this->id ,$board_id));
    }
    function GetBoards()
    {
        $board = new Board_Model();
        return $board->FindByList($this->id);
    }
}