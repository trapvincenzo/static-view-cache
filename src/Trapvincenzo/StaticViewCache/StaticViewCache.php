<?php namespace Trapvincenzo\StaticViewCache;
use Illuminate\View\FileViewFinder;

/**
 * @author Vincenzo Trapani <trapvincenzo@gmail.com>
 *
 **/
class StaticViewCache extends FileViewFinder
{
    /**
     * Gets the html for the requested view
     *
     * @param string $name
     * @param string $id
     * @param array $data
     * @return string
     */
    public function render($name, $id, $data)
    {   
        $directory = $this->getPath();

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory);
        }


        $filename = $this->getFilename($name, $id);

        return ($this->isExpired($name, $id))
            ? ($this->save($name, $filename, $data))
            : ($this->getContent($filename));
    }

    /**
     * Checks if the view is expired
     *
     * @param string $name
     * @param string $id
     * @return bool
     */
    private function isExpired($name, $id)
    {
        $filename = $this->getFilename($name, $id);

        if (!$this->files->exists($filename) || \Config::get('view.ignore_static_view_cache', false) === true)
        {
            return true;
        }

        $realLastModified = $this->files->lastModified($this->find($name));
        $cacheLastModified = $this->files->lastModified($filename);

        return $realLastModified > $cacheLastModified;
    }

    /**
     * Saves the view in cache
     *
     * @param string $name
     * @param string $filename
     * @param array $data
     * @return string
     */
    public function save($name, $filename, $data)
    {
        $content = \View::make($name)->with($data)->render();

        $this->files->put($filename, $content);

        return $content;
    }

    /**
     * Gets the content for the cached view
     *
     * @param string $filename
     * @return string
     */
    private function getContent($filename)
    {
        return $this->files->get($filename);
    }

    /**
     * Gets the cache path
     * 
     * @return string
     */
    private function getPath()
    {
        return storage_path(). '/views/static/';
    }

    /**
     * Flushs the static cache
     *
     * @return bool
     */
    public function flush()
    {   
        if ($this->files->isDirectory($this->getPath())) {
            $this->files->cleanDirectory($this->getPath());
        } else {
            return false;
        }

        return true;
    }

    /**
     * Gets the static filename
     * 
     * @param string $name
     * @param string $id
     * @return string
     */
    private function getFilename($name, $id)
    {
        return  $this->getPath() . md5($name . $id);
    }

}
