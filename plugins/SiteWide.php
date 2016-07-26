<?php

/**
 * @package mdms - markdown management system
 * @subpackage SiteWide - a plugin to enable SiteWide scripts and styles in mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 */

require_once('Plugins.php');

class SiteWide extends Plugins
{
	protected $enabled = true;

	protected $css = array();
	protected $js = array();

	public function onConfigLoaded(&$config)
	{
		if (isset($config['SiteWide.css'])) {
			$this->css = $config['SiteWide.css'];
		}
		if (isset($config['SiteWide.js'])) {
			$this->js = $config['SiteWide.js'];
		}
	}

	public function beforeRender(array &$values)
	{
		$css = '';
		foreach ($this->css as $val) {
			$css .= $val;
		}
		$js = '';
		foreach ($this->js as $val) {
			$js .= $val;
		}
		$values['SiteWide_css'] = $css;
		$values['SiteWide_js'] = $js;
	}
}