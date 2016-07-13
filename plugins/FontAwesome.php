<?php

/**
 * @package mdms - markdown management system
 * @subpackage FontAwesome - a plugin to enable font awesome for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 */

// Font Awesome hosted from MaxCDN for better performance, and higher reliability.

require_once('Plugins.php');

class FontAwesome extends Plugins
{
	protected $enabled = true;

	public function beforeRender(array &$values)
	{
		$values['fontAwesome'] = '
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css">';
	}
}