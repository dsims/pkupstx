<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * @package  Core
 *
 * Domain, to restrict the cookie to a specific website domain. For security,
 * you are encouraged to set this option. An empty setting allows the cookie
 * to be read by any website domain.
 *
$config['domain'] = substr($_SERVER['SERVER_NAME'],
                           strpos($_SERVER['SERVER_NAME'], '.'),
                           100);
 *
 */
$config['domain'] = '';