## Requirements

- At least Laravel 3.1

## Installation

### Artisan

	php artisan bundle:install twig

### Bundle Registration

	'twig' => array('auto' => true),

## Usage

####Specifying the Twig template extension in application/start.php:####

	Event::listen('laravel.started: twig', function()
	{
		Config::set('twig::config.extension', '.twig.html');
	});

####Rendering a Twig template from a controller / route:####

	return View::make('twig|home.index');

Notice the **twig|** prefix. This indicates to the view class that the Twig engine should be used.

When including templates from within Twig, you can simple use the Laravel "dot" syntax to include the templates. **Do not use the typical Twig path syntax**.

####Rendering a Twig template from within a template:####

	{% include 'partials.sidebar' %}

####Rendering a bundle Twig template from within a template:####

	{% include 'bundle::home.index' %}