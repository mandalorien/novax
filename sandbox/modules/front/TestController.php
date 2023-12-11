<?php
	
	class testCore extends Controller {
		private $Code;
		private $Message;
		
		public function __construct($database, $lang,$dispatcher) {
			parent::__construct($database, $lang,$dispatcher);

			$this->Attributes['User'] = $this->model('User');
			
			$this->Template = new Template();
			$this->_DATA = [];
			$this->_QUERIES = [];
		}

		private function a() {

		}

		private function b() {

		}

		private function c() {

		}

		private function cachuete() {

		}

		public function setCode($Code) {
			$this->Code = $Code;
		}
		
		public function setMessage($Message) {
			$this->Message = $Message;
		}
		
		public function show() {
			$this->Parser['TITLE'] = 'TO_SET';
			
			$this->prepareDisplay();

			if(isset($_POST['Ajax']) && (boolean)$_POST['Ajax'] == true) {

				header('Content-Type:application/json');
				$JSON = array();
				$JSON['msg'] = "Hello world";
				$JSON['error'] = true;
				$JSON['codeError'] = 404;
				echo json_encode($JSON);

			}else{
				echo $this->Template->display('test',$this->Parser);
			}
		}
	}
?>






















