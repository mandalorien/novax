<?php

	class Template {
		const THEME_DIR = 'public';
		const TEMPLATE_name = 'default';
		const EXTENSION_TEMPLATE = 'html';
		
		private $filename;
		private $aParser;
		private $directory;
		private $name;
		private $core;
		
		public function __construct($core,$name = null, $directory = null){
			$this->core = $core;
			$this->directory = (is_null($directory)) ? sprintf('%s\%s', ROOT_PATH, self::THEME_DIR) : sprintf('%s/%s', ROOT_PATH, $directory);
			$this->name = (is_null($name)) ? self::TEMPLATE_name : $name;
		}
		
		public function setdirectory($directory) {
			$this->directory = $directory . self::THEME_DIR;
		}

		private function load() {
			$root = sprintf("%s\%s\%s\%s\%s.%s",
				$this->directory, 
				$this->name, 
				'templates',
				$this->core['path']['module'],
				$this->filename, 
				self::EXTENSION_TEMPLATE);
				
			return @file_get_contents($root);
		}
		
		public function loadRessource($Type = 'js', $filename) {
			switch($Type) {
				case 'css_vendor':
				case 'js_vendor':
					$_PATH = sprintf('%s/%s/%s/%s', $this->directory, $this->name, 'vendor', $filename);
					break;
				break;
				default:
					$_PATH = sprintf('%s/%s/%s/%s', $this->directory, $this->name, $Type, $filename);
				break;
			}

			if(file_exists($_PATH)) {
				$_filename = $filename;
				// $_filename = explode('.', $filename);
				// $_filename[(count($_filename) - 2)] = sprintf('%s_%s', $_filename[(count($_filename) - 2)], filemtime($_PATH));
				// $_filename = implode('.', $_filename);
				
				switch($Type) {
					case 'css':
						return sprintf('<link rel="stylesheet" href="/%s/%s/%s/%s" type="text/css" media="screen" />', self::THEME_DIR, $this->name, $Type, $_filename);
						break;
					case 'css_vendor':
						return sprintf('<link rel="stylesheet" href="/%s/%s/%s/%s" type="text/css" media="screen" />', self::THEME_DIR, $this->name, 'vendor', $_filename);
						break;
						
					case 'js':
						return sprintf('<script src="/%s/%s/js/%s" type="text/javascript"></script>', self::THEME_DIR, $this->name, $_filename);
					break;
					case 'js_vendor':
						return sprintf('<script src="/%s/%s/vendor/%s" type="text/javascript"></script>', self::THEME_DIR, $this->name, $_filename);
					break;
				}
			}
			
			return '';
		}

		private function parse($Template, $aParser = array()) {
			if(floatval(phpversion()) <= 5.3) {
				if(!isset($aParser[1])) {
					$aParser[1] = null;
				}
				
				return preg_replace('#\{([a-z0-9\-_]*?)\}#Ssie', '( ( isset($aParser[\'\1\']) ) ? $aParser[\'\1\'] : \'\1\' );', $Template);
			}
			else {
				return preg_replace_callback('#\{([a-z0-9\-_]*?)\}#Ssi', function ($M) use ($aParser) {
					if(!isset($aParser[$M[1]])) {
						$aParser[$M[1]] = (!isset($_GET['DEBUG'])) ? null : $M[1];
					}
					
					return $aParser[$M[1]];
				}, $Template);
			}
		}
		
		public function display($filename, $aParser) {
			$this->filename = $filename;
			$this->aParser = $aParser;

			return $this->parse($this->load(), $this->aParser);
		}
	}

?>