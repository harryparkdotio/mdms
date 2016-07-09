<?php

require_once('../backend/Twig/Autoloader.php');
require_once('../backend/frontmatter.php');

require_once('files.php');
require_once('functions.php');
require_once('login.php');

class Pages
{
	private $functions;
	private $login;
	private $files;
	private $users;

	public function __construct()
	{
		$this->loadClasses();
		$this->functions->loadConfig();
	}

	public function render($template, $values)
	{
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('templates');
		$twig = new Twig_Environment($loader);
		$twig->getExtension('core')->setTimezone('AEST'); // ADD TIMEZONE FROM SETTINGS
		return $twig->render($template . '.html', $values);
	}

	public function loadClasses()
	{
		$this->functions = new Functions();
		$this->login = new Login();
		$this->files = new Files();
		$this->users = include('config/users.php');
	}

	public function defaults()
	{
		$loggedin = false;
		if (isset($_SESSION['logged_in'])) {
			if ($_SESSION['logged_in'] == 1) {
				$loggedin = true;
			}
		}

		$admin = false;
		if (isset($_SESSION['access_level'])) {
			if ($_SESSION['access_level'] >= 10) {
				$admin = true;
			}
		} else {
			$_SESSION['access_level'] = null;
		}

		if (!isset($_SESSION['user_name'])) {
			$_SESSION['user_name'] = null;
		}
		if (!isset($_SESSION['user_id'])) {
			$_SESSION['user_id'] = null;
		}

		$Users = array();
		foreach ($this->users as $key => $value) {
			$username = $key;
			$access_level = $this->users[$key]['access_level'];
			$user_id = $this->users[$key]['id'];
			array_push($Users, array('username' => $username, 'id' => $user_id, 'access_level' => $access_level, ));
		}

		$numusers = count($this->users);
		$currentTheme = $this->functions->getConfig('theme');

		return [
			'base' => $this->functions->homedir(),
			'urldepth' => $this->functions->urldepth(),
			'info' => ['users' => $numusers, 'pages' => $this->files->numPages(), 'theme' => $currentTheme],
			'user' => [
				'username' => $_SESSION['user_name'],
				'access_level' => $_SESSION['access_level'],
				'id' => $_SESSION['user_id'],
				'loggedin' => $loggedin,
				'admin' => $admin,
			],
			'users' => $Users,
		];
	}

	public function login()
	{
		$values = $this->defaults();
		return $this->render('login', $values);
	}

	public function Home()
	{
		$values = array() + $this->defaults();
		return $this->render('home', $values);
	}

	public function Pages()
	{
		$filelist = $this->files->listDirectory('../content/');
		date_default_timezone_set('Australia/Sydney');
		$pages = array();

		foreach ($filelist as &$value) {
			$file = '../content/' . $value;
			$page = str_replace('../content/', '', str_replace('.md', '', str_replace('/index.md', '/', str_replace('content/index.md', '/', $file))));
			$lastedit = date('d M y \- g:ia', filemtime($file));
			$link = str_replace('.md', '', str_replace('../content/', '', $file));
			$functions = '<a class="btn btn-default btn-xs" target="_blank" href="/cms/flatfile/'.$link.'"><i class="fa fa-eye" aria-hidden="true"></i> View</a> '.'<a class="btn btn-default btn-xs" href="pages/edit/' . $value . '"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a> '.'<a class="btn btn-default btn-xs" style="cursor:pointer" onclick="Delete(' . '\'' . $value . '\'' . ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>'; //fix basepathing
			$yaml = new FrontMatter($file);

			if ($yaml->keyExists('childpages')) {
				$childpages = explode(' ', $yaml->fetch('childpages'));
			} else {
				$childpages = '';
			}

			if ($yaml->keyExists('status')) {
				$state = $yaml->fetch('status');
				if ($state == 'draft') {
					$state_style = 'warning';
				} elseif ($state == 'archived') {
					$state_style = 'default';
				}
			} else {
				$state = 'published';
				$state_style = 'success';
			}

			if ($yaml->keyExists('title')) {
				$pagetitle = $yaml->fetch('title');
				if (strlen($pagetitle) > 18) {
					$pagetitle = substr($yaml->fetch('title'), 0, 18) . '...';
				}
			} else {
				$pagetitle = '';
			}
			$page = str_replace('../', '', $page);
			$Page = array('page' => $page, 'state' => $state, 'state_style' => $state_style, 'path' => $value, 'lastedit' => $lastedit, 'fileedited' => filemtime($file), 'functions' => $functions, 'title' => $pagetitle, 'childpages' => $childpages);
			array_push($pages, $Page);
		}

		$this->sortCustom($pages, 'page', 'asc');

		$offset = 0;
		if ($this->functions->url(1) == null) {
			$page = 1;
		} else {
			$page = $this->functions->url(1);
		}
		if ($page > 1) {
			$offset = $page - 1;
		}

		$paginatedPages = array_chunk($pages, 10);
		$total_pages = count($paginatedPages);
		if (isset($paginatedPages[$offset])) {
			$paginatedPages = $paginatedPages[$offset];
		} else {
			$paginatedPages = [];
		}

		$values = array('pages' => $paginatedPages, 'pagination' => ['total_pages' => $total_pages, 'current_page' => $page], 'errors' => $this->files->errors, 'messages' => $this->files->messages, ) + $this->defaults();
		return $this->render('pageslist', $values);
	}

