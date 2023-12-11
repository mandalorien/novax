<?php
/**
 * This file is part of GOFIRST
 *
 * @license none
 *
 * Copyright (c) 2008-Present, CIIAB
 * All rights reserved.
 *
 * create 2018 by CEOS-IT
 */
 
	class REST {
		public $ContentType = 'application/json';
		public $Request = array();
		
		private $Code = 200;
		
		public function __construct() {
			switch($_SERVER['REQUEST_METHOD']) {
				case 'POST':
					$this->Request = $this->cleanData($_POST);
					break;
				
				case 'GET':
				case 'DELETE':
					$this->Request = $this->cleanData($_GET);
					break;
				
				case 'PUT':
					parse_str(file_get_contents('php://input'), $this->Request);
					$this->Request = $this->cleanData($this->Request);
					break;
				
				default:
					$this->sendResponse('', 406);
					break;
			}
		}
		
		public function getMethod() {
			return $_SERVER['REQUEST_METHOD'];
		}
		
		private function cleanData($Data) {
			$CData = array();
			
			if(is_array($Data)){
				foreach($Data as $Key => $Value) {
					$CData[$Key] = $this->cleanData($Value);
				}
			}
			else {
				if(get_magic_quotes_gpc()){
					$Data = stripslashes($Data);
				}
				
				$Data = strip_tags($Data);
				$CData = trim($Data);
			}
			
			return $CData;
		}
		
		public function sendResponse($Data, $Status = 200) {
			$this->Code = $Status;
			$this->setHeaders();
			
			echo $Data;
			exit;
		}
		
		private function getStatusMessage() {
			$Status = array(
				100 => 'Continue', 101 => 'Switching Protocols', 200 => 'OK',
				201 => 'Created', 202 => 'Accepted', 203 => 'Non-Authoritative Information',
				204 => 'No Content', 205 => 'Reset Content', 206 => 'Partial Content',
				300 => 'Multiple Choices', 301 => 'Moved Permanently', 302 => 'Found',
				303 => 'See Other', 304 => 'Not Modified', 305 => 'Use Proxy',
				306 => '(Unused)', 307 => 'Temporary Redirect', 400 => 'Bad Request',
				401 => 'Unauthorized', 402 => 'Payment Required', 403 => 'Forbidden',
				404 => 'Not Found', 405 => 'Method Not Allowed', 406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required', 408 => 'Request Timeout', 409 => 'Conflict',
				410 => 'Gone', 411 => 'Length Required', 412 => 'Precondition Failed', 
				413 => 'Request Entity Too Large', 414 => 'Request-URI Too Long', 415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable', 417 => 'Expectation Failed', 500 => 'Internal Server Error',  
				501 => 'Not Implemented', 502 => 'Bad Gateway', 503 => 'Service Unavailable',
				504 => 'Gateway Timeout', 505 => 'HTTP Version Not Supported'
			);
			
			return ($Status[$this->Code]) ? $Status[$this->Code]:$Status[500];
		}	
		
		private function setHeaders() {
			header('Access-Control-Allow-Origin: *');
			header(sprintf('HTTP/1.1 %s %s', $this->Code, $this->getStatusMessage()));
			header(sprintf('Content-Type: %s', $this->ContentType));
		}
	}	
?>
