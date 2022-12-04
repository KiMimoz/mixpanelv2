<?php

if(!isset($_SESSION)) //da
{ 
    session_start(); 
}

ob_start();

//$GLOBALS['current_page']=-1; / $_SESSION['current_page']=-1; / define('current_page', -1)

define('PRINC',__DIR__ . '/');
define('SYSTEM',__DIR__ . '/system/');
define('APPLICATION',__DIR__ . '/application/');
define('CONTENT',__DIR__. '/views/');
define('STYLE',CONTENT . '/general/');

include PRINC . 'system/autoload.php';
include_once APPLICATION . 'this.auto.php';
include_once APPLICATION . 'connect.auto.php';
include_once APPLICATION . 'user.auto.php';
//include_once APPLICATION . 'PHPMailerAutoload.php'; //unused
if(this::$_ENABLE_RSC==1)
{
	include_once APPLICATION . 'rcon_hl_net.inc.php';
	include_once SYSTEM . 'FTPM/class/FileManager.php';
}

this::init()->getContent();

?>