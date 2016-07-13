<?php

// PLUGINS TO ADD
// image gallery
// image loader
// navigation
// admin panel

class Plugins
{
	public $config;

	public function handleEvent($eventName, array $params)
	{
		$this->config = $params['pluginconfig'];
		if ($this->isEnabled()) {
			if (method_exists($this, $eventName)) {
				call_user_func_array(array($this, $eventName), $params);
			}
		}
	}

	public function isEnabled()
	{
		if (isset($this->config[get_called_class() . '.enabled'])) {
			if ($this->config[get_called_class() . '.enabled'] == false) {
				return false;
			} else {
				return true;
			}
		} else {
			return $this->config['enableByDefault'];
		}
	}
}