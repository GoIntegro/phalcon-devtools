<?php

/*
  +------------------------------------------------------------------------+
  | Phalcon Framework                                                      |
  +------------------------------------------------------------------------+
  | Copyright (c) 2011-2012 Phalcon Team (http://www.phalconphp.com)       |
  +------------------------------------------------------------------------+
  | This source file is subject to the New BSD License that is bundled     |
  | with this package in the file docs/LICENSE.txt.                        |
  |                                                                        |
  | If you did not receive a copy of the license and are unable to         |
  | obtain it through the world-wide-web, please send an email             |
  | to license@phalconphp.com so we can send you a copy immediately.       |
  +------------------------------------------------------------------------+
  | Authors: Andres Gutierrez <andres@phalconphp.com>                      |
  |          Eduar Carvajal <eduar@phalconphp.com>                         |
  +------------------------------------------------------------------------+
*/

/**
 * Phalcon_WebTools
 *
 * Allows to use Phalcon Developer Tools with a web interface
 *
 * @category 	Phalcon
 * @package 	Scripts
 * @copyright   Copyright (c) 2011-2012 Phalcon Team (team@phalconphp.com)
 * @license 	New BSD License
 */
class Phalcon_WebTools {

	private $_path;

	private $_uri;

	private $_settings;

	/**
	 * Load the default config in the project
	 */
	public function __construct($uri, $path){

		$this->_path = $path;
		$this->_uri = $uri;

		$configPath = $path."/../app/config/config.ini";
		if(file_exists($configPath)){
			$this->_settings = new Phalcon_Config_Adapter_Ini($configPath);
		} else {
			throw new Phalcon_Exception('Configuration file could not be loaded');
		}

	}

	/**
	 * Starts connection to DB by config.ini
	 */
	public function getConnection(){
		$connection = Phalcon_Db::factory($this->_settings->database->adapter, $this->_settings->database, true);
		$connection->setFetchMode(Phalcon_Db::DB_NUM);
		return $connection;
	}

