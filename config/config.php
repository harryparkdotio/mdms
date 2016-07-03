<?php

/**
 * mdms configuration
 *
 * @author  Harry Park
 * @link    http://harrypark.io
 * @license http://opensource.org/licenses/MIT The MIT License
 * @version 0.2
 */


// BASIC
$config['site_title'] = 'mdms';
$config['base_url'] = '';

// THEME
$config['theme'] = 'default';
$config['twig_config'] = array(
	'cache' => false, // To enable Twig caching change this to a path to a writable directory
	'autoescape' => false, // Auto-escape Twig vars
	'debug' => false // Enable Twig debug
);

// CONTENT
$config['date_format'] = '%D %T';
$config['content_dir'] = 'content/';
$config['content_ext'] = '.md';

// TIMEZONE
$config['timezone'] = 'AEST';

// PLUGINS
$config['plugins'] = array(
	'Admin' => false,
	'Blog' => true,
	'Example' => false,
);

// TEMP NAV
$config['nav'] = array(
	'Home' => '',
	'About' => 'about',
	'Download' => 'download',
);