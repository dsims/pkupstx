<?php defined('SYSPATH') OR die('No direct access allowed.');

class User_Model extends Auth_User_Model {
	
	public function getName()
	{
		return (strlen($this->username)) ? $this->username : 'Twapper User';
	}

    public function SetFriends($ids)
    {
        if(sizeof($ids) == 0)
            return;

        if($this->id == 0)
            return;

        $sql = 'delete from friends where user_id = '.$this->id;
        $result = $this->db->query($sql);

        $sql = 'insert into friends (user_id, friend_id) VALUES ';
        $first = true;
        foreach($ids as $id)
        {
            if(!$first)
                $sql .= ',';
            $sql .= '('.$this->id . ', '.$id.')';
            $first = false;
        }
        $result = $this->db->query($sql);
    }
	
} // End User Model