	/**
	 * Makes HTML view to Controllers
	 */
	public function getControllers(){

		$html = '<div class="span9">

			<p><h1>Create Controller</h1></p>

			<form method="POST" class="forma-horizontal" action="?action=saveC">
				<fieldset>
				<div class="control-group">
					<label class="control-label" for="name">Table name</label>
					<div class="controls">
						'.Phalcon_Tag::textField(array(
							'name',
							'class' => 'input-xlarge',
							'placeholder' => 'Table name ...',
							'style' => 'height:30px;'
						)).'
					</div>
				</div>
				<label class="checkbox">
					<input type="checkbox" name="force" value="1">Force
				</label>
				<div class="form-sactions" align="right">
		        	<button class="btn btn-primary" type="submit">Save changes</button>
		        	<input class="btn" type="reset" value="Cancel"/>
		        </div>
		        </fieldset>
			</form>
		</div>';

		return $html;
	}

	public function createController(){

		$html = '';
		$request = Phalcon_Request::getInstance();

		$name = $request->getPost('name');
		$force = $request->getPost('force');

		try {

			$modelBuilder = Phalcon_Builder::factory('Controller', array(
				'name' => $name,
				'directory' => $PROJECTPATH,
				'force' => $force
			));

			if($modelBuilder){
				$html = $modelBuilder->build();
			}
			$html = '<div class="alert alert-success">The controller "'.$name.'" was created successfully</div>';
		}
		catch(Phalcon_BuilderException $e){
			$html = '<div class="alert alert-error">'.$e->getMessage().'</div>';
		}

		$html .= $this->getControllers();

		return $html;
	}

	/**
	* Make HTML view to Models
	*/
	public function getModels(){

		$connection = $this->getConnection();

		$tables = array();
		$result = $connection->query("SHOW TABLES");
		while($table = $connection->fetchArray($result)){
			$tables[$table[0]]=$table[0];
		}

		$html = '<div class="span9">

			<p><h1>Generate Models</h1></p>

			<form method="POST" class="forma-horizontal" action="?action=saveM">
				<table class="table table-striped table-bordered table-condensed">
					<tr>
						<td><b>Schema</b></td>
						<td><i>'.$this->_settings->database->name.'</i></td>
					</tr>
					<!--<tr>
						<td><b>All models</b></td>
						<td><i><input type="checkbox" name="allModels" value="1" /></i></td>
					</tr>-->
					<tr>
						<td><b>Table name</b></td>
						<td><i>'.Phalcon_Tag::selectStatic('table-name', $tables).'</i></td>
					</tr>
					<tr>
						<td><b>Add setters and getters</b></td>
						<td><i><input type="checkbox" name="gen-setters-getters" checked="checked" value="1"/></i></td>
					</tr>
					<tr>
						<td><b>Force</b></td>
						<td><i><input type="checkbox" name="force" value="1"/></i></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="form-actions" align="right">
					        	<button class="btn btn-primary" type="submit">Save changes</button>
					        	<button class="btn">Cancel</button>
					        </div>
						</td>
					</tr>
				</table>
			</form>
		</div>';

		return $html;
	}

	public function createModel(){

		$html = '';
		$request = Phalcon_Request::getInstance();

		$genSettersGetters = $request->getPost('gen-setters-getters', 'int');
		$tableName = $request->getPost('table-name');
		$force = $request->getPost('force', 'int');

		try {

			$modelBuilder = Phalcon_Builder::factory('Model', array(
				'name' => $tableName,
				'genSettersGetters' => $genSettersGetters,
				'directory' => $PROJECTPATH,
				'force' => $force
			));

			if($modelBuilder){
				$html = $modelBuilder->build();
			}
			$html = '<div class="alert alert-success">The model "'.$tableName.'" was created success</div>';
		}
		catch(Phalcon_BuilderException $e){
			$html = '<div class="alert alert-error">'.$e->getMessage().'</div>';
		}

		$html .= $this->getModels();

		return $html;
	}

	/**
	 * Makes HTML view to Scaffold
	 */
	public function getScaffold()	{

		$connection = $this->getConnection();

		$tables = array();
		$result = $connection->query("SHOW TABLES");
		while($table = $connection->fetchArray($result)){
			$tables[$table[0]]=$table[0];
		}

		$html = '<div class="span9">

			<p><h1>Generate Scaffold</h1></p>

			<form class="forma-horizontal" action="?action=saveS">
				<table class="table table-striped table-bordered table-condensed">
					<tr>
						<td><b>Schema</b></td>
						<td><i>'.$this->_settings->database->name.'</i></td>
					</tr>
					<tr>
						<td><b>Table name</b></td>
						<td><i>'.Phalcon_Tag::selectStatic('table-name', $tables).'</i></td>
					</tr>
					<tr>
						<td><b>Force</b></td>
						<td><i><input type="checkbox" name="force" /></i></td>
					</tr>
					<tr>
						<td colspan="2">
							<div class="form-actions" align="right">
					        	<button class="btn btn-primary" type="submit">Save changes</button>
					        	<button class="btn">Cancel</button>
					        </div>
						</td>
					</tr>
				</table>
			</form>
		</div>';

		return $html;
	}

	public function getConfig()	{

		$html = '<div class="span7">
			<p><h1>Active Configuration</h1></p>
			<table class="table table-striped table-bordered table-condensed">
				<tr>
					<td><b>Adapter</b></td>
					<td><i>'.$this->_settings->database->adapter.'</i></td>
				</tr>
				<tr>
					<td><b>Database</b></td>
					<td><i>'.$this->_settings->database->name.'</i></td>
				</tr>
				<tr>
					<td><b>Controllers path</b></td>
					<td><i>'.$this->_settings->phalcon->controllersDir.'</i></td>
				</tr>
				<tr>
					<td><b>Models path</b></td>
					<td><i>'.$this->_settings->phalcon->modelsDir.'</i></td>
				</tr>
				<tr>
					<td><b>View path</b></td>
					<td><i>'.$this->_settings->phalcon->viewsDir.'</i></td>
				</tr>
			</table>
		</div>';

		return $html;
	}

	/**
	 * Checks remote address ip to disable remote activity
	 */
	public static function checkIp(){
		if(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR']=='127.0.0.1'){
			return false;
		} else {
			throw new Phalcon_Exception('WebDeveloperTools can only be used on the local machine');
		}
	}

	/**
	 * Applies main template to
	 */
	public static function applyTemplate($uri, $body){
		return '<!DOCTYPE html>
		<html>
			<head>
				<title>Phalcon PHP Framework - Web DevTools.</title>
				<link rel="stylesheet" type="text/css" href="'.$uri.'/css/bootstrap/bootstrap.min.css">
			</head>
			<body>
				<div class="container-fluid">
				    <div class="row-fluid">
					    <div class="span2">
					    	<!--Sidebar content-->
					    	<div style="padding: 8px 0;" class="well">
							    <ul class="nav nav-list">
							      '.Phalcon_WebTools::getMenu($uri).'
							    </ul>
							</div>
					    </div>
					    <div class="span9 well">
					    	<!--Body content-->
					    	'.$body.'
					    </div>
				    </div>
			    </div>
			    <script type="text/javascript" href="'.$uri.'/javascript/bootstrap/bootstrap.min.js"></script>
			</body>
		</html>';
	}

	public static function getMenu($uri){
		$options = array(
			'' => array(
				'caption' => 'Home',
				'icons' => 'icon-home icon-white'
			),
			'C' => array(
				'caption' => 'Controllers',
				'icons' => 'icon-list-alt'
			),
			'M' => array(
				'caption' => 'Models',
				'icons' => 'icon-list'
			),
			'S' => array(
				'caption' => 'Scaffold',
				'icons' => 'icon-lock'
			),
			'E' => array(
				'caption' => 'Configuration',
				'icons' => 'icon-info-sign'
			),
		);
		$code = '';
		foreach($options as $action => $option){
			if($_GET['action']==$action){
				$code.= '<li class="active"><a href="'.$uri.'/webtools.php?action='.$action.'"><i class="'.$option['icons'].'"></i>'.$option['caption'].'</a></li>'.PHP_EOL;
			} else {
				$code.= '<li><a href="'.$uri.'/webtools.php?action='.$action.'"><i class="'.$option['icons'].'"></i>'.$option['caption'].'</a></li>'.PHP_EOL;
			}
		}
		return $code;
	}

	public function dispatch(){
		switch ($_GET['action']) {

			case 'C':
				return $this->getControllers();
				break;

			case 'saveC':
				return $this->createController();
				break;

			case 'M':
				return $this->getModels();
				break;
			case 'saveM':
				return $this->createModel();
				break;

			case 'S':
				return $this->getScaffold();
				break;

			case 'E':
				return $this->getConfig();
				break;

			default:
				return '<div class="alert alert-error">Unknown action</div>';
				break;
		}
	}

	public static function main($path){

		if(isset($_SERVER['PHP_SELF'])){
			$uri = '/'.join(array_slice(explode('/' , dirname($_SERVER['PHP_SELF'])), 1, -1), '/');
		} else {
			$uri = '/';
		}

		try {
			$webTools = new self($uri, $path);
			if(isset($_GET['action']) && $_GET['action']){
				$body = $webTools->dispatch();
			} else {
				$body = '<p><h1>Welcome to Web Developer Tools</h1></p>';
    			$body.= '<p>This application allows you to use Phalcon Developer Tools using a web interface.</p>';
			}
		}
		catch(Phalcon_Exception $e){
			$body = '<div class="alert alert-error">'.$e->getMessage().'</div>';
		}

		if($_SERVER['HTTP_X_REQUESTED_WITH']!='XMLHttpRequest'){
			$body = self::applyTemplate($uri, $body);
		}

		echo $body;
	}

}