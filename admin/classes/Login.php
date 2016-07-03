<?php

require_once('functions.php');

class Login
{
	public function __construct()
	{
		$functions = new Functions();
		if (isset($_POST['login'])) {
			$this->login();
		} elseif ($functions->url(0) == 'logout') {
			$this->logout();
		} elseif (isset($_POST['newuser'])) {
			$this->register();
		}
	}

	public function login()
	{
		$post = array($_POST['user_name'], $_POST['user_password']);
		$settings = include('../config/settings.php');
		$users = include('config/users.php');
		$_SESSION['logged_in'] = false;
		if (array_key_exists($post[0], $users)) {
			if (password_verify($post[1], $users[$post[0]]['password'])) {
				$_SESSION['logged_in'] = true;
				$_SESSION['user_name'] = $post[0];
				$_SESSION['access_level'] = $users[$post[0]]['access_level'];
				$_SESSION['user_id'] = $users[$post[0]]['id'];
			}
		}
	}

	public function logout()
	{
		$_SESSION['logged_in'] = false;
	}

	public function logged_in()
	{
		if ($_SESSION['logged_in'] = true) {
			return true;
		} else {
			return false;

		}
	}

	public function register()
	{
		$return = '';
		$users = include('config/users.php');
		if (isset($_POST['user_name'])) {
			$formData = array($_POST['user_name'], $_POST['user_pass'], $_POST['user_pass_repeat'], $_POST['access_level']);
			if (array_key_exists($formData[0], $users)) {
				return 'Username already exists!';
			} else {
				if ($formData[1] == $formData[2] && $formData[0] != '' && $formData[1] !== '') {
					$hash = password_hash($formData[1], PASSWORD_DEFAULT);
					$username = $formData[0];
					$access_level = $formData[3];
					$user_id = count($users);
					$return = "'" . $username . "' => ['id' => " . $user_id . ", 'password' => '" . $hash . "', 'access_level' => " . $access_level . "],";
				} else {
					$hash = 'PASSWORDS DID NOT MATCH!';
				}
			}
			return $return;
		}
	}
}