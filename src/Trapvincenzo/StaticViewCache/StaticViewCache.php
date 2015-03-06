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
    public function render($name, $id, $data, $path='static')
    {
        $directory = $this->getPath($path);

        if (!$this->files->isDirectory($directory)) {
            $this->files->makeDirectory($directory);
        }

        /**
         * if $id is explicitly set to null, auto generate hash
         */
        if ($id === null) {
            $id = $name . md5(serialize($data), true);
        }

        $filename = $this->getFilename($name, $id, $path);

        return ($this->isExpired($name, $id, $path))
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
    private function isExpired($name, $id, $path)
    {
        $filename = $this->getFilename($name, $id, $path);

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
    private function getPath($path)
    {
        return storage_path(). '/views/' . $path .'/' );
    }

    /**
     * Flushs the static cache
     *
     * @return bool
     */
    public function flush($path='static')
    {
        if ($this->files->isDirectory($this->getPath($path))) {
            $this->files->cleanDirectory($this->getPath($path));
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
    private function getFilename($name, $id, $path)
    {
        return  $this->getPath($path) . md5($name . $id);
    }

}
