<?php

/**
 * @package mdms - markdown management system
 * @subpackage Bootstrap - a plugin to enable bootstrap for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 */

// Boostrap hosted from MaxCDN for better performance, and higher reliability.

require_once('Plugins.php');

class Bootstrap extends Plugins
{
	protected $enabled = true;

	public function beforeRender(array &$values)
	{
		$values['bootstrap_css'] = '
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">';
		$values['bootstrap_js'] = '
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>';
	}
}