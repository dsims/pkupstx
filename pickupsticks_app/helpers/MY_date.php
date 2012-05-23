<?php defined('SYSPATH') or die('No direct script access.');
 
class date extends date_Core {
 
	public static function printTime($timestamp, $tzone = 0.0)
	{
		if($timestamp == 0)
			return 'none';
		$timestamp = $timestamp + ($tzone * 3600); 
		
		$timesince = '';
		$arr = date::timespan($timestamp);
		if($arr['years'] > 0 || $arr['months'] > 0 || $arr['days'] > 0)
		{
			$timesince = date("M j, Y", $timestamp);
		}
		else if($arr['hours'] == 1)
			$timesince .= $arr['hours'].' hr and '.$arr['minutes'].' min ago';
		else if($arr['hours'] > 1)
			$timesince .= $arr['hours'].' hrs and '.$arr['minutes'].' min ago';
		else if($arr['minutes'] > 10)
			$timesince .= $arr['minutes'].' minutes ago';
		else
			$timesince .= 'few minutes ago';
		return $timesince;
	}
 
}
?>