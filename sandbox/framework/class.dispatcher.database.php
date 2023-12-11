<?php
	
	class DatabaseDispatcher {
		static function connect($Connector, $Hostname, $Login, $Password, $Database, $Port) {
			$_PATHS['CONNECTOR'] = sprintf('%s/application/databases/class.%s.php', ROOT_PATH, $Connector);
			$_PATHS['CLASS_NAME'] = sprintf('PDO_%s', strtoupper($Connector));
			
			if(!file_exists($_PATHS['CONNECTOR'])) {
				die(sprintf('Database connector does not exist ! (%s)', $_PATHS['CONNECTOR']));
			}
			
			if($Connector == 'ldap') {
				$DSN = sprintf('%s', $Hostname);
			}
			else {
				if($Connector == 'mssql') {
					if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
						$DSN = sprintf('%s:server=%s;Database=%s', 'sqlsrv', $Hostname,$Database);
					}else{
						$DSN = sprintf('%s:server=%s;Database=%s', 'sqlsrv', $Hostname,$Database);
					}
				}else{
					$DSN = sprintf('%s:host=%s;port=%s;dbname=%s', $Connector, $Hostname, $Port, $Database);
				}
			}

			include_once($_PATHS['CONNECTOR']);
			
			if($Connector == 'ldap') {
				return (new $_PATHS['CLASS_NAME']($DSN, $Login, $Password, $Database));
			}
			else {
				return (new $_PATHS['CLASS_NAME']($DSN, $Login, $Password));
			}
		}
	}
	
?>