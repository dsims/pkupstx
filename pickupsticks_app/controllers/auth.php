<?php defined('SYSPATH') OR die('No direct access allowed.');
class Auth_Controller extends Site_Controller {

	// Do not allow to run in production
	const ALLOW_PRODUCTION = FALSE;

	function __construct() {
		parent::__construct();
		$this->template->title = 'Auth';
	}

	public function create()
	{

		$this->template->title = 'Register User';
        $post = $this->input->post();

        $post = Validation::factory($post)
            ->pre_filter('trim')
            ->add_rules('username', 'required','length[3,32]','alpha_dash')
            ->add_rules('password', 'required', 'length[4,40]');
            //->add_rules('email', 'required','email');

        if ($post->validate())
        {
			// Create new user
			//$user = ORM::factory('user');
				$openid = trim($post['username']);
        		$ident = new User_Identity_Model();
				if(!$ident->exists(array("claimed_id" => $openid)))
				{
					//whos the new guy?
					//register and ask for some more info
					// Create new user
					$user = ORM::factory('user');
					$user->email = $post['email'];
					$user->title = $post['username'];
					$user->username = $post['username'];
					$user->password = $post['password'];
					$user->created = time();
					if ($user->save() AND $user->add(ORM::factory('role', 'login')) AND $user->save())
					{
						$user->add_identity($openid, $openid, array('type'=>'basic'));

						$board = new Board_Model();
						$board->owner_id = $user->id;
						$board->type = 'u';
						$board->Save();
						if($board->id > 0)
						{
							$user->board_id = $board->id;
							$user->save();
						}
						
						$this->db = Database::instance();
						$this->db->query('INSERT INTO subscription_users (poster_id,user_id,pending) values (?,?,?)', array($user->id,$user->id,0));

						if(Auth::instance()->login($openid, true))
						{
							//show view to create new user or add id to existing user
							url::redirect('users/edit');
						}
					}
					$this->template->error = 'Hmm... something went wrong!';
					$view = new View('auth/login');
				}
				else
				{
					$this->template->error = 'Hmm... that OpenID already exists!';
					$view = new View('auth/login');
				}
			/*			
			if ( ! $user->exists(array("username" => $post['username'])))
			{
				foreach ($post as $key => $val)
				{
					// Set user data
					$user->$key = $val;
				}

				if ($user->save() AND $user->add(ORM::factory('role', 'login')))
				{
					Auth::instance()->login($user, $post['password']);

					// Redirect to the login page
					url::redirect('auth/login');
				}
			}*/
		}

		// Display the form
        $view = new View('auth/create');
        $this->template->content = $view;
	}

	public function login()
	{
		if ($this->isLoggedIn)
		{
			$this->template->title = 'User Logout';
            $view = new View('auth/logout');
            $this->template->content = $view;
		}
		else
		{
			$view = new View('auth/create');
			$this->template->title = 'User Login';
		}

		// Display the form
		$this->template->content = $view;
	}

	public function basic()
	{
		if ($this->isLoggedIn)
		{
			$this->template->title = 'User Logout';
            $view = new View('auth/logout');
            $this->template->content = $view;
		}
		else
		{
			$view = new View('auth/loginbasic');
			$this->template->title = 'User Login';
            $post = $this->input->post();

			if(!empty($post))
			{
				$post = Validation::factory($post)
				->pre_filter('trim')
				->add_rules('username', 'length[3,32]')
				->add_rules('password', 'length[4,40]');

				if ($post->validate())
				{
					// Load the user
					$user = ORM::factory('user', $post['username']);

					if (Auth::instance()->loginWithPassword($user, $post['password'], TRUE))
					{
						/*if ($user->add(ORM::factory('role', 'login')))
						{
							Auth::instance()->login($user, $post['password']);

							// Redirect to the login page
							url::redirect('auth/login');
						}*/

						// Login successful, redirect
						url::redirect('topics/');
					}
					else
					{
						$this->template->error = 'Wrong Username or Password';
						$view = new View('auth/loginbasic');
					}
				}
				else
				{
					$this->template->error = 'Invalid Username or Password';
					$view = new View('auth/loginbasic');
				}
			}
		}

		// Display the form
		$this->template->content = $view;
	}

