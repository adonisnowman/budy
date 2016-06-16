<?php

use \Phalcon\Config\Adapter\Ini as PhConfig;
use \Phalcon\Loader as PhLoader;
use \Phalcon\Mvc\Application as PhApplication;
use \Phalcon\Exception as PhException;
use \Phalcon\Session\Adapter\Database as PhDatabase;

class Bootstrap
{
	private $_di;

	/**
	 * Constructor
	 *
	 * @param $di
	 */
	public function __construct($di)
	{
		$this->_di = $di;
	}

	/**
	 * Runs the application performing all initializations
	 *
	 * @param $options
	 *
	 * @return mixed
	 */
	public function init($options)
	{
		$loaders = array(
				'config',
// 				'url',
				'loader',
				'dispatcher',
				'view',
				'database',
				'mangodb',
// 				'session',
				'dbsession',
// 				'Mongosession',	
// 				'environment',
// 				'timezone',
// 				'debug',
// 				'flash',
// 				'url',
// 				'cache',
// 				'logger',
				'behaviors',
		);


		try {

			foreach ($loaders as $service)
			{
				$function = 'init' . ucfirst($service);

				$this->$function($options);
			}

			
			
			$application = new PhApplication();
			$application->setDI($this->_di);

			return $application->handle()->getContent();

		} catch (PhException $e) {
			echo $e->getMessage();
		} catch (\PDOException $e) {
			echo $e->getMessage();
		}
	}

	// Protected functions

	/**
	 * Initializes the config. Reads it from its location and
	 * stores it in the Di container for easier access
	 *
	 * @param array $options
	 */
	protected function initConfig($options = array())
	{
		$configFile = CONFIGFILE_PATH;

		// Create the new object
		$config = new PhConfig($configFile);

		// Store it in the Di container
		$this->_di->set('config', $config);
	}
	
