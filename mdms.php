<?php

/**
 *
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0.1
 * @package mdms - markdown management system
 */

require_once('backend/parsedown.php');
require_once('backend/frontmatter.php');
require_once('backend/Twig/Autoloader.php');

class mdms
{
	// Loaded Packages
	public $markdown;
	public $yaml;

	// Config + Plugins Storage
	public $config;
	public $plugins;

	// Page Variables
	public $page;
	public $pageError = 0; // 0 = none, 1 = 404, 2 = plugin override. Allows plugins to override the 404 page.
	public $template;
	public $values;
	public $theme;
	public $headers;
	public $content;
	public $rerend = true;

	// URL + Page Variables
	public $base;
	public $urldepth;
	public $url;
	public $RequestUrl;

	public function __construct()
	{
		$this->debug(true); // enable/disable debugging
		$this->loadPlugins();
		$this->triggerEvent('onPluginsLoaded', array(&$this->plugins));
		$this->loadConfig();
		$this->triggerEvent('onConfigLoaded', array(&$this->config));
		$this->getPage();
	}

	public function debug($enable)
	{
		if ($enable == true) {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		}
	}

	protected function loadPlugins()
	{
		$this->plugins = array();
		$pluginFiles = $this->getFiles('plugins/', '.php');
		foreach ($pluginFiles as $pluginFile) {
			require_once($pluginFile);
			$className = preg_replace('/^[0-9]+-/', '', basename($pluginFile, '.php'));
			if (class_exists($className)) {
				$plugin = new $className($this);
				$className = get_class($plugin);
				$this->plugins[$className] = $plugin;
			}
		}
	}

	public function getPlugin($pluginName = null)
	{
		if ($pluginName != null) {
			if (isset($this->plugins[$pluginName])) {
				return $this->plugins[$pluginName];
			}
			throw new RuntimeException("Missing plugin '" . $pluginName . "'");
		} else {
			return $this->plugins;
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
		$this->cleanContentDirectory($directory);
		$directory = rtrim($directory, '/');
		$result = array();
		$files = scandir($directory, 0);
		$fileExtensionLength = strlen($fileExtension);
		if ($files !== false) {
			foreach ($files as $file) {
				if ((substr($file, 0, 1) === '.') || in_array(substr($file, -1), array('~', '#'))) {
					continue;
				}
				if (is_dir($directory . '/' . $file)) {
					$result = array_merge($result, $this->getFiles($directory . '/' . $file, $fileExtension));
				} elseif (empty($fileExtension) || (substr($file, -$fileExtensionLength) === $fileExtension)) {
					$result[] = $directory . '/' . $file;
				}
			}
		}
		return $result;
	}

	private function cleanContentDirectory($path)
	{
		$empty = true;
		foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file) {
			$empty &= is_dir($file) && $this->cleanContentDirectory($file);
		}
		return $empty && rmdir($path);
	}

	public function getPage()
	{
		$this->base = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$this->url = str_replace($this->base, '', $_SERVER['REQUEST_URI']);
		$this->RequestUrl = str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']);
		$this->triggerEvent('onRequestUrl', array(&$this->RequestUrl));
		$this->page = 'content/' . str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']) . '.md';
		$this->urldepth = str_repeat('../', substr_count(str_replace(str_replace('index.php', '', $_SERVER['SCRIPT_NAME']), '', $_SERVER['REQUEST_URI']), '/'));
		$this->pageExists();
	}

	public function pageExists()
	{
		if (!file_exists($this->page)) {
			if (substr($this->page, -4) !== '/.md') {
				$this->page = str_replace('.md', '/index.md', $this->page);
			}
			if (!file_exists($this->page)) {
				$this->page = str_replace('/.md', '/index.md', $this->page);
				if (!file_exists($this->page)) {
					$this->page = str_replace('/index.md', '.md', $this->page);
					if (!file_exists($this->page)) {
						$this->page = 'content/404.md'; // returns if no page found
						$this->pageError = 1;
						$this->triggerEvent('on404error', array(&$this->pageError));
					}
				}
			}
		}
		$this->triggerEvent('getPage', array(&$this->page));
		if ($this->pageError < 2) {
			$this->pageStatus();
		} else {
			$this->defaultValues();
		}
	}

