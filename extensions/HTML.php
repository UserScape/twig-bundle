<?php
class Laravel_Twig_Extension extends Twig_Extension {

	/**
	 * Returns a list of global functions to add to the existing list.
	 *
	 * @return array An array of global functions
	 */
	public function getFunctions()
	{
		$methods = array();
		foreach (get_class_methods('HTML') as $method)
		{
			$name = 'html_'.$method;
			$methods[$name] = new \Twig_Function_Function('HTML::'.$method);
		}

		foreach (get_class_methods('URL') as $method)
		{
			$name = 'url_'.$method;
			$methods[$name] = new \Twig_Function_Function('URL::'.$method);
		}

		return $methods;
	}

	/**
	 * Returns the name of the extension.
	 *
	 * @return string The extension name
	 */
	public function getName()
	{
		return 'Laravel';
	}
}