<?php

// home path of URL (subfolder of installation; leave as slash for root unstallations)
$homepath = '/';
// gets current URL
$url = $_SERVER['REQUEST_URI'];
// gets current url, removes the homepath for getting correct .md file below
$requestedPage = str_replace($homepath, '', $url);
// adds the content folder prefix (content/), adds the markdown file extension (.md)
$page = 'content/' . $requestedPage . '.md';

// Check for file existence, returns 404 error if not.
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

//assetslash is required for loading css and js into templates from an assets folder. This is because html gets files from the current directory, not the source directory like php would.
$assetslash = str_repeat('../', substr_count($requestedPage, '/')); // gets URL depth, allows html page to find assets correctly

// Load required files
require_once('backend/parsedown.php'); // Markdown to html parser
require_once('backend/frontmatter.php'); // Gets YAML vars from .md file, seperates YAML header from content
require_once('backend/Twig/Autoloader.php'); // load twig
Twig_Autoloader::register(); //start twig templating

// Load classes from required file
$page = new FrontMatter($page); //yaml header parser
$mdParse = new Parsedown(); //md to html parser

// get YAML header variables from .md file
// returns an error if not found in the source .md file.
// Please help fix this issue if possible.
$content = $mdParse->text($page->fetch('content')); // Parse content from md to html
$title = $page->fetch('title'); // get title from YAML header
$description = $page->fetch('description'); // get desc from YAML header
$template = $page->fetch('template') . '.html'; // get template from YAML header

$loader = new Twig_Loader_Filesystem('templates'); //the source folder of all templates.

// values to be rendered.
// add custom values here to load them into twig templating.
$values = array(
	'content' => $content,
	'title' => $title,
	'description' => $description,
	'template' => $template,
	'assetslash' => $assetslash,
);

$twig = new Twig_Environment($loader); //load values from array into twig.

echo $twig->render($template, $values); //render twig template

?>