	public function pageStatus()
	{
		$this->yaml = new FrontMatter($this->page);
		if ($this->yaml->keyExists('status')) {
			$state = $this->yaml->fetch('status');
			if ($this->page !== 'content/404.md') {
				$state = strtolower($state);
				if ($state == 'archived' || $state == 'draft') {
					$this->page = 'content/404.md';
				}
			}
		}
		$this->yaml = new FrontMatter($this->page);
		$this->defaultValues();
	}

	public function defaultValues()
	{
		$this->values = array(
			'config' => $this->getConfig(),
			'urldepth' => $this->urldepth,
			'base_url' => $this->getConfig('base_url'),
			'base' => $this->base,
			'year' => date("Y"),
			'pagelinks' => $this->getConfig('nav'),
			'nav_title' => $this->getConfig('nav_title'),
		);
		if ($this->pageError < 2) {
			$this->PageValues();
		} else {
			$this->Render();
		}
	}

	public function PageValues()
	{
		$page = array();
		foreach ($this->yaml->values as $value) {
			$page[$value] = $this->yaml->fetch($value);
		}
		$this->markdown = new Parsedown();
		$page['content'] = $this->markdown->text($this->yaml->fetch('content'));
		$this->content = $this->markdown->text($this->yaml->fetch('content'));
		$this->values['page'] = $page;
		$this->headers = $page;
		$this->Children();
	}

	public function Children()
	{
		if ($this->yaml->keyExists('children')) { // change to children
			unset($this->values['children']);
			$children = $this->yaml->fetch('children');
			$children = explode(', ', $children);
			if (!is_array($children)) {
				$children = explode(' ', $children);
			}
			$child = array();
			$count = 0;
			foreach ($children as &$value) {
				$count += 1;
				$page = array();
				$importChild = new FrontMatter('content/' . $value . '.md');
				foreach ($importChild->values as $valuez) {
					$page[$valuez] = $importChild->fetch($valuez);
				}
				$page['content'] = $this->markdown->text($importChild->fetch('content'));
				$child[$count] = $page;
			}
			$this->values['child'] = $child;
		}
		$this->theme();
	}

	public function theme()
	{
		$this->theme = $this->getConfig('theme');
		$themeyaml = new FrontMatter('themes/' . $this->theme . '/theme.yaml');

		$theme_info = array();
		foreach ($themeyaml->values as $value) {
			$theme_info[$value] = $themeyaml->fetch($value);
		}
		$this->theme = str_replace(' ', '%20', $this->theme);
		$theme_info['dir'] = 'themes/' . $this->theme . '/assets/';
		$this->values['theme'] = $theme_info;
		$this->template();
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

		if ($this->yaml->keyExists('content-rerender')) {
			$this->rerend = false;
		}

		$this->Render();
	}

	public function Render()
	{
		if (!isset($this->config['timezone'])) {
			$this->config['timezone'] = 'UTC';
		}
		$values = $this->values;
		$template = $this->template;
		$templateDir = 'themes/' . $this->getConfig('theme');
		Twig_Autoloader::register();
		$this->triggerEvent('beforeRender', array(&$values, &$template, &$templateDir));
		$loader = new Twig_Loader_Filesystem($templateDir);
		$twig = new Twig_Environment($loader, array('autoescape' => false, 'cache' => false, 'debug' => false));
		$twig->getExtension('core')->setTimezone($this->getConfig('timezone'));
		$twig_env = new Twig_Environment(new Twig_Loader_String);
		
		if (isset($values['page']['content']) && $this->rerend === true) {
			$twig_env = new Twig_Environment(new Twig_Loader_String);
			$values['page']['content'] = $twig_env->render($values['page']['content'], $values);
		}
		if (isset($values['child'])) {
			$twig_env = new Twig_Environment(new Twig_Loader_String);
			foreach ($values['child'] as $key => $val) {
				$values['child'][$key]['content'] = $twig_env->render($values['child'][$key]['content'], $values);
			}
		}
		echo $twig->render($template, $values);
	}

	public function triggerEvent($eventName, array $params = array())
	{
		if (!empty($this->plugins)) {
			foreach ($this->plugins as $plugin) {
				if (is_a($plugin, 'Plugins')) {
					$params['pluginconfig'] = $this->getConfig();
					$plugin->handleEvent($eventName, $params);
				}
			}
		}
	}
}