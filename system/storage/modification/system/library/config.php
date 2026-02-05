<?php
/**
 * @package		OpenCart
 * @author		Daniel Kerr
 * @copyright	Copyright (c) 2005 - 2017, OpenCart, Ltd. (https://www.opencart.com/)
 * @license		https://opensource.org/licenses/GPL-3.0
 * @link		https://www.opencart.com
*/

/**
* Config class
*/
class Config {
	private $data = array();
    
	/**
	 * 
	 *
	 * @param	string	$key
	 * 
	 * @return	mixed
	 */
	public function get($key) {
		return (isset($this->data[$key]) ? $this->data[$key] : null);
	}
	
	/**
	 * Get language-specific config value
	 *
	 * @param	string	$key
	 * 
	 * @return	mixed
	 */
	public function getLanguage($key) {
		if (isset($_GET['language_id'])) {
            $language_id = (int)$_GET['language_id'];
        } else {
			$language_id = $this->get('config_language_id');
		}
		$language_key = $key . '_' . $language_id;

		return (isset($this->data[$language_key]) ? $this->data[$language_key] : $this->get($key));
	}
	
	
    /**
     * 
     *
     * @param	string	$key
	 * @param	mixed	$value
     */
	public function set($key, $value) {
		$this->data[$key] = $value;
	}

    /**
     * 
     *
     * @param	string	$key
	 *
	 * @return	bool
     */
	public function has($key) {
		return isset($this->data[$key]);
	}
	
    /**
     * 
     *
     * @param	string	$filename
     */
	public function load($filename) {
		$file = DIR_CONFIG . $filename . '.php';

		if (file_exists($file)) {
			$_ = array();

			require(modification($file));

			$this->data = array_merge($this->data, $_);
		} else {
			trigger_error('Error: Could not load config ' . $filename . '!');
			exit();
		}
	}
}