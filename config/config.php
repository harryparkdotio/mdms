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

// Plugins
$config['enableByDefault'] = true; // true, specify which plugins to disable; false, specify which plugins to enable

// Admin
$config['Admin.enabled'] = true;
$config['Admin.editor'] = 'advanced'; // simple/advanced; default: advanced

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
{{ prev_page }} // relative to current_page
{{ current_page }}
{{ next_page }} // relative to current_page
{{ is_front_page }}

*/