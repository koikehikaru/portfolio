<?php


session_start();
error_reporting( E_ALL );




mb_language( 'uni' );
mb_internal_encoding( 'UTF-8' );
date_default_timezone_set( 'Asia/Tokyo' );




require_once( dirname( __FILE__ ) .'/class.mailform.php' );
$responsive_mailform = new Mailform();




if ( isset( $_POST['token_get'] ) && $_POST['token_get'] !== '' ) {
	$responsive_mailform->javascript_action_check();
	$responsive_mailform->referer_check();
	$responsive_mailform->token_get();
	exit;
}




if ( file_exists( dirname( __FILE__ ) .'/../addon/confirm/confirm.php' ) ) {
	include( dirname( __FILE__ ) .'/../addon/confirm/confirm.php' );
}




$responsive_mailform->javascript_action_check();
$responsive_mailform->referer_check();
$responsive_mailform->token_check();

if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/enquete.php' ) ) {
	include( dirname( __FILE__ ) .'/../addon/csv-record/include/enquete.php' );
}

$responsive_mailform->post_check( 'default' );

if ( file_exists( dirname( __FILE__ ) .'/../addon/block/block.php' ) ) {
	include( dirname( __FILE__ ) .'/../addon/block/block.php' );
}

$responsive_mailform->mail_set( 'send' );
$responsive_mailform->mail_set( 'thanks' );

if ( file_exists( dirname( __FILE__ ) .'/../addon/csv-record/include/csv.php' ) ) {
	include( dirname( __FILE__ ) .'/../addon/csv-record/include/csv.php' );
}

$responsive_mailform->mail_send();
$responsive_mailform->mail_result();




?>