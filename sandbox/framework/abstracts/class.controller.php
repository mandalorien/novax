<?php
	use Ratchet\Client\WebSocket;
	use Ratchet\RFC6455\Messaging\MessageInterface;

	abstract class Controller {
		protected $_core;
		protected $_database;
		
		protected $parser;
		protected $_template;
		
		protected $_lang;
		
		protected $Attributes = array();
		
		protected $_DEFINITION = [];
		protected $_DATA = [];
		
		public function __construct($_database, $_lang ,$_core = array()) {
			$this->_core = $_core;
			
			$this->_database = $_database;
			$this->_lang = $_lang;
			
			$this->checkParam($this->_core['path']['parameters']);
			
			$this->_template = new template($_core);
			
			//-------------------------------------------------------
			//SESSION ACCESS MANAGER PAGE BEGIN
			//-------------------------------------------------------
			$this->rulesStrictPageAccess();
			
			if(!isset($_SESSION['cuid'])) {
			
				if($this->_core['original']['module'] == 'front' && 
				   $this->_core['original']['controller'] == 'auth' &&
				   $this->_core['original']['method'] == 'login') {
						//ON FAIT RIEN
						
				}
				elseif(
					$this->_core['original']['module'] == 'back' && 
					$this->_core['original']['controller'] == 'auth' &&
					$this->_core['original']['method'] == 'login') {
						
						//ON FAIT RIEN
				}
				else{ // bah on redirige
					if(defined('NEED_AUTH')) {
						header('location:/sandbox/front/auth/login');
					}
				}
			}
			
			//-------------------------------------------------------
			//SESSION ACCESS MANAGER PAGE END
			//-------------------------------------------------------
			$this->init();
		}
		
		
		private function rulesStrictPageAccess() {
			
			//secure !
			$this->_core['original']['method'] = isset($this->_core['original']['method']) ? $this->_core['original']['method'] : 'show';
			
			switch($this->_core['original']['module']) {
				case 'front':
				
					switch($this->_core['original']['controller']) {
						case 'auth'://----------------------
						case 'alliance'://----------------------
						break;
						default:
							if(!defined('NEED_AUTH')) {
								define('NEED_AUTH',true);
							}
						break;
					}
					
				break;
				case 'back':
				
					switch($this->_core['original']['controller']) {
						case 'auth'://----------------------
						break;
						default:
							if(!defined('NEED_AUTH')) {
								define('NEED_AUTH',true);
							}
						break;
					}
					
				break;	
					
				case 'api':
				
					if(!defined('NEED_AUTH')) {
						define('NEED_AUTH',true);
					}
					
				break;	
			}
		}
		
		private function init() {
			$this->parser = array();


			$this->parser = $this->_lang;
			$this->parser['TITLE'] = $this->_core['path']['method'];
			$this->parser['COMPANY_NAME'] = $this->_core['path']['method'];
			

			$this->parser['LANG'] = json_encode($this->_lang);
			$this->parser['FLAGS'] = json_encode(Flags::getConstants());
			$this->parser['USER_ID'] = (is_null($this->model('User')) ? "" : $this->model('User')['user_id']);
			$this->parser['USER_FLAGS'] = (is_null($this->model('User')) ? "" : $this->model('User')['user_flags']);
			$this->parser['TOKEN'] = (is_null($this->model('User')) ? "" : urldecode(Crypt::encrypt($this->model('User')['user_token'],TOKEN_WEBSITE)));
			$this->parser['_DEVEL'] = (PROFILE == 'DEV') ? 'true' : 'false';
			$this->parser['URL_API'] =  $_SERVER['SERVER_FRAMEWORK'];
			$this->parser['URL_PATH'] =  $_SERVER['SERVER_FRAMEWORK'];
			$this->parser['URL_WEBSOCKET'] =  '';
			$this->parser['IP_ADDRESS'] = $_SERVER['REMOTE_ADDR'];
			$this->parser['USER_NAME'] = (is_null($this->model('User')) ? '' : $this->model('User')['user_mail']);
			
			$this->parser['STYLE'] = array();
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'bootstrap/css/bootstrap.min.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'bootstrap-icons/bootstrap-icons.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'boxicons/css/boxicons.min.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'quill/quill.snow.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'quill/quill.bubble.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'remixicon/remixicon.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css_vendor', 'simple-datatables/style.css');
			$this->parser['STYLE'][] = $this->_template->loadRessource('css', 'style.css');
			
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'jquery.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'jquery.ui.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'jquery.maskedinput.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'popper.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'bootstrap.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'apexcharts/apexcharts.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'bootstrap/js/bootstrap.bundle.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'chart.js/chart.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'echarts/echarts.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'quill/quill.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'simple-datatables/simple-datatables.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'tinymce/tinymce.min.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js_vendor', 'php-email-form/validate.js');
			
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'api.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'main.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'class._core.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'class.manager.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'class._database.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'class.query_core.js');
			$this->parser['SCRIPTS'][] = $this->_template->loadRessource('js', 'class.events.js');
			

			$this->parser['CHANNELS'] = $this->_template->display('channels', $this->parser);

		}

		protected function prepareDisplay() {

			$this->parser['TITLE'] = $this->_core['path']['method'];
			$this->parser['SCRIPTS'] = implode(PHP_EOL, $this->parser['SCRIPTS']);
			$this->parser['STYLE'] = implode(PHP_EOL, $this->parser['STYLE']);
			
			$this->parser['HEADER'] = $this->_template->display('header', $this->parser);

			if((PROFILE == 'MASTER' || PROFILE == 'DEV')) {


				$_FILE = json_decode(json_encode($this->_database['_database_MYSQL_API']->getReqs()));
				$this->parser['REQUESTS_DEBUG'] = '';

				foreach($_FILE as $_NAME => $_REQS) {
					foreach($_REQS as $_INDEX => $_REQ) {
						$this->parser['REQUESTS_DEBUG'] .= sprintf('<li class="list-group-item alert-secondary">REQ N°%s [%s][<span class="text-success">%s ms</span>]<div class="alert alert-secondary" role="alert"><code>%s</code></div></li>',
							$_INDEX,
							$_REQ->controller,
							round((($_REQ->time_end - $_REQ->time_begin) * 100000), 2),
							nl2br($_REQ->execute)
						);
					}
				}

				$this->parser['DEVEL'] = $this->_template->display('devel', $this->parser);

				if($this->_core['Module'] == 'auth') {
					$this->parser['DEVEL'] = '';
				}
			}

			$this->parser['FOOTER'] = $this->_template->display('footer', $this->parser);
		}
		

		private function checkParam($Param) {
			if(ctype_alnum($Param)) {
				if(is_numeric($Param)) {
					$this->Param = intval($Param);
				}
				else {
					$this->Param = $Param;
				}
			}
		}

		protected function getAttribute($Name) {
			return $this->Attributes[$Name]; 
		}
		
		protected function model($Name) {

			return (isset($_SESSION[$Name]) ? $_SESSION[$Name] : null);
		}
		
		protected function createController($name,$withOverride = false) {
			
			$name = strtolower($name);
			$file = ROOT_PATH . sprintf('/modules/front/%s%s.php',ucfirst($name),'Controller');
			if(file_exists($file)) {
				novax_log('impossible de créer le controller',array($file));
			}
			else {
				$cModel = ROOT_PATH . sprintf('/modules/%s%s.php','Page','Controller');
				$content = file_get_contents($cModel);

				$content = str_replace("page", $name, $content);

				$f = fopen($file, "x+");
				fputs($f,$content);
				fclose($f);
			}
			
			
			//------------------------------------------------------------
			if($withOverride) {
				$name = strtolower($name);
				$file = ROOT_PATH . sprintf('/modules/front/overrides/%s%s.php',ucfirst($name),'Override');
				if(file_exists($file)) {
					novax_log('impossible de créer le l\'override',array($file));
				}
				else {
					$cModel = ROOT_PATH . sprintf('/modules/%s%s.php','Page','Controller');
					$content = file_get_contents($cModel);
					

					$content = str_replace("page", $name, $content);
					$content = str_replace("Core", "Override", $content);
					$content = str_replace("Controller", sprintf("%sCore",ucfirst($name)), $content);

					$f = fopen($file, "x+");
					fputs($f,$content);
					fclose($f);
				}
			}
			
		}
		
		protected function addMethodInController($name,$method,$type = 'private') {
			$name = strtolower($name);
			$file = ROOT_PATH . sprintf('/modules/front/%s%s.php',ucfirst($name),'Controller');
			if(file_exists($file)) {
				$tcontent = file_get_contents($file);

				if (preg_match("/\bfunction " . $method . "\b/i", $tcontent)) {
					novax_log('impossible existance de la méthod',array($method));
				} else {
					$lines = explode(PHP_EOL,$tcontent);
					// fix : if unix file (LF)
					if(count($lines) == 1) {
						$lines = explode("\n",$tcontent);
					}
					//ajout auto à la ligne 16 !!!
					
					$content = '';
					foreach($lines as $i => $line) {
						$content .= $line . PHP_EOL;
						if($i == 15) {
							$content .= sprintf("\t\t%s function %s() {",$type,$method). PHP_EOL;
							$content .= PHP_EOL;
							$content .= sprintf("\t\t}"). PHP_EOL;
							$content .= PHP_EOL;
						}
					}
					
					$content = str_replace("page", $name, $content);
					file_put_contents($file,$content);
				}

			}
			else {
				// on pourrai appeler la method créé une classe lol
				novax_log('impossible de créer la méthod',array($method));

			}
		}
		
		abstract public function show();
	}
	
?>