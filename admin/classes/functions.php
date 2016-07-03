<?php

class Functions
{
	public $config;
	
	public function loadConfig()
	{
		$config = null;
		if (file_exists('../config/config.php')) {
			require_once('../config/config.php');
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

	public function url($a)
	{
		$url = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$requestURI = explode('/', str_replace($url, '', $_SERVER['REQUEST_URI']));
		if (isset($requestURI[$a])) {
			return $requestURI[$a];
		} else {
			return '';
		}
	}

	public function homedir()
	{
		$base = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		return $base;
	}

	public function urldepth()
	{
		$url = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$url = str_replace($url, '', $_SERVER['REQUEST_URI']);
		$depth = str_repeat('../', substr_count($url, '/'));
		return $depth;
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
}