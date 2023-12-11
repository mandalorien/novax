<?php
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");
	
	require_once(__DIR__.'/application/defines.inc.php');
	require_once(__DIR__.'/application/config/config.inc.php');
	require_once(__DIR__.'/libraries/vendor/autoload.php');

	if(file_exists(__DIR__.'/translations/'. $_PARAMS['CURRENT_LANG'] .'.php')) {
		require_once(__DIR__.'/translations/'. $_PARAMS['CURRENT_LANG'] .'.php');
	}
	else {
		require_once(__DIR__.'/translations/fr.php');
	}
	//
	// *********************************************************************************************

	if(filesize(ROOT_PATH.'/application/db/mysql.inc.php') < 1) {
		header('HTTP/1.0 404 Not Found');
		die();
	}
	else {
		require_once(ROOT_PATH.'/application/db/mysql.inc.php');
		
		require_once(ROOT_PATH.'/framework/class.flags.php');
		require_once(ROOT_PATH.'/framework/class.mail.php');
		require_once(ROOT_PATH.'/framework/class.crypt.php');
		
		require_once(ROOT_PATH.'/framework/class.template.php');
		require_once(ROOT_PATH.'/framework/class.dispatcher.database.php');
		require_once(ROOT_PATH.'/framework/abstracts/class.controller.php');
		// require_once(ROOT_PATH.'/framework/class.models.php');
		// require_once(ROOT_PATH.'/framework/class.themes.php'); #https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/#download
		require_once(ROOT_PATH.'/framework/class.dispatcher.php');

		$_DATABASES['DATABASE_MYSQL_API'] = DatabaseDispatcher::connect('mysql', MYSQL_HOST_API, MYSQL_LOGIN_API, MYSQL_PASSWORD_API, MYSQL_DATABASE_API, MYSQL_DATABASE_PORT);
		new Dispatcher($_DATABASES,$_GET);
	}
?>