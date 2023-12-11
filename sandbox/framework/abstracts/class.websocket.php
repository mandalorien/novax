<?php

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Session;

class Socket implements MessageComponentInterface {

    const PREFIX = 'Controller';

    private $Database;
    private $Lang;
    private $Folder;
    private $Page;
    private $File;
    private $Method;
    private $Param;
    private $Attributes;

    public function __construct($_DATABASES,$_LANGS)
    {
        $this->clients = new \SplObjectStorage;

        $this->Database = $_DATABASES;
        $this->Lang = $_LANGS;
        $this->Folder = '/api/';
    }

    private function formToken($token) {
        return strtr($token,
        array(
            ' '=>'+'
        ));
    }

    public function onOpen(ConnectionInterface $conn) {


        // Store the new connection in $this->clients
        $this->clients->attach($conn);
        echo "New connection! ({$conn->resourceId})\n";
    }

    public function onMessage(ConnectionInterface $from, $message) {
        $this->Database['DATABASE_MYSQL'] = 'connexion bdd';
        //----------------------------------------------------------
        //SEND DATA
        $object = json_decode($message);
        $result = $this->viewWebsocket($object);
        $UserID = (isset($object->user_id) ? $object->user_id : '');
        $Token = (isset($object->token) ? $object->token : '');

        echo sprintf("[%s][%s]  - user_id => %s , Token => %s\n",$object->load,$object->method,$UserID,$Token);
        foreach ($this->clients as $client) {

            if ($object->load == 'server') {
                if($client == $from) {
                    $client->send(
                        json_encode(array('date'=> date('d/m/Y H:i:s'),'Type' => ucfirst($object->method)))
                    );
                }
            }else{
                if (isset($result['showClient'])) {
                    if ($result['showClient'] == true) {
                        $client->send(
                            json_encode($result)
                        );
                    }else{
                        if($client == $from) {
                            $client->send(
                                json_encode($result)
                            );
                        }
                    }
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        unset($this->clients[$conn]);
        error_log(
            sprintf('Close[%s]\n',
                $conn->resourceId
            )
        );
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {

        echo json_encode($e) . "\n";

        // exec('service apache2 restart', $output1, $retval1);
        echo "service apache2 restart \n";
        // exec('service php7.3-fpm restart', $output2, $retval2);
        echo "service php7.3-fpm restart";

        //soucis de déconnection MYSQL
        if($e->errorInfo[0] == 'HY000' && (int)$e->errorInfo[1] == 2006) {
            $this->Database['DATABASE_MYSQL'] = 'connexion bdd';
        }

        error_log(
            sprintf('Error[%s] : %s',
                $conn->resourceId,
                json_encode($e)
            )
        );
    }

    private function viewWebsocket($object) {

        // echo json_encode($object);

        $this->Page = ucfirst($object->load);
        $this->File = sprintf('%s/%s%s.php', $this->Folder, $this->Page, self::PREFIX);
        $this->Controller = sprintf('%s%s', $this->Page, self::PREFIX);
        $this->Method = (!isset($object->method) ? 'show' : $object->method);

        if(file_exists(CONTROLLER_PATH.$this->File)) {
            // echo CONTROLLER_PATH.$this->File;

            include_once(CONTROLLER_PATH.sprintf('/%s%s.php', 'Api' , self::PREFIX));
            include_once(CONTROLLER_PATH.$this->File);
            
            $_C = $this->Controller;
            $_O = new $_C($this->Database, $this->Lang,  $object);
            $RC = new ReflectionClass($this->Controller);

            //-------------------------------------------------------------------------------------------------------

            if(is_null($this->Method)) {
                $RM = new ReflectionMethod($this->Controller, 'show');
            }
            else {
                if($RC->hasMethod($this->Method)) {
                    if(!is_null($this->Param)) {
                        if (ctype_alnum($this->Param)) {
                            $RM = new ReflectionMethod($this->Controller, $this->Method);
                        }
                        else {
                            $this->callErrorPage();
                        }
                    }else{
                        $RM = new ReflectionMethod($this->Controller, $this->Method);
                    }
                }
                else {
                    $RM = new ReflectionMethod($this->Controller, 'show');
                }
            }

            return $RM->invoke($_O);
        }else{
            return array();
        }
    }
}
?>
