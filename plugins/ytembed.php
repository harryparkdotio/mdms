<?php

/**
 * @package mdms - markdown management system
 * @subpackage TEMPLATE - a TEMPLATE for making plugins for mdms
 * @author Harry Park <harry@harrypark.io>
 * @link http://harrypark.io
 * @license http://opensource.org/licenses/MIT
 * @version 0.1
 */

require_once('Plugins.php');

class ytembed extends Plugins
{
	public $enabled = true; // SET THIS TRUE TO FORCE ENABLE (can still disable in config after load)

	public function beforeRender(array &$values, &$template, &$templateDir)
	{
		if(array_key_exists('page', $values)) {
			preg_match_all( '#\[embed *.*?\]#s', $values['page']['content'], $matches);

			if(count($matches[0])>0) {

				// Get page content
				$new_content = &$values['page']['content'];
				// Walk through shortcodes one by one
				foreach($matches[0] as $match) {
					// Get youtube like and video ID (Ref:http://stackoverflow.com/questions/3717115/regular-expression-for-youtube-links/3726073#3726073)
					preg_match( '#http(?:s)?\:\/\/(?:www\.)?youtu(?:be\.com/watch\?v=|\.be/)([\w\-]+)(&(amp;)?[\w\?=]*)?#s', $match, $embed_link );

					// Make sure we found the link ($embed_link[0]) and the ID ($embed_link[1])
					if(count($embed_link)>1) {
						// Generate embeding code
						$embed_code = '<iframe width="854" height="480" src="https://www.youtube.com/embed/'.$embed_link[1].'" frameborder="0" allowfullscreen></iframe>' ;

						// Replace embeding code with the shortcode in the content
						$new_content = preg_replace('#\[embed *.*?\]#s', $embed_code, $new_content,1);
					}
				}
			}
		}
	}
}