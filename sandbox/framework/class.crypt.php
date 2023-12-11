<?php

class Crypt {
	
	const IV = '';
	public static function encrypt($Value, $Key) {
		# PHP 5
		// return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $Key, $Value, MCRYPT_MODE_ECB));
		
		return base64_encode(openssl_encrypt($Value, 'aes128',$Key, true,self::IV));
	}

	public static function decrypt($Value, $Key) {
		# PHP 5
		// return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $Key, base64_decode($Value), MCRYPT_MODE_ECB));
		
		return trim(openssl_decrypt(base64_decode($Value), 'aes128',$Key,true,self::IV));
	}
	
	public static function generate($size)
	{
		$password = '';
		// Initialisation des caractères utilisables
		$characters = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, "a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z");

		for($i=0;$i<$size;$i++)
		{
			$password .= ($i%2) ? strtoupper($characters[array_rand($characters)]) : $characters[array_rand($characters)];
		}
			
		return $password;
	}
}
?>