	public function editPage()
	{
		$slashes = substr_count(str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']), '/');
		$filenamearr = array();
		foreach (range(2, $slashes) as $depth) {
			$filename = $this->functions->url($depth);
			array_push($filenamearr, $filename);
		}
		$filename = implode('/', $filenamearr);
		$file = '../content/' . $filename;
		$link = str_replace('.md', '', str_replace('../content/', '', $file));

		$yaml = new FrontMatter($file);

		$values = array('filename' => $filename, 'link' => $link, 'errors' => $this->files->errors, 'messages' => $this->files->messages,) + $this->defaults();

		if ($this->functions->getConfig('editor') == 'simple') {
			$title = $yaml->fetch('title');
			$description = $yaml->fetch('description');
			$template = $yaml->fetch('template');
			$content = $yaml->fetch('content');
			$values['title'] = $title;
			$values['description'] = $description;
			$values['template'] = $template;
			$template = 'edit-simple';
		} else {
			$template = 'edit-advanced';
			$contents = file_get_contents($file);
			$contents = explode( '---', $contents);
			$header = $contents[1];
			$header = rtrim($header);
			$content = $contents[2];
			$content = rtrim($content);
			$values['header'] = $header;
		}

		$values['content'] = $content;

		return $this->render($template, $values); //edit-simple.html is also option
	}

	public function newPage()
	{
		$mdtemplate = file_get_contents('resources/mdtemplate.md');

		$values = array('mdtemplate' => $mdtemplate, 'errors' => $this->files->errors, 'messages' => $this->files->messages,) + $this->defaults();
		return $this->render('newpage', $values);
	}

	public function Users()
	{
		$values = $this->defaults();
		return $this->render('userlist', $values);
	}

	public function newUser()
	{
		$new = $this->login->register();
		$values = array('newuser' => $new) + $this->defaults();
		return $this->render('newuser', $values);
	}

	public function Settings()
	{
		$themesInstalled = scandir('../themes/');
		$themes = array();
		foreach ($themesInstalled as &$value) {
			if (is_dir('../themes/' . $value) && $value != '.' && $value != '..') {
				array_push($themes, $value);
			}
		}

		$values = array('errors' => $this->files->errors, 'messages' => $this->files->messages, 'themes' => $themes) + $this->defaults();
		return $this->render('settings', $values);
	}

	public function Partials()
	{
		$currentTheme = $this->functions->getConfig('theme');
		$filelist = $this->files->listDirectory('../themes/' . $currentTheme . '/partials/');
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
		$values = array('partials' => $partials, 'errors' => $this->files->errors, 'messages' => $this->files->messages, ) + $this->defaults();
		return $this->render('partiallist', $values);
	}

	public function editPartial()
	{
		$currentTheme = $this->functions->getConfig('theme');

		$filename = $this->functions->url(2);
		$file = '../themes/' . $currentTheme . '/partials/' . $filename;

		$values = array('filename' => $filename, 'errors' => $this->files->errors, 'messages' => $this->files->messages,) + $this->defaults();

		$template = 'edit-partial';
		$contents = file_get_contents($file);

		$values['content'] = $contents;
		return $this->render($template, $values); //edit-simple.html is also option
	}

	public function userProfile()
	{
		$values = array('errors' => $this->login->errors, 'messages' => $this->login->messages,) + $this->defaults();
		return $this->render('profile', $values);
	}

	public function error()
	{
		return '404, page not found.';
	}

	private function sortCustom(&$array, $key, $dir='asc')
	{
		$sorter=array();
		$rebuilt=array();

		//make sure we start at the beginning of $array
		reset($array);

		//loop through the $array and store the $key's value
		foreach($array as $ii => $value) {
		$sorter[$ii]=$value[$key];
		}

		//sort the built array of key values
		if ($dir == 'asc') asort($sorter);
		if ($dir == 'desc') arsort($sorter);

		//build the returning array and add the other values associated with the key
		foreach($sorter as $ii => $value) {
		$rebuilt[$ii]=$array[$ii];
		}

		//assign the rebuilt array to $array
		$array=$rebuilt;
	}
}