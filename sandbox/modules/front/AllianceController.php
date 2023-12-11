<?php
	
	class allianceCore extends Controller {
		private $Code;
		private $Message;
		
		public function __construct($database, $lang,$dispatcher) {
			parent::__construct($database, $lang,$dispatcher);

			$this->Attributes['User'] = $this->model('User');
			
			$this->Template = new Template($dispatcher);
			$this->_DATA = [];
			$this->_QUERIES = [];
		}
		
		public function setCode($Code) {
			$this->Code = $Code;
		}
		
		public function setMessage($Message) {
			$this->Message = $Message;
		}
		
		public function test() {
			var_dump($this->fleet);
		}
		
		public function show() {
	
			$this->fleet = 1;
			$this->parser['TITLE'] = 'TO_SET';
			
			$this->prepareDisplay();

			if(isset($_POST['Ajax']) && (boolean)$_POST['Ajax'] == true) {

				header('Content-Type:application/json');
				$JSON = array();
				$JSON['msg'] = "Hello world";
				$JSON['error'] = true;
				$JSON['codeError'] = 404;
				echo json_encode($JSON);

			}else{
				echo $this->Template->display('alliance',$this->parser);
			}
		}
	}
?>