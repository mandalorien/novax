<?php
	
	class PDO_MYSQL extends PDO {
		private $DSN;
		private $Login;
		private $Password;

		public $_REQS = array();
		public $_timer;
		public $_counters;
		public $_infos;
		public $_device;

		public function __construct($DSN, $Login, $Password) {
			try {
				$_O = array();
				$_O[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
				$_O[PDO::MYSQL_ATTR_INIT_COMMAND] = 'SET NAMES utf8mb4';
				$_O[PDO::ATTR_STATEMENT_CLASS] =  array('myPDOStatement', array($this));
				
				parent::__construct($DSN, $Login, $Password, $_O);
			}
			catch(Exception $E) {
				var_dump($E);
				die('Connection error, check parameters.');
			}
		}

		public function setDevice($device) {
			$this->_device = $device;
		}

		public function deviceDebugHistoric($pdo,$params,$result) {

			if(!is_null($this->_device)) {

				if((((int)$this->_device->device_flags&Flags::DEVICE_DEBUG_HISTORIC) <> 0)) {
					// error_log("DEVICE : ". json_encode($this->_device));
					$_REQ = "INSERT INTO debug_historic (device_id,device_version,controller,call_date,call_parameters_get,call_parameters_post,query,json_answer,answer_date) VALUES (?, ? ,? ,? ,? ,? ,? ,? ,?)";

					$_DATA = array();
					$_DATA[] = (int)$this->_device->device_id;
					$_DATA[] = $_GET['version'];
					$_DATA[] = $_GET['controller'];
					$_DATA[] = date('Y-m-d H:i:s',($params['date_begin']));
					$_DATA[] = json_encode($_GET);
					$_DATA[] = json_encode($_POST);
					$_DATA[] = $params['execute'];
					$_DATA[] = json_encode($result);
					$_DATA[] = date('Y-m-d H:i:s',($params['date_end']));


					if(!preg_match("/debug_historic/i", $params['execute'])) {
						$_query = $pdo->prepare($_REQ);
						$_query->execute($_DATA);
					}
				}
			}
		}

		public function timerRequest($time,$infos = array()) {
			$this->_timer = $time;
			$this->_infos = $infos;
			$this->_counters = 0;
		}

		public function getTimer() {
			return $this->_timer;
		}

		public function getReqs() {
			return $this->_REQS;
		}

		public function prepare($statement, $options = NULL) {
			if(!isset($this->_REQS[$this->_timer])) {
				$this->_REQS[$this->_timer] = array();
			}

			if(!isset($this->_REQS[$this->_timer])) {
				$this->_REQS[$this->_timer][$this->_counters] = array();
			}

			$this->_REQS[$this->_timer][$this->_counters]['controller'] = $this->_infos['controller'];
			$this->_REQS[$this->_timer][$this->_counters]['method'] = $this->_infos['method'];
			$this->_REQS[$this->_timer][$this->_counters]['token'] = $this->_infos['token'];
			$this->_REQS[$this->_timer][$this->_counters]['req'] = $statement;
			$this->_REQS[$this->_timer][$this->_counters]['time_begin'] = microtime();
			$this->_REQS[$this->_timer][$this->_counters]['date_begin'] = time();

			return parent::prepare($statement);
		}
	}
	
	class myPDOStatement extends PDOStatement {
		protected $pdo;
	
		protected function __construct($_pdo) {
			$this->pdo = $_pdo;
		}
	
		public function execute($bound_input_params = NULL) {

			if(isset($this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters])) {
				$_QUERY = $this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters]['req'];

				$Champs = array();
				$Valeurs = array();
				
				if(!is_null($bound_input_params)){
					foreach($bound_input_params as $Champ => $Valeur) {
						if (is_string($Champ)) {
							$Champs[] = '/:'.$Champ.'/';
						}
						else {
							$Champs[] = '/[?]/';
						}

						$Valeurs[] = '\''.$Valeur .'\'';
					}

					$this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters]['data'] = json_encode($bound_input_params);
				}else{
					$this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters]['data'] = array();
				}

				$this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters]['execute'] = preg_replace($Champs, $Valeurs, $_QUERY, 1, $Count);
				$this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters]['time_end'] = microtime();
				$this->pdo->_REQS[$this->pdo->_timer][$this->pdo->_counters]['date_end'] = time();

				$this->pdo->_counters ++;
			}

			parent::execute($bound_input_params);
		}

		public function fetch($how = NULL, $orientation = NULL, $offset = NULL) {

			$_recup = $this->pdo->_counters  - 1;

			$this->pdo->_REQS[$this->pdo->_timer][$_recup]['time_end'] = microtime();
			$this->pdo->_REQS[$this->pdo->_timer][$_recup]['date_end'] = time();

			$fetchedData = parent::fetch($how,$orientation,$offset);

			$this->pdo->deviceDebugHistoric($this->pdo,$this->pdo->_REQS[$this->pdo->_timer][$_recup],$fetchedData);

			return $fetchedData;
		}

		public function fetchAll($how = NULL, $class_name = NULL, $ctor_args = NULL) {

			$_recup = $this->pdo->_counters  - 1;

			$fetchedData = call_user_func_array(array('parent', __FUNCTION__), func_get_args());
			
			$this->pdo->_REQS[$this->pdo->_timer][$_recup]['time_end'] = microtime();
			$this->pdo->_REQS[$this->pdo->_timer][$_recup]['date_end'] = time();

			$this->pdo->deviceDebugHistoric($this->pdo,$this->pdo->_REQS[$this->pdo->_timer][$_recup],$fetchedData);
			/* Your code */
			return $fetchedData;
		}

		public function showQuery() {
			$_recup = $this->pdo->_counters - 1;
			return $this->pdo->_REQS[$this->pdo->_timer][$_recup];
		}
	}
?>