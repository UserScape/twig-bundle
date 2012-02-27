<?php

/**
 * Get the path to the Twig compilation directory.
 */
$cache = path('storage').'cache/twig';

/**
 * Make the cache directory if it isn't there
 */
if ( ! is_dir($cache)) mkdir($cache);

/**
 * Register the Twig library with the auto-loader
 */
Laravel\Autoloader::underscored(array(

	'Twig' => Bundle::path('twig').'lib/Twig')

);

/**
 * Register the Twig extension as a valid View extension
 */
Laravel\View::$extensions[] = '.twig.php';

/**
 * Create and register the Twig instance in the IoC container
 */
$loader = new Twig_Loader_Filesystem(array());

$twig = new Twig_Environment($loader, compact('cache'));

Laravel\IoC::instance('twig', $twig);

/**
 * Hook into the Laravel view engine
 */
Laravel\Event::listen(Laravel\View::engine, function($view) use ($twig)
{
	// Only handle views that have the Twig extension
	if ( ! str_contains($view->path, '.twig.php'))
	{
		return false;
	}

	// Set the loader path to the correct path for the view
	$twig->getLoader()->setPaths(array(dirname($view->path)));

	// Give one last chance to tweak the twig and view instance
	Laravel\Event::fire('twig::rendering', array($twig, $view));

	// Render back the template contents
	return $twig->render(basename($view->path), $view->data());
});