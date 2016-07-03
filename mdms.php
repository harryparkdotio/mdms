<?php

/**
 *
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 0.2
 * @package mdms - markdown management system
 *
 */

require_once('backend/parsedown.php');
require_once('backend/frontmatter.php');
require_once('backend/Twig/Autoloader.php');

class mdms
{
	// FUNCTIONS
	public $markdown;
	public $yaml;

	public $config;
	public $plugins;

	// PAGE
	public $page;
	public $template;
	public $values;
	public $theme;
	public $headers;
	public $content;

	// URL
	public $baseurl;
	public $urldepth;
	public $url;
	public $requestedPage;

	public function __construct() // load packages; assign to vars
	{
		$this->debug(true);
		$this->loadPlugins();
		$this->loadConfig();
		$this->run();
	}

	public function run()
	{
		$this->getPlugin('Blog');
		$this->markdown = new Parsedown();
		$this->getPage();
		$this->pageExists();
		$this->pageStatus();
		$this->defaultValues();
		$this->PageValues();
		$this->Children();
		$this->theme();
		$this->template();
		$this->Render();
	}

	public function debug($enable)
	{
		if ($enable == true) {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}
	}

	public function loadConfig()
	{
		$config = null;
		if (file_exists('config/config.php')) {
			require_once('config/config.php');
		}
		$defaults = array(
			'site_title' => 'mdms',
		);

		$this->config = is_array($this->config) ? $this->config : array();
		$this->config += is_array($config) ? $config + $defaults : $defaults;
	}

	public function getConfig($configName = null)
	{
		if ($configName != null) {
			return isset($this->config[$configName]) ? $this->config[$configName] : null;
		} else {
			return $this->config;
		}
	}

	protected function getFiles($directory, $fileExtension = '')
	{
		$directory = rtrim($directory, '/');
		$result = array();
		$files = scandir($directory, 0);
		$fileExtensionLength = strlen($fileExtension);
		if ($files !== false) {
			foreach ($files as $file) {
				// don't show hidden files/dirs starting with a '.' or '..'
				// exclude files ending with a ~ or # (vim/nano backup or emacs backup)
				if ((substr($file, 0, 1) === '.') || in_array(substr($file, -1), array('~', '#'))) {
					continue;
				}
				if (is_dir($directory . '/' . $file)) {
					// get files recursively --> sub dirs
					$result = array_merge($result, $this->getFiles($directory . '/' . $file, $fileExtension, $order));
				} elseif (empty($fileExtension) || (substr($file, -$fileExtensionLength) === $fileExtension)) {
					$result[] = $directory . '/' . $file;
				}
			}
		}
		return $result;
	}

	protected function loadPlugins()
	{
		$this->plugins = array();
		$pluginFiles = $this->getFiles('plugins/', '.php');
		foreach ($pluginFiles as $pluginFile) {
			require_once($pluginFile);
			$className = preg_replace('/^[0-9]+-/', '', basename($pluginFile, '.php'));
			if (class_exists($className)) {
				// class name and file name can differ regarding case sensitivity
				$plugin = new $className($this);
				$className = get_class($plugin);
				$this->plugins[$className] = $plugin;
			}
		}
	}

	public function getPlugin($pluginName)
	{
		if (isset($this->plugins[$pluginName])) {
			return $this->plugins[$pluginName];
		}
		throw new RuntimeException("Missing plugin '" . $pluginName . "'");
	}

	public function getPlugins()
	{
		return $this->plugins;
	}

	public function getPage()
	{
		$this->baseurl = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$this->url = str_replace($this->baseurl, '', $_SERVER['REQUEST_URI']);
		$this->requestedPage = str_replace($this->baseurl, '', $_SERVER['REQUEST_URI']);

		// adds the content folder prefix (content/), adds the markdown file extension (.md)
		$this->page = 'content/' . $this->requestedPage . '.md';
		$this->yaml = new FrontMatter($this->page);
		$this->urldepth = str_repeat('../', substr_count($this->requestedPage, '/'));
	}

	public function pageExists()
	{
		if (!file_exists($this->page)) {
			$this->page = str_replace('/.md', '.md', $this->page);
			if (!file_exists($this->page)) {
				$this->page = str_replace('.md', '/index.md', $this->page);
				if (!file_exists($this->page)) {
					$this->page = 'content/404.md'; // returns if no page found
				}
			}
		}
	}

	public function pageStatus()
	{
		if ($this->yaml->keyExists('status')) {
			$state = $this->yaml->fetch('status');
			if ($state == 'archived' || $state = 'draft') {
				$this->page = 'content/404.md';
			}
			$this->yaml = new FrontMatter($this->page);
		}
	}

	public function defaultValues()
	{
		$this->values = array(
			'urldepth' => $this->urldepth,
			'base' => $this->baseurl,
			'year' => date("Y"),
		);
		$this->values['pagelinks'] = $this->getConfig('nav');
	}

	public function PageValues()
	{
		$this->yaml = new FrontMatter($this->page); // page state
		$page = array();
		foreach ($this->yaml->values as $value) {
			$page[$value] = $this->yaml->fetch($value);
		}
		$page['content'] = $this->markdown->text($this->yaml->fetch('content'));
		$this->content = $this->markdown->text($this->yaml->fetch('content'));
		$this->values['page'] = $page;
		$this->headers = $page;
	}

	public function Children()
	{
		if ($this->yaml->keyExists('childpages')) {
			unset($this->values['childpages']);
			$children = explode(' ', $this->yaml->fetch('childpages'));
			$child = array();
			foreach ($children as &$value) {
				$page = array();
				$importChild = new FrontMatter('content/' . $value . '.md');
				foreach ($importChild->values as $valuez) {
					$page[$valuez] = $this->yaml->fetch($valuez);
				}
				$page['content'] = $this->markdown->text($importChild->fetch('content'));
				$child[$value] = $page;
			}
			$this->values['child'] = $child;
		}
	}

	public function theme()
	{
		$this->theme = $this->getConfig('theme');
		$this->yaml = new FrontMatter('themes/' . $this->theme . '/theme.yaml');

		$theme_info = array();
		foreach ($this->yaml->values as $value) {
			$theme_info[$value] = $this->yaml->fetch($value);
		}

		$theme_info['dir'] = 'themes/' . $this->theme . '/assets/';
		$this->values['theme'] = $theme_info;
	}

	public function template()
	{
		if ($this->yaml->keyExists('template')) {
			$this->template = $this->yaml->fetch('template') . '.html';
			if (file_exists('themes/' . $this->theme . '/templates/' . $this->template)) {
				$this->template = '/templates/' . $this->template;
			} else {
				$this->template = '/templates/index.html';
			}
		} else {
			$this->template = '/templates/index.html';
		}
	}

	public function Render()
	{
		Twig_Autoloader::register();
		$loader = new Twig_Loader_Filesystem('themes/' . $this->getConfig('theme'));
		$twig = new Twig_Environment($loader, $this->getConfig('twig_config'));
		$twig->addExtension(new Twig_Extension_Debug());
		echo $twig->render($this->template, $this->values);
	}
}