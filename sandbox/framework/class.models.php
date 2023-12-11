<?php

	class Models {
		private $Model;
		private $File;
		private $Database;
		private $Lang;
		
		public function load($Database = null, $ModelName = null, $Lang = 'fr') {
			$this->Database = $Database;
			$this->Lang = $Lang;
			
			$this->Model = ucfirst(strtolower($ModelName));
			
			if(!is_null($ModelName)) {
				$this->File = sprintf('/models.%s.php', $this->Model);
				
				if(file_exists(MODEL_PATH.$this->File)) {
					include_once(MODEL_PATH.$this->File);
					
					$_C = $this->Model.'Models';
					$_O = new $_C($this->Database);
					return $_O;
				}
			}
		}
	}

?>