	public function link()
	{
		if ($this->isLoggedIn)
		{
			$this->template->title = 'Link another ID to your account';
			$post = $this->input->post();

			$view = new View('auth/link');

			// Display the form
			$this->template->content = $view;
		}
		else
			url::redirect('auth/login');
	}

	public function logout()
	{
		// Force a complete logout
		Auth::instance()->logout(TRUE);

		// Redirect back to the login page
		url::redirect('auth/login');
	}

	public function rpx()
	{
		//The one-time-use token used for the auth_info API call
		if(!isset($_GET['token']) && !isset($_POST['token']))
		{
			die('Uh oh, something went wrong.  <a href="http://digibutter.nerr.biz/auth/login">try it again</a>');
			url::redirect('/');
		}

		$token = isset($_GET['token']) ? $_GET['token'] : $_POST['token'];
		$api_key = 'INSERTAPIKEYHERE';
		$base_url = 'https://rpxnow.com';

		$rpx = new RPX($api_key, $base_url);
		$result = $rpx->auth_info($token);

		if ($result !== null) {
			//get openid from result
			//$xpath = new DOMXPath($result);

			$element = $result->getElementsByTagName( "identifier" );
			$openid = $element->item(0)->nodeValue;

			//get type
			//echo Kohana::debug($result->saveHTML());
			
			$element = $result->getElementsByTagName( "providerName" );
			$idtype = $element->item(0)->nodeValue;

			$element = $result->getElementsByTagName( "email" );
			$email = (isset($element->item(0)->nodeValue)) ? $element->item(0)->nodeValue : '';

			//$identifier = $xpath->query('rsp/profile/identifier');
			if(Auth::instance()->login($openid, true))
			{
				//already in, go go go!
				url::redirect('/topics');
			}
			else
			{
				$ident = new User_Identity_Model();
				if(!$ident->exists(array("claimed_id" => $openid)))
				{
					//whos the new guy?
					//register and ask for some more info
					// Create new user
					$user = ORM::factory('user');
					$user->email = $email;
					$user->title = 'n00b';
					$user->username = uniqid();
					$user->created = time();
					if ($user->save() AND $user->add(ORM::factory('role', 'login')) AND $user->save())
					{
						$user->add_identity($openid, $openid, array('type'=>$idtype));

						$board = new Board_Model();
						$board->owner_id = $user->id;
						$board->type = 'u';
						$board->Save();
						if($board->id > 0)
						{
							$user->board_id = $board->id;
							$user->save();
						}

						if(Auth::instance()->login($openid, true))
						{
							//show view to create new user or add id to existing user
							url::redirect('users/edit');
						}
					}
					$this->template->error = 'Hmm... something went wrong!';
					$view = new View('auth/login');
				}
				else
				{
					$this->template->error = 'Hmm... that OpenID already exists!';
					$view = new View('auth/login');
				}

				$this->template->content = $view;
			}
		}
		else
			$this->template->content = 'NO RESULT!';
	}

