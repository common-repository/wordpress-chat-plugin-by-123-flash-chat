<?php
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
require( ABSPATH . 'wp-config.php' );
include_once (ABSPATH."wp-includes/class-phpass.php");

$LOGIN_SUCCESS = 0;
$LOGIN_PASSWD_ERROR = 1;
$LOGIN_NICK_EXIST = 2;
$LOGIN_ERROR = 3;
$LOGIN_ERROR_NOUSERID = 4;
$LOGIN_SUCCESS_ADMIN = 5;
$LOGIN_NOT_ALLOW_GUEST = 6;
$LOGIN_USER_BANED = 7;

$username = isset($_GET['username']) ? trim(rawurldecode($_GET['username'])) : '';
$password = isset($_GET['password']) ? $_GET['password'] : '';
$username = mysql_escape_string($username);
$password = mysql_escape_string($password);
$password = str_replace("\\\\",'',$password);

$db_username = "user_login";
$db_password = "user_pass";
$url = "";

if(empty($username)){
	echo $LOGIN_ERROR_NOUSERID;
	exit;
}


echo checkLogin($username, $password);

function checkLogin($username,$password){
    global $wpdb;
    $LOGIN_SUCCESS = 0;
    $LOGIN_PASSWD_ERROR = 1;
    $LOGIN_NICK_EXIST = 2;
    $LOGIN_ERROR = 3;
    $LOGIN_ERROR_NOUSERID = 4;
    $LOGIN_SUCCESS_ADMIN = 5;
    $LOGIN_NOT_ALLOW_GUEST = 6;
    $LOGIN_USER_BANED = 7;

    if( empty($username)){
        return $LOGIN_ERROR_NOUSERID;
    }
	  $user = $wpdb->get_row( "SELECT * FROM ". $wpdb->prefix . "users WHERE user_login ='".$username."'" );
	  $capabilities = $wpdb->get_row( "select meta_value from ". $wpdb->prefix . "usermeta where user_id ='". $user->ID ."' and meta_key = '". $wpdb->prefix ."capabilities'");
    $admin = unserialize($capabilities->meta_value);
    $chatconfig = $wpdb->get_row( "SELECT * FROM ". $wpdb->prefix . "123flashchat" ." WHERE id ='1'" );
    if (!empty($user)){
    	$wp_hasher = new PasswordHash(8, TRUE);
        if ($user->user_pass != $password AND !$wp_hasher->CheckPassword($password, $user->user_pass)){
            return $LOGIN_PASSWD_ERROR;
        }else if ($password == ''){
            return $LOGIN_PASSWD_ERROR;
        }else{
        	$profile = "";
        	if(!empty($user->user_email) && !empty($chatconfig->fc_dis_profile) && $chatconfig->fc_dis_profile == 1 ){
        		$profile .= '|eml='.$user->user_email;
        	}
        	if($admin['administrator'] == 1){
        		return $LOGIN_SUCCESS_ADMIN.$profile;
        	}else{
        		return $LOGIN_SUCCESS.$profile;
        	}        	
        }
    }else{
        return $LOGIN_ERROR_NOUSERID;
    }
}

?>