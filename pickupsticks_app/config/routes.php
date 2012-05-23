<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * @package  Core
 *
 * Sets the default route to "welcome"
 */
$config['_default'] = 'welcome';

$config['users/([0-9]+)(.)*'] = 'users/view/$1/$2';
$config['profile/([a-zA-Z0-9_-]+)(.)*'] = 'users/view/$1/$2';

$config['posts/([0-9]+)(.)*'] = 'topics/view/$1/$2';
$config['topics/xzingscan/'] = 'topics/xzingscan';
$config['topics/([0-9]+)(.)*'] = 'topics/view/$1/$2';

$config['games/([0-9]+)(.)*'] = 'games/view/$1/$2';
$config['games/own/([0-9]+)(.)*'] = 'games/userlist/own/$1';
$config['games/want/([0-9]+)(.)*'] = 'games/userlist/want/$1';
$config['games/played/([0-9]+)(.)*'] = 'games/userlist/played/$1';
$config['games/beat/([0-9]+)(.)*'] = 'games/userlist/beat/$1';
$config['games/edit/([0-9]+)(.)*'] = 'games/edit/$1';

$config['awards/([0-9]+)(.)*'] = 'awards/view/$1/$2';
$config['activity/([0-9]+)(.)*'] = 'activity/view/$1/$2';

$config['topics/submit'] = 'topics/submit';
$config['topics/scan'] = 'topics/scan';


$config['topics/refresh'] = 'topics/refresh';

$config['topics/getsingle'] = 'topics/getsingle';
