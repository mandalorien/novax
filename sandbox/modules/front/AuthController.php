<?php
	
	class AuthCore extends Controller {
		private $Code;
		private $Message;
		
		public function __construct($Database, $Lang,$dispatcher) {
			parent::__construct($Database, $Lang,$dispatcher);
			
			$this->Attributes['User'] = $this->model('User');
			
			$this->Template = new Template($dispatcher);
			$this->_DATA = [];
			$this->_QUERIES = [];
		}
		
		public function login() {

			foreach($_SERVER['_LIST_FP'] as $_I => $page) {
				$this->createController($page);
			}

			// $this->addMethodInController('test','cachuete');
			// $this->addMethodInController('test','c');
			// $this->addMethodInController('test','b');
			// $this->addMethodInController('test','a');
		}
		
		public function show() {
			header('location:sandox/front/auth/login');
		}
	}
?>