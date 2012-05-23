<?php
class Base_Model extends Model
{
	var $id = 0;
	var $dbTable = "notset";
	var $isAdmin = 0; //set from controller to query based on admin status

	function __construct()
	{
		// load database library into $this->db (can be omitted if not required)
		parent::__construct();
	}
	
	function getValues()
	{
		$record = array();
		$record['id'] = $this->id;
		return $record;
	}

    function getValuesForSave()
    {
        return $this->getValues();
    }
	
	function setValues($record)
	{
		$this->id = $record->id;
	}

	function Save()
	{
		//return?
		if($this->id > 0)
		{
			$this->db->where('id', $this->id);
			return $this->db->update($this->dbTable, $this->getValuesForSave());
		}
		else
		{
			$result = $this->db->insert($this->dbTable, $this->getValuesForSave());
			//get id
			$this->id = $result->insert_id( );
			return $this->id > 0;
		}
	}
	
	function Delete()
	{
		if($this->id > 0)
		{
			$this->db->where('id', $this->id);
			$this->db->delete($this->dbTable);
		}
		else return false;
	}
	
	function Load($id)
	{
		if($id > 0)
		{
			$this->db->where('id', $id);
			$query = $this->db->get($this->dbTable);
			if ($query->count() > 0) 
			{
				$this->setValues($query->current());
			}
		}
	}
	
	function LoadWhere($property, $value)
	{
		if(isset($value))
		{
			$this->db->where($property, $value);
			$query = $this->db->get($this->dbTable);
			if ($query->count() > 0) 
			{
				$this->setValues($query->current());
			}
		}
	}
	
	function Find($start=0, $count=0, $sort=null, $dir='desc')
	{
		if($count == 0)
		{
			return $this->db->get($this->dbTable);
		}
		else
		{
			
			if(strlen($sort) && property_exists($this, $sort))
			{
				$dir = ($dir != 'desc' && $dir != 'asc') ? 'desc' : $dir;
				$this->db->orderby($sort, $dir);
			}
			return $this->db->get($this->dbTable, $count, $start);
		}
	}
	function FindWhere($property, $value, $count)
	{
		if($count > 0)
		{
			$this->db->where($property, $value);
			return $this->db->get($this->dbTable, $count);
		}
		else
		{
			$this->db->where($property, $value);
			return $this->db->get($this->dbTable);
		}
	}

    function reduceurl($url, $url_length) {
        $reduced_url = substr($url, 0, $url_length);
        if (strlen($url) > $url_length) $reduced_url .= '...';

        return $reduced_url;
    }
	function formatTime($time)
    {
        /* Works out the time since the entry post, takes a an argument in unix time (seconds) */
        $SECOND = 1;
        $MINUTE = 60 * $SECOND;
        $HOUR = 60 * $MINUTE;
        $DAY = 24 * $HOUR;
        $MONTH = 30 * $DAY;

        $delta = time() - $time;
        if ($delta < 1 * $MINUTE)
        {
          return $delta == 1 ? "one second ago" : floor($delta) . " seconds ago";
        }
        if ($delta < 2 * $MINUTE)
        {
          return "a minute ago";
        }
        if ($delta < 45 * $MINUTE)
        {
          return floor($delta/60) . " minutes ago";
        }
        if ($delta < 90 * $MINUTE)
        {
          return "an hour ago";
        }
        if ($delta < 24 * $HOUR)
        {
          return floor($delta/60/60) . " hours ago";
        }
        if ($delta < 48 * $HOUR)
        {
          return "yesterday";
        }
        if ($delta < 30 * $DAY)
        {
          return floor($delta/60/60/24) . " days ago";
        }
        if ($delta < 12 * $MONTH)
        {
          $months = floor($delta/60/60/24/30);
          return $months <= 1 ? "one month ago" : $months . " months ago";
        }
        else
        {
          $years = floor($delta/60/60/24/30/365);
          return $years <= 1 ? "one year ago" : years . " years ago";
        }
    }
    function filter($text, $censor=0)
    {
		return $text;
    }
    function htmlify($text)
    {
		$text = preg_replace("/(\r\n){1,}/","<br/>",$text);
		$text = preg_replace("/\n{2,}/","<br/>",$text);
		$text = preg_replace("/\r{2,}/","<br/>",$text);

		$text =  preg_replace_callback(
     array(
		'/(^|\s)((https?):\/\/[^<> \n\r]+)/i',
       '/(^|\s)(www.[^<> \n\r]+)/i'
       ),
     'htmlifycallback',
       $text
		);
   
		if(strlen($text))
			return nl2br($text);
		else return '';
    }
    function isUrl($text)
    {
        return (filter_var($text, FILTER_VALIDATE_URL));
    }
	function urlTypes($urls)
	{
		if(!$urls)
			return '';
		$types = '';
		if(preg_match('#\.(jpg|jpeg|gif|png)#is', $urls) || preg_match('#(deviantart.com)#is', $urls))
			$types .= ' image';
		if(preg_match('#(youtube.com|viddler.com|dailymotion.com|video.google.com|livevideo.com|gametrailers.com|metacafe.com|vimeo.com|ign.com/dor|/17-|wegame.com|megavideo.com|embedr.com|revver.com|livestream.com|blip.tv/play|g4tv.com/lv3|collegehumor.com/video|dorkly.com/video|flipnote.hatena.com|motionbox.com)#is', $urls))
			$types .= ($types == '') ? ' video' : ', video';
		if(preg_match('#(.mp3|esnips.com|thesixtyone.com)#is', $urls))
			$types .= ($types == '') ? ' audio' : ', audio';
		if($types == '')
			$types .= ' links';
		return $types;
	}
    function encodeFun($text)
    {
		return $text;
        //image
        if(preg_match('#\.(jpg|jpeg|gif|png)$#is', $text))
            return sprintf('<a href="%1$s" rel="gb_imageset[%2$d]"><img class="imgsize" src="%1$s" alt="%1$s" title="%1$s" /></a>', $text, $this->id);
	}
}
function htmlifycallback($match)
{
	//echo Kohana::debug($match);
	// Prepend http:// if no protocol specified
	$url = $match[2];
	$completeUrl = strpos($url, 'http://') === false ? "http://$url" : $url;
	$display = (strlen($url) > 42) ? substr($url, 0, 42).'...' : $url;

	return $match[1].'<a href="' . $completeUrl . '">'
		. $display . '</a>';
		//. $match[2] . $match[3] . $match[4] . '</a>';
}