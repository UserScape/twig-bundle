<?php use Laravel\Bundle;

class Laravel_Twig_Loader implements Twig_LoaderInterface {

	/**
	 * The file extension being used for Twig views.
	 *
	 * @var string
	 */
	protected $ext;

	/**
	 * Create a new custom Laravel Twig loader implementation.
	 *
	 * @param  string  $extension
	 * @return void
	 */
	public function __construct($ext)
	{
		$this->ext = $ext;
	}

	/**
	 * Gets the source code of a template, given its name.
	 *
	 * @param  string  $name  The name of the template to load
	 * @return string         The template source code
	 */
	public function getSource($name)
	{
		return file_get_contents($this->getPath(Bundle::name($name), Bundle::element($name)));
	}

	/**
	 * Get the path to the given template
	 *
	 * @param  string  $bundle  The name of the template's bundle
	 * @param  string  $name    The name of the template
	 * @return string           The path to the template source
	 */
	public function getPath($bundle, $name)
	{
		$name = str_replace('.', '/', $name);

		return Bundle::path($bundle).'views/'.$name.$this->ext;
	}

	/**
	 * Gets the cache key to use for the cache for a given template name.
	 *
	 * @param  string  $name  The name of the template to load
	 * @return string         The cache key
	 */
	public function getCacheKey($name)
	{
		return $this->getPath(Bundle::name($name), Bundle::element($name));
	}

	/**
	 * Returns true if the template is still fresh
	 *
	 * @param  string     $name  The template name
	 * @param  timestamp  $time  The last modification time of the cached template
	 * @return bool
	 */
	public function isFresh($name, $time)
	{
		return filemtime($this->getPath(Bundle::name($name), Bundle::element($name))) < $time;
	}

}