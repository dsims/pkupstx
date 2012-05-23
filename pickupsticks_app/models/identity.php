<?php
class Identity_Model extends Base_Model {
    
    var $id = 0;
    var $identity = '';
    var $user_id = 0;
    var $temp = 0;
    var $date_added = 0;
    var $type = '';
	var $key = '';

    function __construct()
    {
	$this->dbTable = "identities";
        parent::__construct();
    }

    function getValues()
    {
        $record = $this->getValuesForSave();
        return $record;
    }

    function getValuesForSave()
    {
        $record = parent::getValues();
        $record["identity"] = $this->identity;
        $record["user_id"] = $this->user_id;
        $record["temp"] = $this->temp;
        $record["type"] = $this->type;
        if($this->date_added == 0)
            $this->date_added = time();
        $record['date_added'] = $this->date_added;
		$record['key'] = $this->key;

        return $record;
    }

    function setValues($record)
    {
        parent::setValues($record);

        $this->identity = $record->identity;
        $this->user_id = $record->user_id;
        $this->temp = $record->temp;
        $this->type = $record->type;
        $this->date_added = $record->date_added;
        $this->key = $record->key;
    }
	
    function FindByUser($userid, $count=0)
    {
        //return $this->FindWhere('topic_id', $topicid, $count);
        return $this->FindFinal($userid, $count);
    }

    function FindFinal($userid=0, $count=0, $property='', $comparison='', $value='', $where2='')
    {
        $userid = intval($userid);
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
        if($userid > 0)
        {
            $where .= ' AND c.user_id = '.$userid;
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

        $q = 'SELECT i.id, i.user_id, i.temp, i.type, i.date_submit, i.key, i.identity
                FROM '.$this->dbTable.' i
                '.$where.' '.$limit;

        $query = $this->db->query($q);
        return $query->result(TRUE, 'Identity_Model');
    }

}