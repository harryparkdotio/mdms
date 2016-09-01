<?php

/**
 * @package mdms - markdown management system
 * @subpackage Navigation - a plugin for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */

require_once('Plugins.php');

class Navigation extends Plugins
{
	protected $enabled = true;

	protected $navbar;
	protected $navbar_brand;
	protected $navbar_type;
	protected $links;
	protected $style;
	protected $base;

	public function onConfigLoaded(&$config) {
		if (isset($config['navbar'])) {
			$this->navbar = $config['navbar'];
		}
		if (isset($config['navbar_brand'])) {
			$this->navbar_brand = $config['navbar_brand'];
		}
		if (isset($config['navbar_type'])) {
			$navbar_type = $config['navbar_type'];
		} else {
			$navbar_type = 'default';
		}

		if (isset($config['base'])) {
			$this->base = $config['base'];
		}

		if ($this->navbar_brand !== null) {
			foreach ($this->navbar_brand as $key => $value) {
				$this->navbar_brand = '<a class="navbar-brand" href="/' . $this->base . '/' . $value . '">' . $key . '</a>';
			}
		} else {
			$this->navbar_brand = '';
		}
		foreach ($this->navbar as $key => $value) {
			$this->links .= '<li><a href="/' . $this->base . '/' . $value . '">' . $key . '</a></li>';
		}

		switch ($navbar_type) {
			case 'static':
				$this->navbar_type = 'navbar-default navbar-static-top';
				break;
			case 'fixed':
				$this->navbar_type = 'navbar-default navbar-fixed-top';
				$this->style = 'body {min-height: 2000px;padding-top: 70px;}';
				break;
			default:
				$this->navbar_type = 'navbar-default';
				break;
		}
	}

	public function beforeRender(array &$values) {
		if (!empty($this->navbar)) {
			$values['navbar'] = '
				<style>' . $this->style . '</style>
				<nav class="navbar ' . $this->navbar_type . '">
					<div class="container">
						<div class="navbar-header">
							<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
								<span class="sr-only">Toggle navigation</span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
								<span class="icon-bar"></span>
							</button>' .
							$this->navbar_brand
						. '</div>
						<div id="navbar" class="navbar-collapse collapse">
							<ul class="nav navbar-nav">' .
								$this->links
							. '</ul>
						</div>
					</div>
				</nav>
			';
		}
	}
}