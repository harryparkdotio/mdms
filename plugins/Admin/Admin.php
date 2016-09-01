<?php

/**
 * @package mdms - markdown management system
 * @subpackage Admin - an awesome plugin for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0.1
 */

// ESCAPE ALL $_POST REQUESTS
// htmlspecialchars( , ENT_QUOTES);

require_once('plugins/Plugins.php');

class Admin extends Plugins
{
	public $enabled = true;

	protected $url;
	protected $urlexp;

	protected $pages;

	public $config;

	protected $template;
	protected $values;

	private $login;
	private $users;

	public $notifications = array();

	public function onConfigLoaded(&$config) {
		$this->config = $config;
	}

	public function onRequestUrl(&$url)
	{
		$this->url = $url;
		$this->urlexp = explode('/', $this->url);
	}

	public function override()
	{
		if ($this->urlexp[0] == 'admin') {
			return true;
		} else {
			return false;
		}
	}

	public function on404error(&$error)
	{
		if ($this->override() == true) {
			$error = 2;
			$config['Navigation.enabled'] = false;
			if (session_status() == PHP_SESSION_NONE) {
				session_start();
			}
			$this->users = include('plugins/Admin/config/users.php');
			if (!isset($_SESSION['logged_in'])) {
				$_SESSION['logged_in'] = null;
			}

			$pagefunction = $this->getPage($this->url);

			$this->template = $this->$pagefunction()[0];
			$this->values = $this->$pagefunction()[1];
		}
	}

	public function beforeRender(array &$values, &$template, &$templateDir)
	{
		if ($this->override() == true) {
			$urldepth = str_repeat('../', substr_count($this->url, '/'));
			$values = $this->values;
			$template = $this->template . '.html';
			$templateDir = 'plugins/Admin/templates/';
		}
	}

	public function getPage($url)
	{
		$url = explode('/', rtrim($url, '/') . '/');
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
		if (!isset($_SESSION['logged_in'])) {
			$_SESSION['logged_in'] = null;
		}
		if ($_SESSION['logged_in'] === true) {
			if ($url[1] == '') {
				return 'Home';
			} elseif ($url[1] == 'assets') {
				return 'assets';
			} elseif ($url[1] == 'logout') {
				return 'logout';
			} elseif ($url[1] == 'pages') {
				if ($url[2] == 'edit') {
					if ($url[3] != '') {
						return 'editPage';
					} else {
						return 'Pages';
					}
				} elseif ($url[2] == 'new') {
					return 'newPage';
				} elseif ($url[2] == 'delete' || $url[2] == '' || $url[2] >= 1) {
					return 'Pages';
				} else {
					return 'error';
				}
			} elseif ($url[1] == 'partials' && $_SESSION['access_level'] == 10) {
				if ($url[2] == 'edit') {
					if ($url[3] != '') {
						return 'editPartial';
					} else {
						return 'Partials';
					}
				} elseif ($url[2] == 'new') {
					return 'newPartial';
				} elseif ($url[2] == 'delete' || $url[2] == '') {
					return 'Partials';
				} else {
					return 'error';
				}
			} elseif ($url[1] == 'users' && $_SESSION['access_level'] == 10) {
				if ($url[2] == 'edit') {
					if ($url[3] != '') {
						return 'editUser';
					} else {
						return 'Users';
					}
				} elseif ($url[2] == 'new') {
					return 'newUser';
				} elseif ($url[2] == 'delete' || $url[2] == '') {
					return 'Users';
				} else {
					return 'error';
				}
			} elseif ($url[1] == 'user') {
				if ($url[2] == 'profile') {
					return 'userProfile';
				} elseif ($url[2] == '') {
					return 'Users';
				} else {
					return 'error';
				}
			} elseif ($url[1] == 'settings' && $_SESSION['access_level'] == 10) {
				if ($url[2] == '') {
					return 'Settings';
				} else {
					return 'error';
				}
			} else {
				return 'error';
			}
		} else {
			return 'login';
		}
	}

