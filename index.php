<?php

// Turns on error displaying; helpful for debugging errors.
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);

// gets current URL
$base = str_replace('index.php', '', $_SERVER['SCRIPT_NAME']);
$url = str_replace($base, '', $_SERVER['REQUEST_URI']);
$requestedPage = str_replace($base, '', $_SERVER['REQUEST_URI']);

// adds the content folder prefix (content/), adds the markdown file extension (.md)
$page = 'content/' . $requestedPage . '.md';
$urldepth = str_repeat('../', substr_count($requestedPage, '/'));

// Check if the url (which represents the page location) and corresponding page exists; check if file is an index file (represented by a slash); otherwise return a 404 error
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

// Load required files
require_once('backend/parsedown.php'); // Markdown to html parser
require_once('backend/frontmatter.php'); // Gets YAML vars from .md file, seperates YAML header from content
require_once('backend/Twig/Autoloader.php');
require_once('config/settings.php'); // loads user preferences; eg. activeTheme, backend editor, etc.

// Run Twig templating
Twig_Autoloader::register();

// Load classes from required file
$yaml = new FrontMatter($page);
$mdParse = new Parsedown();
$preferences = new Settings();

// get YAML header variables from .md file
$content = $mdParse->text($yaml->fetch('content')); // Parse content from md to html

// values to be rendered (bare minimums, more values are added further down)
$values = array(
	'urldepth' => $urldepth,
	'base' => $base,
	'year' => date("Y"),
);

// get all yaml page values
$page = array();
foreach ($yaml->values as $value) {
	$page[$value] = $yaml->fetch($value);
}
$page['content'] = $content;
$values['page'] = $page;

// if the template specified is none; load the index template. Otherwise just get the specified template
if (in_array('template', $values)) {
	$template = $yaml->fetch('template') . '.html'; // get template from YAML header
} else {
	$template = 'index';
}

// loads all theme values into an array to then be placed as an array into the values array
$theme = array();
$currentTheme = $preferences->activeTheme;
$themeYaml = new FrontMatter('themes/' . $currentTheme . '/theme.yaml');
foreach ($themeYaml->values as $value) {
	$theme[$value] = $themeYaml->fetch($value);
}
$values['theme'] = $theme;
unset($values['childpages']);

// get navbar links and add to array
$values['pagelinks'] = $preferences->links;

// child page content loader
if ($yaml->fetch('childpages')) {
	$children = explode(' ', $yaml->fetch('childpages'));
	$child = array();
	foreach ($children as &$value) {
		$page = array();
		$importChild = new FrontMatter('content/' . $value . '.md');
		foreach ($importChild->values as $valuez) {
			$page[$valuez] = $yaml->fetch($valuez);
		}
		$page['content'] = $mdParse->text($importChild->fetch('content'));
		$child[$value] = $page;
	}
	$values['child'] = $child;
}

// initializes twig templating; loads the current theme (set in admin/classes/setting.php) from the themes folder
$loader = new Twig_Loader_Filesystem('themes/' . $currentTheme);
$twig = new Twig_Environment($loader);

// specific template loading
if (file_exists('themes/' . $currentTheme . '/templates/' . $template)) {
	echo $twig->render('templates/' . $template, $values);
} else {
	echo $twig->render('templates/index.html', $values);
}

?>