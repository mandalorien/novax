<?php

	class Dispatcher{
		const PREFIX = 'Controller';
		
		private $databases;
		private $core = array();
		private $path = array();
		private $module;
		private $controller;
		private $Method;
		private $parameters;
		private $file;
		private $fileOverride;
		private $Lang;
		private $WhiteList;
		private $BlackList;
		
		//$databases = connect databases
		//$listenEventGet = GET
		public function __construct($databases,$listenEventGet) {
			
			unset($_GET);
			
			if(is_array($listenEventGet)) {
				
				if(count($listenEventGet) > 0) {
					
					$this->module = (isset($listenEventGet['module'])) ? $listenEventGet['module'] : 'front';
					$this->controller = (isset($listenEventGet['controller'])) ? $listenEventGet['controller'] : 'auth';
					$this->method = (isset($listenEventGet['method'])) ? $listenEventGet['method'] : 'show';
					$this->parameters = (isset($listenEventGet['parameters'])) ? $listenEventGet['parameters'] : null;
					
				}
				else {
					
					$this->module = 'front';
					$this->controller = 'auth';
					$this->method = 'login';
					$this->parameters = null;
				}
			}
			else{
				$this->module = 'front';
				$this->controller = 'auth';
				$this->method = 'login';
				$this->parameters = null;
			}
			
			//-----------------------------------------------
			$this->module = strtolower($this->module);
			$this->controller = strtolower($this->controller);
			$this->method = strtolower($this->method);
			$this->parameters = strtolower($this->parameters);
			//-----------------------------------------------
				
			$this->file = ROOT_PATH . sprintf('/%s/%s/%s%s.php','modules',$this->module,ucfirst($this->controller),self::PREFIX);
			$this->fileOverride = ROOT_PATH . sprintf('/%s/%s/overrides/%s%s.php','modules',$this->module,ucfirst($this->controller),'Override');

			//------------------------------------------------------------------------------
			//redirect !
			//------------------------------------------------------------------------------

			$this->updatePath();
			
			$redirect = implode('/',$this->path);
			
			
			if(count($listenEventGet) < 0) {
				header('Status: 301 Moved Permanently', false, 301);
				header(sprintf("Location:%s",$redirect));
				die();
			}

			
			if(!isset($listenEventGet['module'])) {
				header('Status: 301 Moved Permanently', false, 301);
				header(sprintf("Location:%s",$redirect));
				die();
			}

			if(!isset($listenEventGet['controller'])) {
				header('Status: 301 Moved Permanently', false, 301);
				header(sprintf("Location:%s",$redirect));
				die();
			}
			
			switch(strtr($this->module, array('/' => ''))) {
				case 'back':
				case 'front':
				case 'api':
				case 'websocket':
					$this->core['path'] = array();
					$this->core['path']['module'] = $this->module;
					$this->core['path']['controller'] = $this->controller;
					$this->core['path']['method'] = $this->method;
					$this->core['path']['parameters'] = $this->parameters;
					$listenEventGet['file'] = $this->file;
					
					if(file_exists($this->fileOverride)) {
						$listenEventGet['fileOverride'] = $this->fileOverride;
					}
					
					$this->core['redirect'] = $redirect;
					$this->core['original'] = $listenEventGet;
					$this->process();
				break;
				default:
					header('Status: 301 Moved Permanently', false, 301);
					header(sprintf("Location:%s",$redirect));
					die();
				break;
			}
		}
		
		private function updatePath() {


			$_SERVER['DOCUMENT_ROOT'] = strtr($_SERVER['DOCUMENT_ROOT'],array('/' => '\\'));
			$process = strtr(ROOT_PATH,array($_SERVER['DOCUMENT_ROOT'] => ''));
			$process = strtr($process,array('\\' => '/'));
			
			$this->path = array();
			$this->path[] = $process;
			$this->path[] = $this->module;
			$this->path[] = $this->controller;
			
			if($this->method != 'show') {
				$this->path[] = $this->method;
			}
			
			if($this->parameters != null) {
				$this->path[] = $this->parameters;
			}
		}
		
		private function setWhiteList($WhiteList) {
			$this->WhiteList = $WhiteList;
		}
		
		private function process() {
			$this->loader();
		}
		
		private function callErrorPage($type = 'default') {

			novax_log(sprintf('callErrorPage %s',$type),$this->path);
			$this->controller = 'errors';
			$this->method = 'show';
			$this->parameters = 404;

			$this->updatePath();
			
			$this->core['path'] = array();
			$this->core['path']['module'] = $this->module;
			$this->core['path']['controller'] = $this->controller;
			$this->core['path']['method'] = $this->method;
			$this->core['path']['parameters'] = $this->parameters;
			
			$this->file = sprintf('%s/%s/%s%s.php','modules',$this->module,ucfirst($this->controller),self::PREFIX);
			
			$this->core['path']['file'] = $this->file;

			include_once(ROOT_PATH .'/modules/PageController.php');
			
			$_O = new PageCore($this->databases, $this->Lang, $this->core);
			$_O->setCode(404);
			$_O->setMessage('Le site est en actuellement en maintenance.');
			
			$RC = new ReflectionClass('PageCore');
			$RM = new ReflectionMethod('PageCore', 'show');
			$RM->invoke($_O);
			die();
		}

		private function loader() {

			if((PROFILE == 'MASTER') && !in_array($_SERVER['REMOTE_ADDR'], $this->WhiteList)) {
				$this->callErrorPage('loader MASTER');		
			}

			
			if(file_exists($this->file)) {
				

				switch($this->module) {
					case 'api':
						include_once(CORE_PATH.'/controllers/ApiController.php');
					break;
				}

				if(file_exists($this->fileOverride)) {
					$this->core['override'] = true;
					include_once($this->file);
					include_once($this->fileOverride);
					$_C = $this->controller.'Override';
				}
				else{
					$this->core['override'] = false;
					include_once($this->file);
					$_C = $this->controller.'Core';
				}
				

				if(class_exists($_C)) {

					// permet de tcheck si les methods existent ou non !!!
					//ajoute aussi des parametres dans le constructeur !
					$_O = new $_C($this->databases, $this->Lang, $this->core);
					$RC = new ReflectionClass($_C);
				}
				else {
					$this->callErrorPage(sprintf('loader class %s doesn t exist',$_C));	
				}
				
				//-------------------------------------------------------------------------------------
				// CALL METHOD DEFAULT : show
				//-------------------------------------------------------------------------------------
				if(is_null($this->method)) {
					$RM = new ReflectionMethod($_C, 'show');
				}
				else {
					if($RC->hasMethod($this->method)) {
						$RM = new ReflectionMethod($_C, $this->method);
										
						//on call que les methods public
						// var_dump($_C);
						// var_dump($this->method);
						if($RM->isPublic()) {
							$RM->invoke($_O);
						}
						else {
							$this->callErrorPage(sprintf('loader method %s is not public',$this->method));	
						}
						
					}
					else {
						$this->callErrorPage(sprintf('loader method %s doesn t exist',$this->method));
					}
				}
			}
			else {

				$this->callErrorPage(sprintf('loader controller %s doesn t exist',$this->file));	
			}	
		}
	}
	
	
	function novax_log($subject,$array) {
		$date = date('Y-m-d h:i:s',time());
		$file = ROOT_PATH .'/logs/' . date('Y-m-d',time()) . '-default.log';
		
		if(!file_exists($file)) {
			$f = fopen($file, "x+");
			fputs($f, ' ');
			fclose($f);
		}
		
		$text = sprintf("%s[%s] : %s",$date,$subject,implode(' , ',$array)).PHP_EOL;
		$FilePointer = fopen($file, 'a+');
		
		fwrite($FilePointer, $text);
		fclose($FilePointer);
	}
	
?>