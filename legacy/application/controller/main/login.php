<?php
/**
 * application_controller_main_login
 */
class controller_main_login extends controller_main_base
{
	protected $output_type;
	
	public function __construct()
	{
		parent::__construct(__CLASS__);
		$this->output_type = "ajax";
	}


	public function run()
	{
		view::set_special("ajax", "browser/ajax/modal.html");
		//debug::add_info("(".__FILE__.")<b>".__CLASS__."</b>::".__FUNCTION__."() betreten.");
		if(count(app::$request) == 0 || !array_key_exists('username', app::$request) || !array_key_exists('passhash', app::$request))
		{
			$_SESSION['notice']['default'] = "Please enter username & password!";
			$this->generate_special_output($this->output_type);
			Header("Location: " . cfg::$web_root);
			exit();
		}
		else
		{
			$username = app::$request['username'];
			$password = app::$request['passhash'];
			$admin = model_admin::get_admin_by_username($username);
			if(is_null($admin))
			{
				$_SESSION['notice']['default'] = "Unknown username!";
				Header("Location: " . cfg::$web_root);
				exit();
			}
			elseif($password != $admin->password )
			{
				$_SESSION['notice']['default'] = "Username and password combination mismatch!";
				Header("Location: " . cfg::$web_root);
				exit();
			}
			elseif($admin->active == 0)
			{
				$_SESSION['notice']['default'] = "Login not allowed!";
				Header("Location: " . cfg::$web_root);
				exit();
			}
			else
			{
				// cleanup previous session
				$_SESSION = array();
				$_COOKIE = array();
				session_regenerate_id(true); // unique session_id every login!
				$_SESSION['notice']['default'] = "Hello $username!";
				$_SESSION['admin'] = $username;
				$admin->last_login = date("Y-m-d H:i:s");
				$admin->save();
				$_SESSION['type'] = "admin";
				Header("Location: " . cfg::$web_root . $_SESSION['type'] . "/");
				exit();
			}
		}
	}
}
