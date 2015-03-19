<?php namespace Trapvincenzo\StaticViewCache;
use Illuminate\View\FileViewFinder;

/**
 * @author Vincenzo Trapani <trapvincenzo@gmail.com>
 *
 **/
class StaticViewCache  extends FileViewFinder
{
    protected $target = null;

    /**
     * Gets the html for the requested view
     *
     * @param string $name
     * @param string $id
     * @param array $data
     * @return string
     */
    public function render($name, $id, $data, $path = 'static')
    {
   
        if (is_null($this->target)) {
            $this->target = $this->getTarget();
            $this->target->init($path);
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

        return $this->target->isExpired($name, $filename);


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

        return $this->target->save($filename, $content);
    }

    /**
     * Gets the content for the cached view
     *
     * @param string $filename
     * @return string
     */
    private function getContent($filename)
    {
        return $this->target->get($filename);
    }

   

    /**
     * Flushs the static cache
     *
     * @return bool
     */
    public function flush($path='static')
    {
        return $this->target->flush($path);
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
        return $this->target->getKey($name, $id, $path);
    }

    /**
     * Gets the target
     *
     */
    public function getTarget()
    {
        $config =  \Config::get('view.static_view_cache', 'laravel');

        switch ($config) {
            case 'laravel':
                return new \Trapvincenzo\StaticViewCache\TargetInterface\LaravelTarget();
                break;
            default:
                return new \Trapvincenzo\StaticViewCache\TargetInterface\InternalTarget($this, $this->files);
                break;
        }
    }

}
