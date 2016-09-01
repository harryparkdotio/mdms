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
$config['base'] = 'mdms';
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
];
$config['SiteWide.js'] = [
	'<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>',
	'<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>',
];

// Blog
$config['Blog.enabled'] = false;

// GoogleAnalytics
$config['google_tracking_id'] = 'XX-XXXXXXXX-X';

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
$config['timezone'] = 'UTC';