<?php

/**
 *
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 0.4
 * @package mdms - markdown management system
 * @subpackage Admin
 */

session_start();

require_once('classes/pages.php');
require_once('classes/files.php');
require_once('classes/functions.php');
require_once('classes/login.php');

$login = new Login();
$pages = new Pages();
$files = new Files();
$functions = new Functions();

if ($pages->defaults()['user']['loggedin'] == true) {
	if ($functions->url(0) == '') {
		echo $pages->Home();
	} elseif ($functions->url(0) == 'pages') {
		if ($functions->url(1) == 'edit') {
			if ($functions->url(2) != '') {
				echo $pages->editPage();
			} else {
				echo $pages->Pages();
			}
		} elseif ($functions->url(1) == 'new') {
			echo $pages->newPage();
		} elseif ($functions->url(1) == 'delete') {
			echo $pages->Pages();
		} elseif ($functions->url(1) == '') {
			echo $pages->Pages();
		} elseif ($functions->url(1) >= 1) {
			echo $pages->Pages();
		} else {
			echo $pages->error();
		}
	} elseif ($functions->url(0) == 'partials' && $pages->defaults()['user']['access_level'] == 10) {
		if ($functions->url(1) == 'edit') {
			if ($functions->url(2) != '') {
				echo $pages->editPartial();
			} else {
				echo $pages->Partials();
			}
		} elseif ($functions->url(1) == 'new') {
			echo $pages->newPartial();
		} elseif ($functions->url(1) == 'delete') {
			echo $pages->Partials();
		} elseif ($functions->url(1) == '') {
			echo $pages->Partials();
		} else {
			echo $pages->error();
		}
	} elseif ($functions->url(0) == 'users' && $pages->defaults()['user']['access_level'] == 10) {
		if ($functions->url(1) == 'edit') {
			if ($functions->url(2) != '') {
				echo $pages->editUser();
			} else {
				echo $pages->Users();
			}
		} elseif ($functions->url(1) == 'new') {
			echo $pages->newUser();
		} elseif ($functions->url(1) == 'delete') {
			echo $pages->User();
		} elseif ($functions->url(1) == '') {
			echo $pages->Users();
		} else {
			echo $pages->error();
		}
	} elseif ($functions->url(0) == 'user') {
		if ($functions->url(1) == 'profile') {
			echo $pages->userProfile();
		} elseif ($functions->url(1) == '') {
			echo $pages->Users();
		} else {
			echo $pages->error();
		}
	} elseif ($functions->url(0) == 'settings' && $pages->defaults()['user']['access_level'] == 10) {
		if ($functions->url(1) == '') {
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