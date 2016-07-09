<?php

/**
 * @package mdms - markdown management system
 * @subpackage GoogleAnalytics - a plugin to make google analytics easy to use with mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 1.0
 */

require_once('Plugins.php');

class GoogleAnalytics extends Plugins
{
	protected $enabled = true; // enabled by default

	protected $googleTrackingId;
	protected $site_title;

	public function onConfigLoaded(&$config) {
		if (isset($config['google_tracking_id'])) {
			$this->googleTrackingId = $config['google_tracking_id'];
		}
		if (isset($config['site_title'])) {
			$this->site_title = $config['site_title'];
		}
	}
	
	public function beforeRender(array &$values, &$template, &$templateDir) {
		if (!empty($this->googleTrackingId)) {
			$values['googletrackingcode'] = '
			<script>
				(function(i,s,o,g,r,a,m){i[\'GoogleAnalyticsObject\']=r;i[r]=i[r]||function(){
				(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
				m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
				})(window,document,\'script\',\'//www.google-analytics.com/analytics.js\',\'ga\');
				ga(\'create\', \'' . $this->googleTrackingId . '\', \'' . $this->site_title . '\');
				ga(\'send\', \'pageview\');
			</script>
			';
		}
	}
}