<?php

namespace Trapvincenzo\StaticViewCache\TargetInterface;

/**
 * @author Vincenzo Trapani <trapvincenzo@gmail.com>
 *
 * Target interface
 **/
interface IInterface
{
	/**
	 * Initializes the target class
	 *
	 * @param string $path
	 */
	public function init($path);

	/**
	 * Checks the cache key status
	 *
	 * @param string $name
	 * @param string $filename
	 * @return bool
	 */
	public function isExpired($name, $filename);

	/**
	 * Saves the content in cache
	 * 
	 * @param string $filename
	 * @param string $content
	 * @return string
	 */
	public function save($filename, $content);

	/**
	 * Flushs the cache
	 */
	public function flush();
	
	/**
	 * Gets the content from the cache
	 *
	 * @param string $filename
	 * @return mixed
	 */
	public function get($filename); 

	/**
	 * Gets the key
	 *
	 * @param string $name
	 * @param string $id
	 * @param string $path
	 * @return string
	 */
	public function getKey($name, $id, $path);
}
