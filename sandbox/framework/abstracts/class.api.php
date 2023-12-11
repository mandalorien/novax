<?php
	use Ratchet\Client\WebSocket;
	use Ratchet\RFC6455\Messaging\MessageInterface;

	abstract class Controller {
		protected $Core;
		protected $Database;
		
		protected $Parser;
		protected $Template;
		
		protected $Lang;
		
		protected $Action;
		protected $Param;
		
		protected $Attributes = array();
		
		protected $_DEFINITION = [];
		protected $_DATA = [];
		
		public function __construct($Database, $Lang ,$Core = array()) {
			$this->Core = $Core;
			$this->Database = $Database;
			$this->Lang = $Lang;
			$this->Action = (isset($_GET['method'])) ? $_GET['method'] : null;
			
			$_F = isset($_GET['folder']) ? $_GET['folder'] : null;
			//test
			switch(strtr($_F, array('/' => ''))){
				case 'back':
				case 'api':
				case 'front':
					$this->Action = (isset($_GET['method'])) ? $_GET['method'] : null;
					break;
				
				default:
					$this->Action = (isset($_GET['controller'])) ? $_GET['controller'] : null;
			}
			
			$this->Action = (!empty($this->Action)) ? strtolower($this->Action) : null;
			
			$this->Param = (isset($_GET['param'])) ? $_GET['param'] : null;
			$this->Param = (!empty($this->Param)) ? strtolower($this->Param) : null;
			
			$this->checkParam($this->Param);
			
			$this->Template = new Template();
			$this->init();
		}
		
		private function init() {
			$this->Parser = array();


			$this->Parser = $this->Lang;
			$this->Parser['TITLE'] = $this->Core['Page'];
			$this->Parser['COMPANY_NAME'] = $this->Core['Page'];
			

			$this->Parser['LANG'] = json_encode($this->Lang);
			$this->Parser['FLAGS'] = json_encode(Flags::getConstants());
			$this->Parser['USER_ID'] = (is_null($this->model('User')) ? "" : $this->model('User')['user_id']);
			$this->Parser['USER_FLAGS'] = (is_null($this->model('User')) ? "" : $this->model('User')['user_flags']);
			$this->Parser['TOKEN'] = (is_null($this->model('User')) ? "" : urldecode(Crypt::encrypt($this->model('User')['user_token'],TOKEN_WEBSITE)));
			$this->Parser['_DEVEL'] = (PROFILE == 'DEV') ? 'true' : 'false';
			$this->Parser['URL_API'] =  URL_PATH;
			$this->Parser['URL_PATH'] =  URL_PATH;
			$this->Parser['URL_WEBSOCKET'] =  URL_WEBSOCKET;
			$this->Parser['IP_ADDRESS'] = $_SERVER['REMOTE_ADDR'];
			$this->Parser['USER_NAME'] = (is_null($this->model('User')) ? '' : $this->model('User')['user_mail']);
			
			$this->Parser['STYLE'] = array();
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'bootstrap/css/bootstrap.min.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'bootstrap-icons/bootstrap-icons.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'boxicons/css/boxicons.min.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'quill/quill.snow.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'quill/quill.bubble.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'remixicon/remixicon.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css_vendor', 'simple-datatables/style.css');
			$this->Parser['STYLE'][] = $this->Template->loadRessource('css', 'style.css');
			
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'jquery.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'jquery.ui.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'jquery.maskedinput.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'popper.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'bootstrap.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'apexcharts/apexcharts.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'bootstrap/js/bootstrap.bundle.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'chart.js/chart.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'echarts/echarts.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'quill/quill.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'simple-datatables/simple-datatables.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'tinymce/tinymce.min.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js_vendor', 'php-email-form/validate.js');
			
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'api.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'main.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'class.core.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'class.manager.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'class.database.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'class.queryCore.js');
			$this->Parser['SCRIPTS'][] = $this->Template->loadRessource('js', 'class.events.js');
			

			$this->Parser['CHANNELS'] = $this->Template->display('channels', $this->Parser);

			$this->refreshWebsocket();
		}
		
		private function refreshWebsocket() {

			if(!isset($_GET['imei']) && isset($_GET['token'])) {
				if(isset($_GET['folder']) && $_GET['folder'] != 'api') {
					//-----------------------------------------------------------------------
					$loop = React\EventLoop\Factory::create();

					$connector = new Ratchet\Client\Connector($loop);
					$connection = $connector('wss://miami-dev.ciasfpa.fr/websocket')->then(
						function (Ratchet\Client\WebSocket $conn) {
							$conn->on('message', function (MessageInterface $msg) use ($conn) {
								// echo "{$msg}\n";
								error_log("refresh Websocket");
								$conn->close();
							});

							$_DATA = new stdClass();
							$_DATA->load = 'refresh';
							$conn->send(json_encode($_DATA));

						}, function (Throwable $e) {
							echo "Could not connect: {$e->getMessage()}\n";
						}
					);

					$loop->run();
				}
			}
		}

		protected function prepareDisplay() {

			$this->Parser['TITLE'] = $this->Core['Page'];
			$this->Parser['SCRIPTS'] = implode(PHP_EOL, $this->Parser['SCRIPTS']);
			$this->Parser['STYLE'] = implode(PHP_EOL, $this->Parser['STYLE']);
			
			$this->Parser['HEADER'] = $this->Template->display('header', $this->Parser);

			if((PROFILE == 'MASTER' || PROFILE == 'DEV')) {


				$_FILE = json_decode(json_encode($this->Database['DATABASE_MYSQL_API']->getReqs()));
				$this->Parser['REQUESTS_DEBUG'] = '';

				foreach($_FILE as $_NAME => $_REQS) {
					foreach($_REQS as $_INDEX => $_REQ) {
						$this->Parser['REQUESTS_DEBUG'] .= sprintf('<li class="list-group-item alert-secondary">REQ N°%s [%s][<span class="text-success">%s ms</span>]<div class="alert alert-secondary" role="alert"><code>%s</code></div></li>',
							$_INDEX,
							$_REQ->controller,
							round((($_REQ->time_end - $_REQ->time_begin) * 100000), 2),
							nl2br($_REQ->execute)
						);
					}
				}

				$this->Parser['DEVEL'] = $this->Template->display('devel', $this->Parser);

				if($this->Core['Module'] == 'auth') {
					$this->Parser['DEVEL'] = '';
				}
			}

			$this->Parser['FOOTER'] = $this->Template->display('footer', $this->Parser);
		}
		
		protected function processData(&$_DATA) {
			foreach($this->_DEFINITION as $_FIELD) {
				if(in_array($_DATA['Action'], $_FIELD['action'])) {
					if(!isset($_DATA['Parameters'][$_FIELD['field']])) {
						$_DATA['Parameters'][$_FIELD['field']] = '';
					}
					
					switch($_FIELD['type']) {
						case 'password':
							$_SESSION['random_password'] = Crypt::generate(10);
							$this->_DATA[$_FIELD['field']] = Crypt::encrypt($_SESSION['random_password'], CRYPT_TOKEN);
							break;
							
						case 'int':
							if(substr($_FIELD['field'], -5) != 'flags') {
								$this->_DATA[$_FIELD['field']] = ($_FIELD['nullable'] && (strlen(trim($_DATA['Parameters'][$_FIELD['field']])) == 0)) ? null : (int)$_DATA['Parameters'][$_FIELD['field']];
							}
							else {
								$_FLAGS = 0;
								
								if(!empty($_DATA['Parameters'][$_FIELD['field']])){
									foreach($_DATA['Parameters'][$_FIELD['field']] as $_VALUE) {
										$_FLAGS += $_VALUE;
									}
								}
								
								$this->_DATA[$_FIELD['field']] = $_FLAGS;
							}
							break;
							
						default:
							$this->_DATA[$_FIELD['field']] = ($_FIELD['nullable'] && (strlen(trim($_DATA['Parameters'][$_FIELD['field']])) == 0)) ? null : $_DATA['Parameters'][$_FIELD['field']];
					}
				}
			}
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
		
		
		protected function checkPost($POST) {
			

			# send by Devices -> decrypt data !!!
			if(isset($POST['data'])) {
				parse_str(Crypt::decrypt($POST['data'],CRYPT_TOKEN), $_TEMPO);
				# Actually is string of characters
				return $_TEMPO;
			}
			else { # send by internal -> not decrypt !
				return $POST;
			}
		}
		
		protected function getAttribute($Name) {
			return $this->Attributes[$Name]; 
		}
		
		protected function controller($Name,$type = 'api') {
			include_once(sprintf(CONTROLLER_PATH.'/%s/%sController.php',$type,$Name));

			$_C = sprintf('%s%s',ucfirst($Name), 'Controller');
			return new $_C($this->Database, $this->Lang);
		}

		protected function model($Name) {

			return (isset($_SESSION[$Name]) ? $_SESSION[$Name] : null);
		} 
		

		protected function checkSession($Name) {

			if($Name == 'User') {
				if(isset($_SESSION['User'])) {

					if(!isset($_GET['imei'])) {
						$_DATA = array();
						$_DATA[] = (int)$_SESSION['User']['user_id'];
						$_QUERY = "SELECT * FROM users WHERE user_id = ?";

						$_REQ = $this->Database['DATABASE_MYSQL_API']->prepare($_QUERY);
						$_REQ->execute($_DATA);
						$User = $_REQ->fetch(PDO::FETCH_ASSOC);
						$User['Logged'] = true;

						if($_SESSION['User']['user_token'] != $User['user_token']) {
								header('Status: 301 Moved Permanently', false, 301);
								header(sprintf('Location: %s/logout/', substr($_SERVER['REQUEST_URI'], 0, strpos($_SERVER['REQUEST_URI'], '/', 1))));
						}else{
							return $_SESSION['User'];
						}
					}else {
						return (isset($_SESSION[$Name]) ? $_SESSION[$Name] : null);
					}
				}else{
					return null;
				}
			}else{
				return (isset($_SESSION[$Name]) ? $_SESSION[$Name] : null);
			}
		} 

		protected function sql($file){
			return file_get_contents(sprintf("%s/%s.sql",SQL_PATH,$file));
		}
		
		protected function loadModuleRessource($type,$namefile) {
			$_PATH = $this->Core['Module'] . '/' . Template::THEME_DIR . '/' . $type . '/' . $namefile;

			switch($type) {
				case 'css':
					return sprintf('<link rel="stylesheet" href="/%s" type="text/css" media="screen" />', $_PATH);
					break;
				case 'js':
					return sprintf('<script src="/%s" type="text/javascript"></script>', $_PATH);
				break;
			}
		}

		protected function callErrorPage($ErrorCode) {
			include_once(CONTROLLER_PATH.'/front/HttpErrorsController.php');
			(new HttpErrorsController($this->Database, $this->Lang))->setCode($ErrorCode)->show();
		}
		
		abstract public function show();
	}
	
?>