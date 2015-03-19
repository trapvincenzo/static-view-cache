<?php
namespace Trapvincenzo\StaticViewCache\TargetInterface;
/**
 * @author Vincenzo Trapani <trapvincenzo@gmail.com>
 *
 * Internal Target
 **/
class InternalTarget implements IInterface
{
	protected $target;
	protected $files;
	protected $fileView;

	public function __construct($t, $files)
	{
		$this->files = $files;
		$this->fileView = $t;
	}

	/**
	 * {@inheritdoc}
	 */
	public function init($path)
	{
        $directory = $this->getPath($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory);
        }
	}

	/**
     * Gets the cache path
     *
     * @return string
     */
    private function getPath($path)
    {
        return storage_path(). '/views/' . $path .'/';
    }

    /**
	 * {@inheritdoc}
	 */
	public function isExpired($name, $filename)
	{
		if (!$this->files->exists($filename) || \Config::get('view.ignore_static_view_cache', false) === true) {
            return true;
        }

        $realLastModified = $this->files->lastModified($this->fileView->find($name));
        $cacheLastModified = $this->files->lastModified($filename);

        return $realLastModified > $cacheLastModified;
	}

	/**
	 * {@inheritdoc}
	 */
	public function save($filename, $content)
	{
		$this->files->put($filename, $content);

        return $content;
	}

	/**
	 * {@inheritdoc}
	 */
	public function flush()
	{
		if ($this->files->isDirectory($this->getPath($path))) {
            $this->files->cleanDirectory($this->getPath($path));
        } else {
            return false;
        }

        return true;
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function get($filename)
	{
		return $this->files->get($filename);
	}

	/**
	 * {@inheritdoc}
	 */
	public function getKey($name, $id, $path)
	{
		return  $this->getPath($path) . md5($name . $id);
	}
}