	public function render($template, $values = array())
	{
		return array($template, $values + $this->defaults());
	}

	public function defaults()
	{
		$loggedin = false;
		if (isset($_SESSION['logged_in'])) {
			if ($_SESSION['logged_in'] === true) {
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
			$access_level = $this->users[$key]['access_level'];
			$user_id = $this->users[$key]['id'];
			array_push($Users, array('username' => $key, 'id' => $user_id, 'access_level' => $access_level, ));
		}

		return [
			'base' => $this->homedir(),
			'urldepth' => $this->urldepth(),
			'info' => ['users' => count($this->users), 'pages' => $this->numDir('content'), 'theme' => $this->config['theme']],
			'user' => [
				'username' => $_SESSION['user_name'],
				'access_level' => $_SESSION['access_level'],
				'id' => $_SESSION['user_id'],
				'loggedin' => $loggedin,
				'admin' => $admin,
			],
			'users' => $Users,
			'notifications' => $this->Notifications(),
		];
	}

	public function login()
	{
		if (isset($_POST['login'])) {
			$_SESSION['logged_in'] = null;
			$_SESSION['user_name'] = null;
			$_SESSION['access_level'] = null;
			$_SESSION['user_id'] = null;
			$post = array($_POST['user_name'], $_POST['user_password']);
			$_SESSION['logged_in'] = false;
			if (array_key_exists($post[0], $this->users)) {
				if (password_verify($post[1], $this->users[$post[0]]['password'])) {
					$_SESSION['logged_in'] = true;
					$_SESSION['user_name'] = $post[0];
					$_SESSION['access_level'] = $this->users[$post[0]]['access_level'];
					$_SESSION['user_id'] = $this->users[$post[0]]['id'];
				}
			}
			header ('Location: ' . $_SERVER['REQUEST_URI']);
		}
		return $this->render('login');
	}

	public function logout()
	{
		$_SESSION['logged_in'] = false;
		return $this->render('login');
	}

	public function Home()
	{
		return $this->render('home');
	}

	public function Pages()
	{
		if (isset($_POST["delpage"])) {
			$f = $_POST["delpage"];
			$f = rtrim($f, '.md') . '.md';
			if (file_exists('content/' . $f)) {
				unlink('content/' . $f);
				$this->cleanDir('content/');
			}

			if (!file_exists('content/' . $f)) {
				$this->notifications = ['success' => '<b>' . $f . '</b> Successfully Deleted.'];
			}
		} elseif (isset($_POST["save"])) {
			if (isset($this->config['Admin.editor'])) {
				if ($this->config['Admin.editor'] == 'simple') {
					$title = $_POST['title'];
					$description = $_POST['description'];
					$template = $_POST['template'];
					$content = $_POST['content'];
					$page = '---' . "\n" . 'title: ' . $title . "\n" . 'description: ' . $description . "\n" . 'template: ' . $template . "\n" . '---' . "\n" . $content;
				} else {
					$header = $_POST['header'];
					$content = $_POST['content'];
					$page = '---' . "\n" . $header . "\n" . '---' . "\n" . $content;
				}
			} else {
				$header = $_POST['header'];
				$content = $_POST['content'];
				$page = '---' . "\n" . $header . "\n" . '---' . "\n" . $content;
			}

			$oldfile = 'content/' . $_POST['oldfilename'];
			$file = 'content/' . $_POST['filename'];
			$file = rtrim($file, '.md') . '.md';
			$oldfile = rtrim($oldfile, '.md') . '.md';
			$directory = substr($file, 0, strrpos( $file, '/'));
			if (!is_dir($directory)) {
				mkdir($directory, 0777, true);
			}

			if ($file == $oldfile || file_exists($file) == false) {
				file_put_contents($file, $page);
			} else {
				file_put_contents($oldfile, $page);
			}

			if (file_get_contents($file) == $page) {
				$f = str_replace('content/', '', $file);
				$f = str_replace('.md', '', $f);
				$this->notifications = ['success' => 'Page Saved as <b>' . $f . '</b> Successful.'];
				if ($file !== $oldfile) {
					unlink($oldfile);
				}
			} else {
				$f = str_replace('content/', '', $file);
				$f = str_replace('.md', '', $f);
				$o = str_replace('content/', '', $oldfile);
				$o = str_replace('.md', '', $o);
				$this->notifications = ['danger' => 'Could not save as file as <b>' . $f . '</b>, saved contents as <b>' . $o . '</b> instead.'];
			}
		} elseif (isset($_POST["newfilename"])) {
			$file = 'content/' . str_replace('.md', '', $_POST['newfilename']) . '.md';
			$file = rtrim($file, '.md') . '.md';
			if ($file !== 'content/.md') {
				$content = $_POST['content'];
				$directory = substr($file, 0, strrpos( $file, '/'));

				if (!is_dir($directory)) {
					mkdir($directory, 0777, true);
				}

				if (!file_exists($file)) {
					file_put_contents($file, $content);
				}

				if (file_get_contents($file) == $content) {
					$file = str_replace('.md', '', str_replace('content/', '', $file));
					$this->notifications = ['success' => 'Page Created Successfully as <b>' . $file . '</b>'];
				} else {
					$this->notifications = ['danger' => 'Page could not be saved'];
				}
			}
			else {
				$this->notifications = ['danger' => 'Filename was blank'];
			}
		}

		$filelist = $this->listDirectory('content/');
		date_default_timezone_set('Australia/Sydney');
		$pages = array();

		foreach ($filelist as &$value) {
			$file = 'content/' . $value;
			$file = rtrim($file, '.md') . '.md';
			$edit = str_replace('.md', '', $value);
			$page = str_replace('content/', '', str_replace('.md', '', str_replace('/index.md', '/', str_replace('content/index.md', '/', $file))));
			$lastedit = date('d M y \- g:ia', filemtime($file));
			$lasteditFormatted = $this->time_elapsed('@' . filemtime($file));
			$link = str_replace('.md', '', str_replace('content/', '', $file));
			$functions = '<a class="btn btn-default btn-xs" target="_blank" href="' . $this->homedir() . $link.'"><i class="fa fa-eye" aria-hidden="true"></i> View</a> '.'<a class="btn btn-default btn-xs" href="pages/edit/' . $edit . '"><i class="fa fa-pencil" aria-hidden="true"></i> Edit</a> '.'<a class="btn btn-default btn-xs" style="cursor:pointer" onclick="Delete(' . '\'' . $edit . '\'' . ')"><i class="fa fa-times" aria-hidden="true"></i> Delete</a>'; //fix basepathing
			$yaml = new FrontMatter($file);

			if ($yaml->keyExists('children')) {
				$childpages = $yaml->fetch('children');
				$childpages = explode(', ', $childpages);
				if (!is_array($childpages)) {
					$childpages = explode(' ', $childpages);
				}
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
			$Page = array('page' => $page, 'state' => $state, 'state_style' => $state_style, 'path' => $value, 'lastedit' => $lastedit, 'lastedit_formatted' => $lasteditFormatted, 'fileedited' => filemtime($file), 'functions' => $functions, 'title' => $pagetitle, 'children' => $childpages);
			array_push($pages, $Page);
		}

		$this->sortCustom($pages, 'page', 'asc');

		$offset = 0;
		if ($this->url(2) == null) {
			$page = 1;
		} else {
			$page = $this->url(2);
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

		$values = array('pages' => $paginatedPages, 'pagination' => ['total_pages' => $total_pages, 'current_page' => $page], );
		return $this->render('pageslist', $values);
	}

	public function editPage()
	{
		$slashes = substr_count(str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']), '/');
		$filenamearr = array();
		foreach (range(3, $slashes) as $depth) {
			$filename = $this->url($depth);
			array_push($filenamearr, $filename);
		}
		$filename = implode('/', $filenamearr);
		$file = 'content/' . $filename . '.md';
		$file = rtrim($file, '.md') . '.md';
		$link = str_replace('.md', '', str_replace('content/', '', $file));

		$yaml = new FrontMatter($file);

		$values = array('filename' => $filename, 'link' => $link, );

		if (isset($this->config['Admin.editor'])) {
			if ($this->config['Admin.editor'] == 'simple') {
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
		return $this->render('newpage', array('mdtemplate' => file_get_contents('plugins/Admin/resources/mdtemplate.md')));
	}

	public function assets()
	{
		$assets = $this->listDirectory('assets/');
		$return = array();
		foreach ($assets as $key => $val) {
			$asset = array();
			$file = 'assets/'.$val;
			$pathinfo = pathinfo($file);
			$asset['name'] = $pathinfo['filename'].'.'.$pathinfo['extension'];
			$asset['dir'] = $pathinfo['dirname'];
			$asset['ext'] = $pathinfo['extension'];
			$asset['type'] = mime_content_type($file);
			$asset['size'] = $this->human_filesize(filesize($file)) . 'B';
			$asset['date'] = $this->time_elapsed('@' . filemtime($file));
			array_push($return, $asset);
		}
		return $this->render('assets', array('assets' => $return));
	}

	public function Users()
	{
		return $this->render('userlist');
	}

	public function newUser()
	{
		$return = '';
		if (isset($_POST['user_name'])) {
			$formData = array($_POST['user_name'], $_POST['user_pass'], $_POST['user_pass_repeat'], $_POST['access_level']);
			if (array_key_exists($formData[0], $this->users)) {
				$return = '<p><code>USERNAME ALREADY EXISTS!</code></p>';
			} elseif ($formData[0] == null && $formData[1] == null && $formData[2] == null) {
				$return = '<p><code>USERNAME & PASSWORD CANNOT BE BLANK!</code></p>';
			} elseif ($formData[0] == null) {
				$return = '<p><code>USERNAME CANNOT BE BLANK!</code></p>';
			} elseif ($formData[1] == null && $formData[2] == null) {
				$return = '<p><code>PASSWORD CANNOT BE BLANK!</code></p>';
			} else {
				if ($formData[1] == $formData[2] && $formData[0] != '' && $formData[1] !== '') {
					$hash = password_hash($formData[1], PASSWORD_DEFAULT);
					$username = $formData[0];
					$access_level = $formData[3];
					$user_id = count($this->users);
					$return = "<p>Paste this into the 'users.php' file storing all user info.<br><code>'" . $username . "' => ['id' => " . $user_id . ", 'password' => '" . $hash . "', 'access_level' => " . $access_level . "],</code></p>";
				} else {
					$return = '<p><code>PASSWORDS DO NOT MATCH!</code></p>';
				}
			}
		}
		return $this->render('newuser', array('newuser' => $return));
	}

	public function Settings()
	{
		$themesInstalled = scandir('themes/');
		$themes = array();
		foreach ($themesInstalled as &$value) {
			if (is_dir('themes/' . $value) && $value != '.' && $value != '..') {
				array_push($themes, $value);
			}
		}
		return $this->render('settings', array('themes' => $themes));
	}

	public function Partials()
	{
		if (isset($_POST["savepartial"])) {
			$content = $_POST['content'];
			$oldfile = 'themes/' . $this->config['theme'] . '/partials/' . $_POST['oldfilename'];
			$file = 'themes/' . $this->config['theme'] . '/partials/' . $_POST['filename'];
			$directory = substr($file, 0, strrpos( $file, '/'));
			$file = str_replace('.html', '', $file) . '.html';
			$file = rtrim($file, '.html') . '.html';
			if (!is_dir($directory)) {
				mkdir($directory, 0777, true);
			}

			if ($file == $oldfile || !file_exists($file)) {
				file_put_contents($file, $content);
			} else {
				file_put_contents($oldfile, $content);
			}

			if (file_get_contents($file) == $content) {
				$this->notifications = ['success' => 'Page Saved as <b>' . $file . '</b> Successfully.'];
				if ($file !== $oldfile) {
					unlink($oldfile);
				}
			} else {
				$this->notifications = ['danger' => 'Could not save as file as <b>' . $file . '</b>, saved contents as <b>' . $oldfile . '</b> instead.'];
			}
		}

		$filelist = $this->listDirectory('themes/' . $this->config['theme'] . '/partials/');

		$partials = array();
		foreach ($filelist as &$value) {
			$Partial = array();
			$f = rtrim($value, '.html');
			$Partial['partial'] = str_replace('.html', '', str_replace('/index.html', '/', str_replace('_', '', $value)));
			$Partial['functions'] = '<a href="partials/edit/'.$f.'">Edit</a> '.'<a href="partials/delete/'.$f.'">Delete</a>';
			array_push($partials, $Partial);
		}
		return $this->render('partiallist', array('partials' => $partials));
	}

	public function editPartial()
	{
		$filename = $this->url(3);
		$file = 'themes/' . $this->config['theme'] . '/partials/' . $filename;
		$file = rtrim($file, '.html') . '.html';
		return $this->render('edit-partial', array('filename' => $filename, 'content' => file_get_contents($file)));
	}

	public function userProfile()
	{
		return $this->render('profile');
	}

	public function error()
	{
		return $this->render('404');
	}

	private function sortCustom(&$array, $key, $dir='asc')
	{
		$sorter = array();
		$rebuilt = array();
		reset($array);

		foreach($array as $ii => $value) {
			$sorter[$ii] = $value[$key];
		}

		if ($dir == 'asc') asort($sorter);
		if ($dir == 'desc') arsort($sorter);

		foreach($sorter as $ii => $value) {
			$rebuilt[$ii] = $array[$ii];
		}

		$array = $rebuilt;
	}

	// FUNCTIONS

	public function Notifications()
	{
		$notifications = array();
		foreach ($this->notifications as $key => $val) {
			$key = 'alert-' . $key;
			$html = '
				<div class="alert ' . $key . '">
					<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
					' . $val . '
				</div>
			';
			array_push($notifications, $html);
		}
		return $notifications;
	}

	public function homedir()
	{
		return str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
	}

	public function urldepth()
	{
		return str_repeat('../', substr_count(str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']), '/'));
	}

	public function url($a)
	{
		$requestURI = explode('/', str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']));
		if (isset($requestURI[$a])) {
			return $requestURI[$a];
		} else {
			return '';
		}
	}

	public function listDirectory($dir, $prefix = '') {
		$dir = rtrim($dir, '\\/');
		$result = array();

		foreach (scandir($dir) as $f) {
			if ($f !== '.' and $f !== '..') {
				if (is_dir("$dir/$f")) {
					$result = array_merge($result, $this->listDirectory("$dir/$f", "$prefix$f/"));
				} else {
					$result[] = $prefix.$f;
				}
			}
		}
		return $result;
	}

	public function numDir($dir)
	{
		return count($this->listDirectory(rtrim($dir, '/') . '/'));
	}

	private function cleanDir($path)
	{
		$empty = true;
		foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
		{
			$empty &= is_dir($file) && $this->cleanDir($file);
		}
		return $empty && rmdir($path);
	}

	function time_elapsed($datetime, $full = false) {
		$now = new DateTime();
		$ago = new DateTime($datetime);
		$diff = $now->diff($ago);

		$diff->w = floor($diff->d / 7);
		$diff->d -= $diff->w * 7;

		$string = [
			'y' => 'year', 'm' => 'month', 'w' => 'week', 'd' => 'day', 'h' => 'hour', 'i' => 'minute', 's' => 'second',
		];

		foreach ($string as $k => &$v) {
			if ($diff->$k) {
				$v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
			} else {
				unset($string[$k]);
			}
		}

		if (!$full) $string = array_slice($string, 0, 1);
		return $string ? implode(', ', $string) . ' ago' : 'just now';
	}

	public function human_filesize($bytes, $decimals = 2) {
		$sz = 'BKMGTP';
		$factor = floor((strlen($bytes) - 1) / 3);
		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
	}

	public function post($val)
	{
		return htmlspecialchars($_POST[$val], ENT_QUOTES);
	}
}