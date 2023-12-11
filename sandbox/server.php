<?php
    # https://chstudio.fr/2020/09/creer-un-serveur-de-websocket-en-php/
    # https://github.com/shulard/ipc-websocket-sample/blob/master/composer.json
    # https://c-mh.fr/posts/websockets-en-php-plus-simple-qu-il-n-y-parait
    # https://stackoverflow.com/questions/34466380/websockets-over-ssl
    # https://www.twilio.com/blog/create-php-websocket-server-build-real-time-even-driven-application

	require_once(__DIR__.'/application/defines.inc.php');
	require_once(__DIR__.'/application/config/config.inc.php');
	require_once(__DIR__.'/libraries/vendor/autoload.php');

    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;


	if(file_exists(__DIR__.'/translations/'. $_PARAMS['CURRENT_LANG'] .'.php')) {
		require_once(__DIR__.'/translations/'. $_PARAMS['CURRENT_LANG'] .'.php');
	}
	else {
		require_once(__DIR__.'/translations/fr.php');
	}
	
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
		require_once(ROOT_PATH.'/framework/class.themes.php'); #https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/#download
		require_once(ROOT_PATH.'/framework/class.dispatcher.php');
		require_once(ROOT_PATH.'/framework/class.websocket.php');

		$_DATABASES['DATABASE_MYSQL_API'] = DatabaseDispatcher::connect('mysql', MYSQL_HOST_API, MYSQL_LOGIN_API, MYSQL_PASSWORD_API, MYSQL_DATABASE_API, MYSQL_DATABASE_PORT);
	}

	echo sprintf("START WEBSOCKET %s LOCAL \n",$_SERVER['SERVER_FRAMEWORK']);
    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Socket($_DATABASES,$_LANG)
            )
        ),
        8088,
        '127.0.0.1'
    );

    $server->run();
?>
