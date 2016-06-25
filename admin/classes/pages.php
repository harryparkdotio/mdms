<?php

require_once('../backend/Twig/Autoloader.php');
require_once('../backend/frontmatter.php');
require_once('classes/files.php');
require_once('../config/settings.php');
require_once('classes/functions.php');
require_once('classes/Login.php');

class Pages
{
	public function loadUser()
	{
		$user = array('username' => $_SESSION['user_name'], 'email' => $_SESSION['user_email'], 'access_level' => $_SESSION['access_level'], 'loggedin' => $_SESSION['user_login_status']);
		return $user;
	}

	public function loadValues()
	{
		$url = new URL();
		if ($_SESSION['user_login_status'] == 1) {
			$loggedin = true;
		} else {
			$loggedin = false;
		}

		if ($_SESSION['access_level'] == 10) {
			$adminstatus = true;
		} else {
			$adminstatus = false;
		}

		$values = array('base' => $url->homedir(), 'user' => $this->loadUser(), 'urldepth' => $url->urldepth(), 'loggedin' => $loggedin, 'admin' => $adminstatus);
		return $values;
	}

	public function login()
	{
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		$login = new Login();
		$values = array('errors' => $login->errors, 'messages' => $login->messages,) + $this->loadValues();
		return $twig->render('login.html', $values);
	}

	public function Home()
	{
		$settings = new Settings();
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);

