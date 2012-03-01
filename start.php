<?php

/**
 * Push the bootstrap until after application start to give time to set configs
 */
Event::listen('laravel.started: twig', function()
{

	/**
	 * Grab the Twig file extension.
	 */
	$ext = Config::get('twig::config.extension');

	/**
	 * Get the path to the Twig compilation directory.
	 */
	$cache = Config::get('twig::config.cache');

	/**
	 * Make the cache directory if it is enabled and doesn't exist.
	 */
	if ($cache and ! is_dir($cache)) mkdir($cache);

	/**
	 * Register the Twig library with the auto-loader
	 */
	Laravel\Autoloader::underscored(array(

		'Twig' => Bundle::path('twig').'lib/Twig')

	);
	include 'Filesystem_loader.php';

	/**
	 * Register the Twig extension as a valid View extension
	 */
	Laravel\View::$extensions[] = $ext;

	Laravel\View::$extensions = array_unique(Laravel\View::$extensions);

	/**
	 * Hook into the Laravel view engine
	 */
	Laravel\Event::listen(Laravel\View::engine, function($view) use ($cache, $ext)
	{
		// Create the Twig file-system loader
		$loader = new Filesystem_loader(array());

		// Create a new Twig environment
		$twig = new Twig_Environment($loader, compact('cache'));

		// Only handle views that have the Twig extension
		if ( ! str_contains($view->path, $ext)) return false;

		// Set the loader path to the correct path for the view
		$twig->getLoader()->setPaths(array(dirname($view->path)));

		// Give one last chance to tweak the twig and view instance
		Laravel\Event::fire('twig::rendering', array($twig, $view));

		// Render back the template contents
		return $twig->render(basename($view->path), $view->data());
	});

});