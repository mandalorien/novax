<?php
	
	class Flags {
		//---------------------------------------
		// All
		
		const DELETED = 0x00001;
		const ENABLED = 0x00002;
		
		public static function hasFlag($object_flags, $flag) {
			return (intval($object_flags & $flag)) == intval($flag);
		}

		static function getConstants() {
			$oClass = new ReflectionClass(__CLASS__);
			return $oClass->getConstants();
		}
	}
	
?>