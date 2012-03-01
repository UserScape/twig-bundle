## Requirements

- At least Laravel 3.1

## Installation

### Artisan

	php artisan bundle:install twig

### Bundle Registration

	'twig' => array('auto' => true),

### Application start file

	Event::listen(View::loader, function($bundle, $view)
	{
		$ext = Config::get('twig::config.extension');
		return Bundle::path($bundle).'views/'.$view.$ext;
	});

## Usage

Simply use the Laravel view class like normal. If your view file extension is **.twig.php**, Twig will be used to parse your view.

If you would like to use a different extension, that's fine. Just set the configuration option in your **application/start.php** file:

**Specifying the Twig template extension:**

	Event::listen('laravel.started: twig', function()
	{
		Config::set('twig::config.extension', '.html');
	});

## Events

Before rendering the content of a template, the bundle raises a **twig::rendering** event, passing in the Twig_Environment instance and the View being rendered.

**Catching the twig::rendering event:**

	Event::listen('twig::rendering', array($twig, $view)
	{
		// Do stuff here...
	});

> **Note:** You do not need to return anything from the rendering event. It is just a place to do any last minute work on the Twig or View instance.