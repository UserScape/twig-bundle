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
	 * Get various other Twig configuration items
	 */
	$debug = Config::get('twig::config.debug');

	$autoescape = Config::get('twig::config.autoescape');

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

	/**
	 * Instantiate a new Laravel Twig loader
	 */
	include 'loader.php';

	/**
	 * If it's registered in the IoC, resolve from there... otherwise use default
	 */
	if (IoC::registered('twig::loader'))
	{
		$loader = IoC::resolve('twig::loader');
	}
	else
	{
		$loader = new Laravel_Twig_Loader($ext);
	}

	/**
	 * Hook into the Laravel view loader
	 */
	Laravel\Event::override(Laravel\View::loader, function($bundle, $view) use ($loader, $ext)
	{
		// Use the custom Laravel Twig loader for Twig views...
		if (starts_with($view, 'twig|'))
		{
			return $loader->getPath($bundle, substr($view, 5));
		}
		elseif (starts_with($bundle, 'twig|'))
		{
			return $loader->getPath(substr($bundle, 5), $view);
		}
		// Otherwise use the default Laravel loading conventions...
		else
		{
			return View::file($bundle, $view, Bundle::path($bundle).'views');
		}
	});

	/**
	 * Hook into the Laravel view engine
	 */
	Laravel\Event::listen(Laravel\View::engine, function($view) use ($loader, $cache, $ext, $debug, $autoescape)
	{
		// Only handle views that have the Twig marker
		if ( ! starts_with($view->view, 'twig|')) return false;

		// Load the Laravel Twig extensions
		require_once 'extensions/HTML.php';

		$twig = new Twig_Environment($loader, compact('cache', 'debug', 'autoescape'));

		// Register the Laravel Twig extensions
		$twig->addExtension(new Laravel_Twig_Extension);

		// Render back the template contents
		return $twig->render(substr($view->view, 5), $view->data());
	});

});