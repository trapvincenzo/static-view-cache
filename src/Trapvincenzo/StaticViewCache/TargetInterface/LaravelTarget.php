<?php
namespace Trapvincenzo\StaticViewCache\TargetInterface;
/**
 * @author Vincenzo Trapani <trapvincenzo@gmail.com>
 *
 * Laravel Target
 **/

class LaravelTarget implements IInterface
{
	protected $interface = null;
	protected $exp;
	/**
	 * {@inheritdoc}
	 */
	public function init($path)
	{
		$this->exp = \Config::get('view.static_view_cache_exptime', 5);
		$this->interface = \App::make('cache');
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function isExpired($name, $filename)
	{
		return !$this->interface->has($filename);
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function save($filename, $content)
	{
		$this->interface->put($filename, $content, $this->exp);
		
		return $content;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function flush()
	{
		$this->interface->flush();
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function get($filename)
	{
		return $this->interface->get($filename);
	}

	public function getKey($name, $id, $path)
	{
		return md5($name . $id . $path);
	}
}