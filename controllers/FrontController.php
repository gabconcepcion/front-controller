<?php

class FrontController
{
	const DEFAULT_CONTROLLER = 'Index';
	const DEFAULT_ACTION = 'index';

	protected $controller = self::DEFAULT_CONTROLLER;
	protected $action = self::DEFAULT_ACTION;
	protected $params = array();
	protected $basePath = 'front-controller/';

	function __construct(array $options=array())
	{
		if(!empty($options))
		{
			if(isset($options['controller']))
				$this->setController($options['controller']);

			if(isset($options['action']))
				$this->setAction($options['action']);

			if(isset($options['params']))
				$this->setParams($options['params']);
		}else{
			$this->parseUri();
		}
	}
	function parseUri()
	{
		$path = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), "/");
		$path = preg_replace('/[^a-zA-Z0-9]\//', "", $path);
		if(strpos($path, $this->basePath)===0)
		{
			$path = substr($path, strlen($this->basePath));
			@list($controller,$action,$params) = explode('/', $path, 3);
		}
		
		if(isset($controller))
			$this->setController($controller);
		else
			$this->setController($this->controller);

		if(isset($action))
			$this->setAction($action);
		else
			$this->setAction($this->action);

		if(isset($params))
			$this->setParams(explode('/', $params));
		else
			$this->setParams($this->params);
	}
	function setController($controller)
	{
		$controller = ucfirst(strtolower($controller)).'Controller';
		if(!class_exists($controller))
			throw new InvalidArgumentException("Controller {$controller} does not exists", 1);
		$this->controller = $controller;
		return $this;
	}
	function setAction($action)
	{
		$reflector = new ReflectionClass($this->controller);
		if(!$reflector->hasMethod($action))
			throw new InvalidArgumentException("Controller method {$action} does not exists", 1);
		$this->action = $action;
		return $this;
	}
	function setParams(array $params)
	{
		$this->params = $params;
		return $this;
	}
	function run()
	{
		call_user_func_array(array($this->controller, $this->action), $this->params);
	}
}