		$values = array('currenttheme' => $settings->activeTheme, ) + $this->loadValues();
		return $twig->render('home.html', $values);
	}

	public function Pages()
	{
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		$twig->getExtension('core')->setTimezone('Australia/Sydney');

		$url = new URL();
		$filehandling = new File();

		$filelist = $filehandling->listDirectory('../content/');
		date_default_timezone_set('Australia/Sydney');
		$pages = array();

		foreach ($filelist as &$value) {
			$file = '../content/' . $value;
			$page = str_replace('../content/', '', str_replace('.md', '', str_replace('/index.md', '/', str_replace('../content/index.md', '/', $file))));
			$lastedit = date('D d M y \- g:i a', filemtime($file));
			$link = str_replace('.md', '', str_replace('../content/', '', $file));
			$functions = '<a target="_blank" href="/cms/flatfile/'.$link.'">View</a> '.'<a href="pages/edit/'.urlencode($value).'">Edit</a> '.'<a href="pages/delete/'.urlencode($value).'">Delete</a>';
			$yaml = new FrontMatter($file);
			if ($yaml->fetch('title')) {
				$pagetitle = $yaml->fetch('title');
			}

			if ($yaml->fetch('childpages')) {
				$childpages = explode(' ', $yaml->fetch('childpages'));
			} else {
				$childpages = '';
			}
			$Page = array('page' => $page, 'path' => $value, 'lastedit' => $lastedit, 'fileedited' => filemtime($file), 'functions' => $functions, 'title' => $pagetitle, 'childpages' => $childpages);
			array_push($pages, $Page);
		}
		$values = array('pages' => $pages, 'errors' => $filehandling->errors, 'messages' => $filehandling->messages, ) + $this->loadValues();
		return $twig->render('pageslist.html', $values);
	}

	public function editPage()
	{
		$filehandling = new File();
		$settings = new Settings();
		$url = new URL();

		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);

		$filename = $url->url(2);
		$file = '../content/' . $url->url(2);
		$link = str_replace('.md', '', str_replace('../content/', '', $file));

		$yaml = new FrontMatter($file);

		$values = array('filename' => $filename, 'link' => $link, 'errors' => $filehandling->errors, 'messages' => $filehandling->messages,) + $this->loadValues();

		if ($settings->editor == 'simple') {
			$title = $yaml->fetch('title');
			$description = $yaml->fetch('description');
			$template = $yaml->fetch('template');
			$content = $yaml->fetch('content');
			$values['title'] = $title;
			$values['description'] = $description;
			$values['template'] = $template;
			$template = 'edit-simple.html';
		} else {
			$template = 'edit-advanced.html';
			$contents = file_get_contents($file);
			$contents = explode( '---', $contents);
			$header = $contents[1];
			$header = rtrim($header);
			$content = $contents[2];
			$content = rtrim($content);
			$values['header'] = $header;
		}

		$values['content'] = $content;

		return $twig->render($template, $values); //edit-simple.html is also option
	}

	public function newPage()
	{
		$url = new URL();
		$filehandling = new File();
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);

		$mdtemplate = file_get_contents('resources/mdtemplate.md');

		$values = array('mdtemplate' => $mdtemplate, 'errors' => $filehandling->errors, 'messages' => $filehandling->messages,) + $this->loadValues();
		return $twig->render('newpage.html', $values);
	}

	public function Users()
	{
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		
		$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

		$sql = "SELECT user_id, user_name, user_email, access_level FROM users";
		$result = $conn->query($sql);

		if ($result->num_rows > 0) {
			$Users = array();
			while($row = $result->fetch_assoc()) {
				$user = array();
				$user['user_id'] = $row['user_id'];
				$user['user_name'] = $row['user_name'];
				$user['access_level'] = $row['access_level'];
				$user['user_email'] = $row['user_email'];
				$user['functions'] = 'edit' . ' ' . 'delete';
				array_push($Users, $user);
			}
		}
		$conn->close();

		$values = array('users' => $Users) + $this->loadValues();
		return $twig->render('userlist.html', $values);
	}

	public function Settings()
	{
		$url = new URL();
		$filehandling = new File();
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		$themesInstalled = scandir('../themes/');
		$themes = array();
		foreach ($themesInstalled as &$value) {
			if (is_dir('../themes/' . $value) && $value != '.' && $value != '..') {
				array_push($themes, $value);
			}
		}

		$values = array('errors' => $filehandling->errors, 'messages' => $filehandling->messages, 'themes' => $themes) + $this->loadValues();
		return $twig->render('settings.html', $values);
	}

	public function Partials()
	{
		$url = new URL();
		$settings = new Settings();
		$filehandling = new File();
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		$currentTheme = $settings->activeTheme;
		$filelist = $filehandling->listDirectory('../themes/' . $currentTheme . '/partials/');
		$partials = array();

		foreach ($filelist as &$value) {
			$Partial = array();
			$file = $value;
			$partial = str_replace('.html', '', str_replace('/index.html', '/', str_replace('_', '', $file)));
			$functions = '<a href="partials/edit/'.urlencode($value).'">Edit</a> '.'<a href="partials/delete/'.urlencode($value).'">Delete</a>';

			$Partial['partial'] = $partial;
			$Partial['functions'] = $functions;
			array_push($partials, $Partial);
		}
		$values = array('partials' => $partials, 'errors' => $filehandling->errors, 'messages' => $filehandling->messages, ) + $this->loadValues();
		return $twig->render('partiallist.html', $values);
	}

	public function editPartial()
	{
		$url = new URL();
		$settings = new Settings();
		$currentTheme = $settings->activeTheme;
		$filehandling = new File();
		$settings = new Settings();

		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);

		$filename = $url->url(2);
		$file = '../themes/' . $currentTheme . '/partials/' . $filename;

		$values = array('filename' => $filename, 'errors' => $filehandling->errors, 'messages' => $filehandling->messages,) + $this->loadValues();

		$template = 'edit-partial.html';
		$contents = file_get_contents($file);

		$values['content'] = $contents;
		return $twig->render($template, $values); //edit-simple.html is also option
	}

	public function userProfile()
	{
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		$login = new Login();
		$values = array('errors' => $login->errors, 'messages' => $login->messages,) + $this->loadValues();
		return $twig->render('profile.html', $values);
	}

	public function error()
	{
		return '404, page not found.';
	}
}