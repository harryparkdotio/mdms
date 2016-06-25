<?php

// Turns on error displaying; helpful for debugging errors.
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

require_once('classes/pages.php');
require_once('classes/files.php');
require_once('classes/functions.php');
require_once('config/db.php');
require_once('classes/Login.php');

$login = new Login();
$pages = new Pages();
$filehandling = new File();
$url = new URL();

if ($login->isUserLoggedIn() == true) {
	if ($url->url(0) == '') {
		echo $pages->Home();
	} elseif ($url->url(0) == 'pages') {
		if ($url->url(1) == 'edit') {
			if ($url->url(2) != '') {
				echo $pages->editPage();
			} else {
				echo $pages->Pages();
			}
		} elseif ($url->url(1) == 'new') {
			echo $pages->newPage();
		} elseif ($url->url(1) == 'delete') {
			echo $pages->Pages();
		} elseif ($url->url(1) == '') {
			echo $pages->Pages();
		} else {
			echo $pages->error();
		}
	} elseif ($url->url(0) == 'partials') {
		if ($url->url(1) == 'edit') {
			if ($url->url(2) != '') {
				echo $pages->editPartial();
			} else {
				echo $pages->Partials();
			}
		} elseif ($url->url(1) == 'new') {
			echo $pages->newPartial();
		} elseif ($url->url(1) == 'delete') {
			echo $pages->Partials();
		} elseif ($url->url(1) == '') {
			echo $pages->Partials();
		} else {
			echo $pages->error();
		}
	} elseif ($url->url(0) == 'users') {
		if ($url->url(1) == 'edit') {
			if ($url->url(2) != '') {
				echo $pages->editUser();
			} else {
				echo $pages->Users();
			}
		} elseif ($url->url(1) == 'new') {
			echo $pages->newUser();
		} elseif ($url->url(1) == 'delete') {
			echo $pages->User();
		} elseif ($url->url(1) == '') {
			echo $pages->Users();
		} else {
			echo $pages->error();
		}
	} elseif ($url->url(0) == 'user') {
		if ($url->url(1) == 'profile') {
			echo $pages->userProfile();
		} elseif ($url->url(1) == '') {
			echo $pages->Users();
		} else {
			echo $pages->error();
		}
	} elseif ($url->url(0) == 'settings') {
		if ($url->url(1) == '') {
			echo $pages->Settings();
		} else {
			echo $pages->error();
		}
	} else {
		echo $pages->error();
	}
} else {
	echo $pages->login();
}