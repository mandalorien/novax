<?php
session_start();
define('ROOT_PATH',dirname(dirname(__FILE__)));
define('PROFILE','PROD');
$_SERVER['SERVER_FRAMEWORK'] = 'http://novax';
$_PARAMS = array();
$_PARAMS['CURRENT_LANG'] = 'fr';

// PAGES FRONT
$_LIST_FP = array();
$_LIST_FP[] = 'declares';
$_LIST_FP[] = 'alliance';
$_LIST_FP[] = 'announcement';
$_LIST_FP[] = 'banned';
$_LIST_FP[] = 'buddy';
$_LIST_FP[] = 'buildings';
$_LIST_FP[] = 'changelog';
$_LIST_FP[] = 'chat';
$_LIST_FP[] = 'fleet';
$_LIST_FP[] = 'galaxy';
$_LIST_FP[] = 'imperium';
$_LIST_FP[] = 'messages';
$_LIST_FP[] = 'merchant';
$_LIST_FP[] = 'notes';
$_LIST_FP[] = 'parameters';
$_LIST_FP[] = 'officers';
$_LIST_FP[] = 'overview';
$_LIST_FP[] = 'records';
$_LIST_FP[] = 'stats';
$_SERVER['_LIST_FP'] = $_LIST_FP;
?>