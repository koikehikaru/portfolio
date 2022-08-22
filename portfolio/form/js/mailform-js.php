<?php


session_start();
error_reporting( E_ALL );




mb_language( 'uni' );
mb_internal_encoding( 'UTF-8' );




require_once( dirname( __FILE__ ) .'/class.mailform-js.php' );
$responsive_mailform_js = new Mailform_Js();




?>