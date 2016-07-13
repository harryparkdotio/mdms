<?php

/**
 * @package mdms - markdown management system
 * @subpackage IE8 - a plugin to *help* with the shit of IE8 for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 */

// IE8 is a piece of shit. So are all the other IE versions. Hopefully this makes supporting those frustrating users a little faster. Hopefully, probably not though.

require_once('Plugins.php');

class IE8 extends Plugins
{
	protected $enabled = true;

	public function beforeRender(array &$values)
	{
		$values['IE8'] = '
		<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!-- WARNING: Respond.js doesn\'t work if you view the page via file:// -->
		<!--[if lt IE 9]>
			<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
			<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
    	';
	}
}