	protected function initUrl($options = array())
	{
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
	
		//Setup the database service
		$di->set('url', function() use ($config){
			$url = new Phalcon\Mvc\Url();
			$url->setBaseUri('/ms168/');
			return $url;
		});
	}
	
	
	/**
	 * Initializes the loader
	 *
	 * @param array $options
	 */
	protected function initLoader($options = array())
	{
		$config = $this->_di->get('config');

		// Creates the autoloader
		$loader = new PhLoader();
		
		$loader->registerDirs(
				array(
						ROOT_PATH . $config->application->controllersDir,
						ROOT_PATH . $config->application->pluginsDir,
						ROOT_PATH . $config->application->modelsDir,
						ROOT_PATH . $config->application->libraryDir,
				)
		);
		
		// Register the namespace
// 		$loader->registerNamespaces(
// 				array("Lib" => $config->application->libraryDir)
// 		);
		$loader->registerNamespaces(array(
				'Phalcon' => '/path/to/incubator/Library/Phalcon/'
		));
		
		$loader->register();
	}
	protected function initDatabase($options = array())
	{
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
		
		//Setup the database service
		$di->set('db', function() use ($config){
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
					"host" => $config->database->host,
					"username" => $config->database->username,
					"password" => $config->database->password,
					"dbname" => $config->database->name,
					'charset'   =>$config->database->charset,
			));
		});
	}
	
	
	protected function initBlogdb($options = array())
	{
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
	
		//Setup the database service
		$di->set('blogdb', function() use ($config){
			return new \Phalcon\Db\Adapter\Pdo\Mysql(array(
					"host" => $config->blogdb->host,
					"username" => $config->blogdb->username,
					"password" => $config->blogdb->password,
					"dbname" => $config->blogdb->name,
					'charset'   =>$config->blogdb->charset,
			));
		});
	}
	
	protected function initMangodb($options = array())
	{
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
		$di->set('mongo', function() use ($config){
			$mongo = new MongoClient();
			return $mongo->selectDB("my_mongodb");
		}, true);
		//Set the CollectionManager
		$di->set('collectionManager', function(){
			return new Phalcon\Mvc\Collection\Manager();
		}, true);
		/*
		//MongoDB Database
		$di->set('MongoDB', function () use ($config) {
			if (!$config->database->mongo->username OR !$config->database->mongo->password) {
				$mongo = new MongoClient('mongodb://' . $config->database->mongo->host);
			} else {
				$mongo = new MongoClient("mongodb://" . $config->database->mongo->username . ":" . $config->database->mongo->password . "@" . $config->database->mongo->host, array("db" => $config->database->mongo->dbname));
			}
	
			return $mongo->selectDb($config->database->mongo->dbname);
		}, TRUE);
		/**/
	}
	
	protected function initView($options = array())
	{
		$config = $this->_di->get('config');
		$di     = $this->_di;

		//Setup the view component
		$di->set('view', function() use ($config){
			$view = new \Phalcon\Mvc\View();
			$view->setViewsDir($config->application->viewsDir);
			$view->registerEngines(array(
					".volt" => 'Phalcon\Mvc\View\Engine\Volt',
					".phtml" => 'Phalcon\Mvc\View\Engine\Volt'
			));
		
			return $view;
		});
		/*
		$this->_di->set(
				'volt',
				function($view, $di) use($config)
				{
					$volt = new PhVolt($view, $di);
					$volt->setOptions(
							array(
									'compiledPath'      => ROOT_PATH . $config->app->volt->path,
									'compiledExtension' => $config->app->volt->extension,
									'compiledSeparator' => $config->app->volt->separator,
									'stat'              => (bool) $config->app->volt->stat,
							)
					);
					return $volt;
				}
		);
		
		/**/
	}
	

	protected function initDispatcher($options = array())
	{
		
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
		$di->set('dispatcher', function() use ($di) {

	    $eventsManager = $di->getShared('eventsManager');
	    $security = new Security($di);

	    $eventsManager->attach('dispatch', $security);
	    
	    $dispatcher = new Phalcon\Mvc\Dispatcher();	    
	    $dispatcher->setEventsManager($eventsManager);	
	    	return $dispatcher;
		});
	}
	
	protected function initSession($options = array())
	{
	
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
		$di->setShared('session', function() {
			$session = new Phalcon\Session\Adapter\Files();
			$session->start();
			return $session;
		});
	}
	
	
	protected function initMongosession($options = array())
	{
		//https://github.com/phalcon/incubator/tree/master/Library/Phalcon/Session/Adapter
		require_once ROOT_PATH . '/phalcon/library/Mongo.php';
		$config = $this->_di->get('config');
		$di     = $this->_di;
	
		$di->set('session', function() {
	
			//Create a connection to mongo
			$mongo = new Mongo();
		
			//Passing a collection to the adapter
			$session = new Phalcon\Session\Adapter\Mongo(array(
					'collection' => $mongo->test->session_data
			));
		
			$session->start();
		
			return $session;
		});
	}
	
	protected function initDbsession($options = array())
	{
		require_once ROOT_PATH . '/phalcon/library/Database.php';
		$config = $this->_di->get('config');
		$di     = $this->_di;
				
		$di->set('session', function() use ($config) {

		    // Create a connection
		    $connection = new \Phalcon\Db\Adapter\Pdo\Mysql(array(
		        	"host" => $config->database->host,
					"username" => $config->database->username,
					"password" => $config->database->password,
					"dbname" => $config->database->name,
					'charset'   =>$config->database->charset,
		    ));
		
		    $session = new Phalcon\Session\Adapter\Database(array(
		        'db' => $connection,
		        'table' => 'session_data'
		    ));
		
		    $session->start();
		
		    return $session;
		});
	}
	
	
	
	
	/**
	 * Initializes the model behaviors
	*
	* @param array $options
	*/
	protected function initBehaviors($options = array())
	{
		$session = $this->_di->getShared('session');

		// Timestamp
		$this->_di->set(
				'Timestamp',
				function() use ($session)
				{
					$timestamp = new Models\Behaviors\Timestamp($session);
					return $timestamp;
				}
		);
	}
}

