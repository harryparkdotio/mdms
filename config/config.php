<?php

/**
 * mdms config
 *
 * @author  Harry Park <harry@harrypark.io>
 * @link    http://harrypark.io
 * @license http://opensource.org/licenses/MIT The MIT License
 */

// BASIC
$config['site_title'] = 'mdms';
$config['base_url'] = 'mdms'; // must have starting & trailing slashes. http://example.com/BASE_URL
$config['meta'] = [ // add your meta info here. Note, these will be overwritten if specified in yaml header of .md file
	'title' => '',
	'description' => '',
	'author' => '',
	'robots' => '',
];

// THEME
$config['theme'] = 'default';
$config['twig_config'] = array(
	'cache' => false,
	'autoescape' => false,
	'debug' => false
);

// PLUGINS
$config['enableByDefault'] = true; // true, specify which plugins to disable; false, specify which plugins to enable

// Blog
$config['Blog.enabled'] = false; // NOT READY FOR PRODUCTION; TRY AT YOUR OWN RISK.

// GoogleAnalytics
$config['google_tracking_id'] = 'UA-77005711-1';

// Navigation
$config['navbar'] = [ // link => name
	'Home'=> '',
	'About' => 'about',
	'Documentation'	=> 'documentation',
	'Plugins' => 'plugins',
];
$config['navbar_type'] = 'static'; // static / default / fixed
$config['navbar_brand'] = ['mdms' => ''];

// TIMEZONE
$config['timezone'] = 'UTC';