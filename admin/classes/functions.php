<?php

class URL
{
	public function url($a)
	{
		$url = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
		$requestURI = explode('/', str_replace($url, '', $_SERVER['REQUEST_URI']));
		return $requestURI[$a];
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
		$baseurl = str_repeat('../', substr_count($url, '/'));
		return $baseurl;
	}
}