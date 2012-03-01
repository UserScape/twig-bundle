<?php
use Symfony\Component\Templating\TemplateNameParserInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Laravel\Bundle;

/**
 * FilesystemLoader extends the default Twig filesystem loader
 * to work with Laravel paths.
 */
class Filesystem_loader extends \Twig_Loader_Filesystem
{
	/**
	 * Constructor.
	 *
	 * @param array $paths
	 */
	public function __construct($paths)
	{
		parent::__construct($paths);
	}

	/**
	 * Returns the path to the template file.
	 *
	 * Extend the findTemplate to find bundle views.
	 *
	 * @param string $name
	 */
	protected function findTemplate($name)
	{
		// normalize name
		$name = preg_replace('#/{2,}#', '/', strtr($name, '\\', '/'));

		if (isset($this->cache[$name])) {
			return $this->cache[$name];
		}

		$this->validateName($name);

		foreach ($this->paths as $path)
		{
			if (is_file($path.'/'.$name))
			{
				return $this->cache[$name] = $path.'/'.$name;
			}
			elseif (is_file($name))
			{
				return $this->cache[$name] = $name;
			}
			elseif (is_file(path('app').'views/'.$name))
			{
				$name = path('app').'views/'.$name;
				return $this->cache[$name] = $name;
			}
			elseif (strpos($name, '::') !== false)
			{
				$name = Bundle::path(Bundle::name($name)).'views/'.Bundle::element($name);
				return $this->cache[$name] = $name;
			}
		}

		throw new Twig_Error_Loader(sprintf('Unable to find a template "%s" (looked into: %s).', $name, implode(', ', $this->paths)));
	}
}
