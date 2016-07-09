<?php

/**
 * @package mdms - markdown management system
 * @subpackage Blog - a plugin for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */

require_once('plugins/Plugins.php');

require_once('backend/parsedown.php');
require_once('backend/frontmatter.php');

class Blog extends Plugins
{
	protected $enabled = true;

	protected $url;
	protected $urlExploded;

	protected $Posts = array();

	public function onRequestUrl(&$url)
	{
		$this->url = $url;
		$this->urlExploded = explode('/', $this->url);
	}

	public function override()
	{
		if ($this->url == 'blog') {
			return true;
		} else {
			return false;
		}
	}

	public function getPage()
	{
		if ($this->urlExploded[0] == 'blog') {
			return 'Hola';
		}
	}

	public function on404error(&$error)
	{
		if ($this->override() == true) {
			$error = 2;
		}
	}

	public function blogDir()
	{
		if (!file_exists('content/blog')) {
			mkdir('content/blog');
		}
		foreach ($this->listDirectory('content/blog') as $value) {
			$filename = str_replace('.md', '', $value);
			$this->Posts[$filename] = $this->parsePost('content/blog/' . $value, $filename);
		}
	}

	public function parsePost($post, $filename)
	{
		$yaml = new FrontMatter($post);
		$page = array();
		foreach ($yaml->values as $value) {
			$page[$value] = $yaml->fetch($value);
		}

		$markdown = new Parsedown();
		$content = $markdown->text($yaml->fetch('content'));
		$page['content'] = $content;
		$page['preview'] = substr($content, 0, 240);
		$page['link'] = $filename;
		return $page;
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

	public function beforeRender(array &$values, &$template, &$templateDir)
	{
		if ($this->override() == true) {
			$this->blogDir();
			$urldepth = str_repeat('../', substr_count($this->url, '/'));
			$values = $values + array('posts' => $this->Posts, 'urldepth' => $urldepth,);
			$template = 'templates/posts.html';
			$templateDir = 'plugins/Blog/';
		}
	}
}