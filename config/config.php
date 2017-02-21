<?php

/**
 * mdms config
 *
 * @author  Harry Park <harry@harrypark.io>
 * @link    http://harrypark.io
 * @license http://opensource.org/licenses/MIT The MIT License
 */

// BASIC
$config['debug'] = true;
$config['site_title'] = 'mdms';
$config['meta'] = [ // add your meta info here. Note, these will be overwritten if specified in yaml header of .md file
	'title' => '',
	'description' => '',
	'author' => '',
	'robots' => '',
];

// THEME
$config['theme'] = 'default';

// Plugins
$config['enableByDefault'] = true; // true, specify which plugins to disable; false, specify which plugins to enable

// Admin
$config['Admin.enabled'] = true;
$config['Admin.editor'] = 'advanced'; // simple/advanced; default: advanced

// SiteWide
$config['SiteWide.enabled'] = true;
$config['SiteWide.css'] = [
	'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">',
	'<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">',
	'<!--[if lt IE 9]>' .
		'<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>' .
		'<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>' .
	'<![endif]-->',
];
$config['SiteWide.js'] = [
	'<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>',
	'<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>',
];

// Blog
$config['Blog.enabled'] = true;

// GoogleAnalytics
$config['google_tracking_id'] = 'UA-77005711-1';

// Navigation
$config['navbar'] = [ // link => name
	'Home'=> '',
	'About' => 'about',
	'Documentation'	=> 'documentation',
	'Themes' => 'customisation/themes',
	'Plugins' => 'customisation/plugins',
];
$config['navbar_type'] = 'static'; // static / default / fixed
$config['navbar_brand'] = ['mdms' => ''];

// TIMEZONE
$config['timezone'] = 'AEST';

/**

VALUES TO IMPLEMENT

{{ base_dir }} // path of root --> location of mdms
{{ base_url }} // linking path
{{ theme_url }} //
{{ rewrite_url }}
{{ site_title }} // title of the site - set in config/config.php
{{ meta }} // contains array of meta values
	{{ meta.title }} <-- YAMLfetch
	{{ meta.description }}
	{{ meta.author }}
	{{ meta.date }}
	{{ meta.date_formatted }}
	{{ meta.time }}
	{{ meta.robots }}
{{ content }} // contains markdown parsed content
{{ pages }}
	{{ page.id }}
	{{ page.url }}
Y	{{ page.title }}
Y	{{ page.description }}
Y	{{ page.author }}
	{{ page.time }}
	{{ page.date }}
	{{ page.date_formatted }}
	{{ page.raw_content }}
Y	{{ page.content|raw }}
	{{ page.meta }}
*/