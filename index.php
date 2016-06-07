<?php

// Turns on error displaying
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// home path of URL (subfolder of installation; leave as slash for root unstallations)
$homepath = '/';
// gets current URL
$url = $_SERVER['REQUEST_URI'];
// gets current url, removes the homepath for getting correct .md file below
$requestedPage = str_replace($homepath, '', $url);
// adds the content folder prefix (content/), adds the markdown file extension (.md)
$page = 'content/' . $requestedPage . '.md';

// Check for file existence, returns 404 error.
if (!file_exists($page)) {
	$page = str_replace('/.md', '.md', $page);
	if (!file_exists($page)) {
		$page = str_replace('/.md', '/index.md', $page);
		if (!file_exists($page)) {
			$page = str_replace('.md', '/index.md', $page);
			if (!file_exists($page)) {
				$page = 'content/404.md'; // returns if no page found
			}
		}
	}
}

$assetslash = str_repeat('../', substr_count($requestedPage, '/')); // gets URL depth, allows html page to find assets correctly

// Load required files
require_once('backend/parsedown.php'); // Markdown to html parser
require_once('backend/frontmatter.php'); // Gets YAML vars from .md file, seperates YAML header from content
require_once('backend/Twig/Autoloader.php');
Twig_Autoloader::register();

// Load classes from required file
$page = new FrontMatter($page);
$mdParse = new Parsedown();

// get YAML header variables from .md file
$content = $mdParse->text($page->fetch('content')); // Parse content from md to html
$title = $page->fetch('title'); // get title from YAML header
$description = $page->fetch('description'); // get desc from YAML header
$template = $page->fetch('template') . '.html'; // get template from YAML header

$loader = new Twig_Loader_Filesystem('templates');

// values to be rendered.
$values = array(
	'content' => $content,
	'title' => $title,
	'description' => $description,
	'template' => $template,
	'assetslash' => $assetslash,
);

$twig = new Twig_Environment($loader);

echo $twig->render($template, $values);

?>