	public function rpxadd()
	{
		if (!$this->isLoggedIn)
			die('Adding another account failed because you are not logged in!  Go <a href="http://digibutter.nerr.biz/auth/login">login</a> and try again.');

		//The one-time-use token used for the auth_info API call
		if(!isset($_GET['token']) && !isset($_POST['token']))
		{
			die('Uh oh, something went wrong.  <a href="http://digibutter.nerr.biz/auth/link">try it again</a>');
		}

		$user = Session::instance()->get('auth_user');

		$token = isset($_GET['token']) ? $_GET['token'] : $_POST['token'];
		$api_key = 'RPXAPIKEY';
		$base_url = 'https://rpxnow.com';

		$rpx = new RPX($api_key, $base_url);
		$result = $rpx->auth_info($token);

		if ($result !== null) {
			//get openid from result
			//$xpath = new DOMXPath($result);

			$element = $result->getElementsByTagName( "identifier" );
			$openid = $element->item(0)->nodeValue;

			//get type
			//echo Kohana::debug($result->saveHTML());

			$element = $result->getElementsByTagName( "providerName" );
			$idtype = $element->item(0)->nodeValue;

			$element = $result->getElementsByTagName( "email" );
			$email = (isset($element->item(0)->nodeValue)) ? $element->item(0)->nodeValue : '';


			$ident = new User_Identity_Model();
			if(!$ident->exists(array("claimed_id" => $openid)))
			{
				//add the new idenity to the user
				$user->add_identity($openid, $openid, array('type'=>$idtype));

				url::redirect('users/edit');

			}
			else
			{
				$this->template->error = 'Hmm... that ID already exists!  You should add a NEW account.';
				$view = new View('auth/link');
				$this->template->content = $view;
			}
		}
		else
			$this->template->content = 'NO RESULT!';
	}

	//ask if user is new or old
	private function register()
	{
		$iid = 0;
		$key = '';
		if(isset($get['iid']))
        {
            $iid = intval($get['iid']);
        }
		if(isset($get['key']))
        {
            $key = intval($get['key']);
        }

		$identity = new Identity_Model();
		$identity->Load($iid);
		if($identity->id != $iid || $identity != $key || $identity->temp != 1)
		{
			$this->template->content = 'Identity not valid';
			return;
		}

		$step = 0;
		if(isset($get['step']))
        {
            $step = intval($get['step']);
        }
		//step 0 - ask if user is new or existing
		//step 1 - ask for name
		//step 2 - ask for gender
		//step 3 - ask for avatar

		$view = new View('auth/register');
		$view->iid = $iid;
		$view->key = $key;
		$view->step = $step;
		$this->template->content = $view;
	}

	//ask for username to link to, for approval later
	private function linkwhat()
	{
		$iid = 0;
		$key = '';
		if(isset($get['iid']))
        {
            $iid = intval($get['iid']);
        }
		if(isset($get['key']))
        {
            $key = intval($get['key']);
        }

		$identity = new Identity_Model();
		$identity->Load($iid);
		if($identity->id != $iid || $identity != $key || $identity->temp != 1)
		{
			$this->template->content = 'Identity not valid';
			return;
		}

		$step = 1;
		if(isset($get['username']))
        {
            $username = $get['username'];
			//find user
			//if user doesnt exit, add error to view

			//link iid with user in pending state

			//display success step
			$step = 2;
        }

		$view = new View('auth/link');
		$view->iid = $iid;
		$view->key = $key;
		$view->step = $step;
		$this->template->content = $view;
	}

	private function join()
	{
		$iid = 0;
		if(isset($get['iid']))
        {
            $iid = intval($get['iid']);
        }
		if(isset($get['key']))
        {
            $key = intval($get['key']);
        }

		$identity = new Identity_Model();
		$identity->Load($iid);
		if($identity->id != $iid || $identity != $key || $identity->temp != 1)
		{
			$this->template->content = 'Identity not valid';
			return;
		}

		$user = ORM::factory('user');
		$user->date_added = time();
		if ($user->save() AND $user->add(ORM::factory('role', 'login')) AND $user->save())
		{
				$identity->user_id = $user->id;
				$identity->Save();

				$board = new Board_Model();
				$board->owner_id = $user->id;
				$board->type = 'u';
				$board->title = $user->username;
				$board->Save();
				if($board->id > 0)
				{
					$user->board_id = $board->id;
					$user->save();
				}

				if(Auth::instance()->login($identity->identity, true))
				{
						// Redirect to the settings page
						url::redirect('users/edit/new');
				}
				else
				{
						$this->template->error = 'Oops!  Login failed!';
						$view = new View('users/fail');
				}
		}
		else
		{
				$this->template->error = 'Oops!  User creation failed!';
				$view = new View('users/fail');
		}
	}

} // End Auth Controller