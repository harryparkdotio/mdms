<?php

require_once('functions.php');

class Files
{
	/**
	 * @var array Collection of error messages
	 */
	public $errors = array();
	/**
	 * @var array Collection of error messages
	 */
	public $messages = array();

	public function __construct()
	{
		$functions = new Functions();
		if (isset($_POST["save"])) {
			$this->editPage();
		} elseif ($functions->url(0) == 'pages' && $functions->url(1) == 'delete' && $functions->url(2) != '') {
			$this->deletePage($functions->url(2));
		} elseif (isset($_POST["newfilename"])) {
			$this->newPage();
		} elseif (isset($_POST["savepartial"])) {
			$this->editPartial();
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

	public function numPages()
	{
		$num = 0;
		$arr = $this->listDirectory('../content/');
		$num = count($arr);
		return $num;
	}

	private function newPage()
	{
		$file = '../content/' . str_replace('.md', '', $_POST['newfilename']) . '.md';
		if ($file !== '../content/.md') {
			$content = $_POST['content'];
			$directory = substr($file, 0, strrpos( $file, '/'));

			if (!is_dir($directory)) {
				mkdir($directory, 0777, true);
			}

			if (!file_exists($file)) {
				file_put_contents($file, $content);
			}

			if (file_get_contents($file) == $content) {
				$this->messages[] = 'Page Created Successfully as <b>' . $file . '</b>.' ;
			} else {
				$this->errors[] = 'Page could not be saved.';
			}
		}
		else {
			$this->errors[] = 'File name was empty';
		}
	}

	private function editPage()
	{
		$settings = include('config/settings.php');

		if ($settings['editor'] == 'simple') {
			$title = $_POST['title'];
			$description = $_POST['description'];
			$template = $_POST['template'];
			$content = $_POST['content'];
			$page = '---' . "\n" . 'title: ' . $title . "\n" . 'description: ' . $description . "\n" . 'template: ' . $template . '---' . "\n" . $content;
		} else {
			$header = $_POST['header'];
			$content = $_POST['content'];
			$page = '---' . "\n" . $header . "\n" . '---' . "\n" . $content;
		}

		$oldfile = '../content/' . $_POST['oldfilename'];
		$file = '../content/' . $_POST['filename'];
		$directory = substr($file, 0, strrpos( $file, '/'));
		$file = str_replace('.md', '', $file) . '.md';
		if (!is_dir($directory)) {
			mkdir($directory, 0777, true);
		}

		if ($file == $oldfile || file_exists($file) == false) {
			file_put_contents($file, $page);
		} else {
			file_put_contents($oldfile, $page);
		}

		if (file_get_contents($file) == $page) {
			$this->messages[] = 'Page Saved as <b>' . $file . '</b> Successful.';
			if ($file !== $oldfile) {
				unlink($oldfile);
			}
		} else {
			$this->errors[] = 'Could not save as file as <b>' . $file . '</b>, saved contents as <b>' . $oldfile . '</b> instead.';
		}
	}

	private function deletePage($file)
	{
		if (file_exists('../content/' . $file)) {
			unlink('../content/' . $file);
			$this->cleanContentDirectory('../content/');
		}

		if (!file_exists('../content/' . $file)) {
			$this->messages[] = '<b>' . $file . '</b> Successfully Deleted.';
		}
	}

	private function cleanContentDirectory($path)
	{
		$empty = true;
		foreach (glob($path.DIRECTORY_SEPARATOR."*") as $file)
		{
			$empty &= is_dir($file) && $this->cleanContentDirectory($file);
		}
		return $empty && rmdir($path);
	}

	private function editPartial()
	{
		$settings = include('../config/settings.php');
		$currentTheme = $settings['theme'];
		$content = $_POST['content'];
		$page = $content;

		$oldfile = '../themes/' . $currentTheme . '/partials/' . $_POST['oldfilename'];
		$file = '../themes/' . $currentTheme . '/partials/' . $_POST['filename'];
		$directory = substr($file, 0, strrpos( $file, '/'));
		$file = str_replace('.html', '', $file) . '.html';
		if (!is_dir($directory)) {
			mkdir($directory, 0777, true);
		}

		if ($file == $oldfile || file_exists($file) == false) {
			file_put_contents($file, $page);
		} else {
			file_put_contents($oldfile, $page);
		}

		if (file_get_contents($file) == $page) {
			$this->messages[] = 'Page Saved as <b>' . $file . '</b> Successful.';
			if ($file !== $oldfile) {
				unlink($oldfile);
			}
		} else {
			$this->errors[] = 'Could not save as file as <b>' . $file . '</b>, saved contents as <b>' . $oldfile . '</b> instead.';
		}
	}
}

?>