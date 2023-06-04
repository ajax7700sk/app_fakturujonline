<?php

/******************
 * helper functions
 ********************/
if(! function_exists('dumpe')) {
    /**
     * Dump and exit
     *
     * @param mixed $var
     */
    function dumpe($var)
    {
        dump($var);
        exit();
    }
}

if(! function_exists('dd')) {
	/**
	 * Dump and exit
	 *
	 * @param mixed $var
	 */
	function dd($var)
	{
		dumpe($var);
	}
}


if (!function_exists('is_countable')) {

    /**
     * Verify that the contents of a variable is a countable value
     *
     * @param mixed $c
     * @return bool
     */
    function is_countable($c) {
        return is_array($c) || $c instanceof Countable;
    }
}

/**
 * @return string
 */
function get_app_root_folder_path()
{
    return dirname(__FILE__, 2);
}

/**
 * @return string
 */
function get_app_www_folder_path()
{
	return get_app_root_folder_path() . DIRECTORY_SEPARATOR . 'www';
}

/**
 * @return string
 */
function get_app_folder_path()
{
    return dirname(__FILE__, 1);
}

/**
 * @return string
 */
function get_app_server_url()
{
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';

    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://{$host}{$requestUri}";
}

if (!function_exists('load_env_val')) {

	/**
	 * Load env file data into global variable
	 *
	 * @param string $file_path
	 */
	function load_env( $file_path ) {
		$file_path = get_app_root_folder_path() . DIRECTORY_SEPARATOR . $file_path;

		if ( file_exists( $file_path ) ) {
			if ( isset( $GLOBALS['env'] ) ) {
				$env_data        = require_once $file_path;
				$GLOBALS['env'] = array_merge( $GLOBALS['env'], $env_data );
			} else {
				$env_data        = require_once $file_path;
				$GLOBALS['env'] = $env_data;
			}
		}
	}
}

if (!function_exists('envval')) {

    /**
     * Get casted env value
     *
     * @param string $key
     * @param null|string|bool|int   $default_value
     *
     * @return mixed
     */
    function envval($key, $default_value = null) {
//        $value = getenv($key);

		if(isset($GLOBALS['env'][$key])) {
			$value = $GLOBALS['env'][$key];
		} else {
			$value = $default_value;
		}

        switch (strtolower($value)) {
            case 'true':
            case '(true)':
                return true;
            case 'false':
            case '(false)':
                return false;
            case 'empty':
            case '(empty)':
                return '';
            case 'null':
            case '(null)':
                return;
            default:
                return $value;
        }
    }
}
