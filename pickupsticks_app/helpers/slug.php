<?php defined('SYSPATH') or die('No direct script access.');

class slug_Core {

	public static function format($text)
	{
		return url::title($text, '-');
	}
}

?>