<?php

	class Dispatcher extends Themes{
		const PREFIX = 'Controller';
		
		public $Core = array();
		private $Databases;
		private $Page;
		private $module;
		private $File = array();
		private $Path = array();
		private $Controller;
		private $Method;
		private $Param;
		private $Lang;
		private $WhiteList;
		private $BlackList;
		
		public function __construct($Databases, $Controller, $module, $Lang, $WhiteList) {
			

			if((!isset($_GET['module']) || !isset($_GET['controller']))) {
				header('Status: 301 Moved Permanently', false, 301);
				header(sprintf('Location: %s/Auth/Login/', substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/', 1)),$_PARAMS['MODULE'],$_PARAMS['CONTROLLER']));
				die();
			}
			
			switch(strtr($module, array('/' => ''))) {
				case 'back':
					$this->Core['process'] = 'back';
					$this->module = strtolower($Controller);
					$this->Page = ucfirst(strtr($_GET['method'], array('/' => '')));
					$this->Method = (isset($_GET['param']) && (strlen(trim($_GET['param'])) > 0)) ? $_GET['param'] : 'show';

					break;
				case 'api':
					$this->Core['process'] = 'api';
					$this->module = strtolower($Controller);
					$this->Page = ucfirst(strtolower(strtr($_GET['method'], array('/' => ''))));
					$this->Page = empty($this->Page) ? '' : $this->Page;
					$this->Method = (isset($_GET['param']) && (strlen(trim($_GET['param'])) > 0)) ? $_GET['param'] : 'show';
					$this->Method = empty($this->Page) ? 'show' : $this->Method;

					break;
				
				case '':
					$this->Core['process'] = 'front';
					$this->module = strtolower($_PARAMS['MODULE']);
					$this->Page = ucfirst($_PARAMS['CONTROLLER']);
					$this->Method = 'show';
					break;
				
				default:
					$this->Core['process'] = 'front';
					$this->module = strtolower($module);
					$this->Page = ucfirst((!isset($Controller) ? 'Login' : $Controller));
					$this->Method = (isset($_GET['method']) && (strlen(trim($_GET['method'])) > 0)) ? $_GET['method'] : null;
			}
			
			$this->moduleCore($this->module,$this->Page);


			if($this->Core['process'] == 'api') {
				if($this->Core['mode'] == 'core') {
					$this->Controller = sprintf('%s%s%s', $this->Page, $this->Core['mode'] ,'Api');
				}else{
					$this->Controller = sprintf('%s%s', $this->Page, 'Api');
				}
				
			}else{
				if($this->Core['mode'] == 'core') {
					$this->Controller = sprintf('%s%s', $this->Page, $this->Core['mode']);
				}else{
					$this->Controller = sprintf('%s%s', $this->Page, self::PREFIX);
				}
			}

			$this->Param = (isset($_GET['param']) && (strlen(trim($this->Param)) > 0)) ? $_GET['param'] : null;
			
			$this->Lang = $Lang;
			$this->Databases = $Databases;

			$this->Core['Path'] = $this->Path;
			$this->Core['File'] = $this->File;
			$this->Core['Module'] = $this->module;
			$this->Core['Page'] = $this->Page;
			$this->Core['Method'] = $this->Method;
			$this->Core['Param'] = $this->Param;
			$this->Core['WhiteList'] = $this->WhiteList;

			$this->setWhiteList($WhiteList);

			if(strtolower($this->Core['Page']) === 'themes') {
				$this->processTheme($this->Core);
			}else{
				$this->process();
			}
		}
		
		public function setWhiteList($WhiteList) {
			$this->WhiteList = $WhiteList;
		}
		
		private function log() {
			$Message = sprintf('[%s] IP:%s - FILE:%s - ', date('Y-m-d H:i:s'), $_SERVER['REMOTE_ADDR'], $this->File['path']);
			
			if(!is_null($this->Method)) {
				$Type = 'NoType';
				
				if(class_exists(($this->Controller))) {
					$RC = new ReflectionClass($this->Controller);

					if($RC->hasMethod($this->Method)) {
						$Method = new ReflectionMethod($this->Controller, $this->Method);
						
						if($Method->isPrivate()) {
							$Type = 'Private';
						}
						elseif($Method->isProtected()) {
							$Type = 'Protected';
						}
						elseif($Method->isStatic()) {
							$Type = 'Static';
						}
						elseif($Method->isPublic()) {
							$Type = 'Public';
						}
					}
				}
				
				$Message .= sprintf('METHOD:%s {%s} - ', $this->Method, $Type);
			}
		
			if(!is_null($this->Param)) {
				$Message .= sprintf('PARAM : %s - ', $this->Param);
			}
			
			$Message .= sprintf('URL: %s', $_SERVER['REQUEST_URI']).PHP_EOL;
			
			$FilePointer = fopen(INCLUDE_PATH.'/logs/errors_access.log', 'a+');
			
			fwrite($FilePointer, $Message);
			fclose($FilePointer);
		}
		
		private function process() {
			$this->loader();
		}
		
		private function moduleCore() {

			$this->Core['mode'] = 'core';
			$this->Path['core'] = sprintf('%s/%s/%s/', MOD_PATH ,$this->module, 'core');
			if($this->Core['process'] == 'back') {
				$this->Path['core'] = sprintf('%s/%s/%s/', MOD_PATH ,$this->module, 'core/back');
			}

			$this->Path['path'] = $this->Path['core'];
			$this->Path['override'] = sprintf('%s/%s/%s/', MOD_PATH ,$this->module, 'override');

			
			if($this->Core['process'] == 'back') {
				$this->Path['override'] = sprintf('%s/%s/%s/', MOD_PATH ,$this->module, 'override/back');
			}


			$this->File['core'] = sprintf('%s%s%s.php', $this->Path['core'] , $this->Page, self::PREFIX);

			if($this->Core['process'] == 'api') {
				$this->File['core'] = sprintf('%s%s%s.php', $this->Path['core'] , $this->Page, 'Api');
			}

			$this->File['path'] = $this->File['core'];
			$this->File['override'] = sprintf('%s%s%s.php', $this->Path['override'], $this->Page, self::PREFIX);

			if($this->Core['process'] == 'api') {
				$this->File['override'] = sprintf('%s%s%s.php', $this->Path['override'] , $this->Page, 'Api');
			}

			
			if(file_exists($this->File['override'])) {
				$this->File['path'] = $this->File['override'];
				$this->Path['path'] = $this->Path['override'];
				$this->Core['mode'] = 'override';
			}
		}

		public function callErrorPage() {

			$this->module = strtolower('errors');
			$this->Page = ucfirst('Page');
			$this->Method = 'show';

			include_once(MOD_PATH .'/errors/core/PageController.php');
			
			$_O = new PageCore($this->Databases, $this->Lang, $this->Core);
			$_O->setCode(404);
			$_O->setMessage('Le site est en actuellement en maintenance.');
			
			$RC = new ReflectionClass('PageCore');
			$RM = new ReflectionMethod('PageCore', 'show');
			$RM->invoke($_O);
			$this->log();
			die();
		}

		private function loader() {

			// var_dump($this->Core);
			// die();
			if((PROFILE == 'MASTER') && !in_array($_SERVER['REMOTE_ADDR'], $this->WhiteList)) {
				$this->callErrorPage();		
			}

			if(file_exists($this->File['path'])) {
				

				switch($this->Core['process']) {
					case 'api':
						include_once(CORE_PATH.'/controllers/ApiController.php');
					break;
				}

				//----------------------------------------------------------------
				switch($this->Core['mode']) {
					case 'override':
						include_once($this->Core['File']["core"]);
					break;
				}

				include_once($this->File['path']);

				$_C = $this->Controller;
				if(class_exists($_C)) {

					//------------------------------------------------------------------------------

						$_TIME = date('ymd_hsi');

						$_INFOS = array(
							'controller' => $this->Controller,
							'method' => $this->Method,
							'token' => (isset($_GET['token']) ? $_GET['token'] : '')
						);

						$this->Databases['DATABASE_MYSQL_API']->timerRequest($_TIME,$_INFOS);

					//------------------------------------------------------------------------------

					$_O = new $_C($this->Databases, $this->Lang,$this->Core);
					$RC = new ReflectionClass($this->Controller);
				}
				else {
					$this->callErrorPage();	
				}
				
				//-------------------------------------------------------------------------------------------------------
				if(is_null($this->Method)) {
					if((PROFILE == 'MASTER' || PROFILE == 'DEV') && in_array($_SERVER['REMOTE_ADDR'], $this->WhiteList)) {
						if($RC->hasMethod('show' . ucfirst(strtolower(PROFILE)))) {
							$RM = new ReflectionMethod($this->Controller, 'showDev');
						}
						else {
							$RM = new ReflectionMethod($this->Controller, 'show');
						}
					}
					else {
						$RM = new ReflectionMethod($this->Controller, 'show');
					}
				}
				else {
					if((PROFILE == 'MASTER' || PROFILE == 'DEV') && in_array($_SERVER['REMOTE_ADDR'], $this->WhiteList)) {
						if($RC->hasMethod($this->Method . ucfirst(strtolower(PROFILE)))) {
							if(!is_null($this->Param)) {
								if(ctype_alnum($this->Param)) {
									$RM = new ReflectionMethod($this->Controller, $this->Method . ucfirst(strtolower(PROFILE)));
								}
								else {
									$this->callErrorPage();
								}
							}
							else {
								$RM = new ReflectionMethod($this->Controller, $this->Method);
							}
						}
						else {
							if($RC->hasMethod($this->Method)) {
								if(!is_null($this->Param)) {
									if(ctype_alnum($this->Param)) {
										$RM = new ReflectionMethod($this->Controller, $this->Method);
									}
									else {
										$this->callErrorPage();
									}
								}
								else {
									$RM = new ReflectionMethod($this->Controller, $this->Method);
								}
							}
							else {
								$this->callErrorPage();
							}
						}
					}
					else {
						if($RC->hasMethod($this->Method)) {
							if(!is_null($this->Param)) {
								if (ctype_alnum($this->Param)) {
									$RM = new ReflectionMethod($this->Controller, $this->Method);
								}
								else {
									$this->callErrorPage();
								}
							}else{
								$RM = new ReflectionMethod($this->Controller, $this->Method);
							}
						}
						else {
							$this->callErrorPage();
						}
					}
					
					if(PROFILE == 'PROD') {
						if($RC->hasMethod($this->Method)) {
							if(!is_null($this->Param)) {
								if(ctype_alnum($this->Param)) {
									$RM = new ReflectionMethod($this->Controller, $this->Method);
								}
								else {
									$this->callErrorPage();
								}
							}else{
								$RM = new ReflectionMethod($this->Controller, $this->Method);
							}
						}
						else {
							$this->callErrorPage();
						}
					}
				}
				
				if($RM->isPublic()) {
					$RM->invoke($_O);
				}
				else {
					$this->callErrorPage();	
				}
			}
			else {

				if($this->Core['process'] != 'back') {
					if(count($_SESSION) <= 0) {
						header('location:' . URL_PATH);
					}
				}

				$this->callErrorPage();	
			}	
		}
	}
	
?>