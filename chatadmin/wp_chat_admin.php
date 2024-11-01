<?php
if (!defined('CHAT_VERSION')) exit('No direct script access allowed');
class Chat_Admin extends topcmmRequestRemote{
	private $table_name;

	function Chat_Admin(){
		$this->__construct();
	}
	
	function __construct(){
    	global $wpdb;
		$this->table_name = $wpdb->prefix . "123flashchat";
	}

	function chat_active(){
		register_activation_hook(__FILE__,'jal_install');
	}

	function chat_deactivate(){
		global $wpdb;
		// remove activation check & version
		delete_option( 'widget_chat_activation' );
		// delete 123flashchat table
		$deletechat = $wpdb->query( "DROP TABLE $this->table_name" );
		//delete 123flashchat page
		$wpdb->query("DELETE FROM $wpdb->posts WHERE post_name = '123flashchat'");	
	}

	function my_chat_admin_init(){
		wp_register_script('myPluginScript', CHAT_PLUGIN_URL.'/js/main_settings.js');
	}

	function wm_add_chat_page(){
		add_object_page('123FlashChat', '123FlashChat', 'manage_options', 'chat_settings', array( $this, 'chat_settings_page' ), CHAT_PLUGIN_URL.'/image/123flashchat.png', 6);
		$chat_settings = add_submenu_page( 'chat_settings', '123FlashChat', 'Chat Settings', 'manage_options', 'chat_settings', array( $this, 'chat_settings_page' ));
		$admin_panel = add_submenu_page( 'chat_settings', '123FlashChat', 'Admin Panel', 'manage_options', 'chat_adminpanel', array( $this, 'chat_adminpanel_page' ));
		add_action( "admin_print_scripts-$chat_settings", array( $this, 'my_chat_admin_styles' ), CHAT_VERSION );
	}

	function my_chat_admin_styles(){
		/*
		* It will be called only on your plugin admin page, enqueue our script here
		*/
		wp_enqueue_script('myPluginScript');
	}

	function chat_settings_page(){
		global $wpdb;
		$updateChat = $this->updateChatSetting();
		$getChat = $wpdb->get_results( "SELECT * FROM {$this->table_name} where id = '1'" );
		$get_chat_settings = $getChat[0];
		do_action( 'option_tree_admin_header' );
		include (CHAT_PLUGIN_DIR."/chat_settings.php");
	}

	function chat_adminpanel_page(){
		global $wpdb;
		$getChat = $wpdb->get_results( "SELECT * FROM {$this->table_name} where id = '1'" );
		$get_chat_settings = $getChat[0];
		do_action( 'option_tree_admin_header' );
		include (CHAT_PLUGIN_DIR."/chat_adminpanel.php");
	}

	function chat_load(){
		add_thickbox();
	    wp_enqueue_script( 'jquery-table-dnd', CHAT_PLUGIN_URL.'/js/main_settings.js', array('javascript'), CHAT_VERSION );
	}

	function save_Local_Settings(){
	    if(!$_REQUEST){
	          $error_Info .= "Save Failed";
	          return $error_Info;
	    }
	    $data['fc_extendserver'] = 0;
	    $data['fc_servertype'] = $_REQUEST['fc_servertype'];
	    $client_location = $_REQUEST['fc_client_loc'];
	    if($client_location){
	    	$data['fc_client_loc'] = $client_location . (substr($client_location,-1,1) != '/' ? '/' : '');
	    	$serverinfo = $this->getServerHostPortLocal($data['fc_client_loc']);
	    	$data['fc_server_host'] = !empty($_REQUEST['fc_server_host']) ? $_REQUEST['fc_server_host'] : (empty($serverinfo['fc_server_host']) ? $_SERVER['HTTP_HOST'] : $serverinfo['fc_server_host']) ;
	    	$data['fc_server_host_s'] = empty($serverinfo['fc_server_host_s']) ? $data['fc_server_host'] : $serverinfo['fc_server_host_s'];
	    	$data['fc_server_host_h'] = empty($serverinfo['fc_server_host_h']) ? $data['fc_server_host'] : $serverinfo['fc_server_host_h'];
	    	if(!is_numeric($_REQUEST['fc_server_port']) || strpos($_REQUEST['fc_server_port'] ,".")!==false){
	    		$data['fc_server_port'] = empty($serverinfo['fc_server_port']) ? ( $data['fc_servertype'] ==1 ? '51212' : '51127') : $serverinfo['fc_server_port'] ;
	    	}else{
	    		$data['fc_server_port'] = $_REQUEST['fc_server_port'];
	    	}
	    	if(!is_numeric($_REQUEST['fc_server_port_h']) || strpos($_REQUEST['fc_server_port_h'] ,".")!==false){
	    		$data['fc_server_port_h'] = empty($serverinfo['fc_server_port_h']) ? ( $data['fc_servertype'] == 1 ? '31212' : '35555') : $serverinfo['fc_server_port_h'] ;
	    	}else{
	    		$data['fc_server_port_h'] = $_REQUEST['fc_server_port_h'];
	    	}
	    	$data['fc_server_port_s'] = empty($serverinfo['fc_server_port_s']) ? ( $data['fc_servertype'] == 1 ? '51213' : '51128') : $serverinfo['fc_server_port_s'] ;
	    	$data['fc_api_url'] = 'http://' . $data['fc_server_host'] . ':' . $data['fc_server_port_h'] . '/';
	    }else{
	    	$data['fc_server_host'] = $_SERVER['HTTP_HOST'];
	    	$data['fc_server_host_h'] = $data['fc_server_host_s'] = $data['fc_server_host'];
	    	if($data['fc_servertype'] == 1){
	    		$data['fc_server_port_h'] = '31212';
	    		$data['fc_server_port_s'] = '51213';
	    		$data['fc_server_port'] = '51212';
	    	}else{
	    		$data['fc_server_port_h'] = '35555';
	    		$data['fc_server_port_s'] = '51128';
	    		$data['fc_server_port'] = '51127';
	    	}
	    	$data['fc_api_url'] = 'http://' . $data['fc_server_host'] . ':' . $data['fc_server_port_h'] . '/';
	    	$data['fc_client_loc'] = $data['fc_api_url'];
	    }
	    if($data['fc_servertype'] == 1){
	        $data['fc_clienttype'] = 0;
	    }else{
	        $data['fc_clienttype'] = $_REQUEST['fc_clienttype'];
	    }
	    $s_own_loc = 1;
		$c_own_loc = 1;
		$da = 1;
		if($_REQUEST['server_loc']){
			$c_own_loc = validate_client($data['fc_client_loc']);
			$da = validate_data_api($data['fc_api_url'] . 'online.js');
			if($c_own_loc || $da){
				$error_Info .= 'Chat Server host or port is configured incorrectly';
				return $error_Info;
			}
		}else{
			$c_own_loc = validate_client($data['fc_client_loc']);
			if($c_own_loc){
				$error_Info .= 'Chat Server http_port or Client location is configured incorrectly';
				return $error_Info;
			}
		}
		$check = validate_client('http://'.parse_url($data['fc_client_loc'], PHP_URL_HOST).':'.$data['fc_server_port_h'].'/');
		if($check){
			$error_Info .= 'Client Location is configured incorrectly';
			return $error_Info;
		}
		
		$getPublic = $this->getPublic();	
	    $getPost = $data + $getPublic;	
	    $updatechat = $this->UpdateChatSettingTabel($getPost);
	    return $updatechat;
	}

	function save_Host_Settings(){
	    if(!$_REQUEST){
	        $error_Info .=  "Save Failed";
	        return $error_Info;
	    }
	    $data['fc_extendserver'] = 1;	    
	    $data['fc_client_loc'] = $_REQUEST['fc_client_loc'] ? ($_REQUEST['fc_client_loc'] . (substr($_REQUEST['fc_client_loc'],-1,1) != '/' ? '/' : '')) : '';
	    if(empty($data['fc_client_loc'])){
	    	$error_Info .= "Client Location is Empty!";
	    	return $error_Info;
	    }
	    $serverinfo = $this->getServerHostPortHost($data['fc_client_loc']);
	    if($serverinfo){
	    	$data['fc_server_host'] = empty($serverinfo['fc_server_host']) ? parse_url($data['fc_client_loc'], PHP_URL_HOST) : $serverinfo['fc_server_host'];
	    	$data['fc_server_host_s'] = empty($serverinfo['fc_server_host_s']) ? '' : $serverinfo['fc_server_host_s'];
	    	$data['fc_server_host_h'] = empty($serverinfo['fc_server_host_h']) ? '' : $serverinfo['fc_server_host_h'];
	    	$data['fc_server_port'] = empty($serverinfo['fc_server_port']) ? '' : $serverinfo['fc_server_port'];
	    	$data['fc_server_port_h'] = empty($serverinfo['fc_server_port_h']) ? '' : $serverinfo['fc_server_port_h'];
	    	$data['fc_server_port_s'] = empty($serverinfo['fc_server_port_s']) ? '' : $serverinfo['fc_server_port_s'];
	    	$data['fc_api_url'] = 'http://' . $data['fc_server_host'] . '/';
	    	$data['fc_group'] = substr(parse_url($data['fc_client_loc'], PHP_URL_PATH),1,-1);
	    	$c_own_loc = validate_client($data['fc_client_loc']);
	    	$da = validate_data_api($data['fc_api_url'] . 'online.js?group=' . $data['fc_group']);
	    	if($c_own_loc || $da){
	    		$error_Info .= "Client Location is configured incorrectly";
	        	return $error_Info;
	    	}
	    }else{
	    	$error_Info .= "Client Location is configured incorrectly";
	        return $error_Info;
	    }
	    $data['fc_clienttype'] = $_REQUEST['fc_clienttype'];
	    //public
	    $getPublic = $this->getPublic($_REQUEST);
	    $getPost = $data + $getPublic;
		$updatechat = $this->UpdateChatSettingTabel($getPost);
	    return $updatechat;
	}

	function save_Free_Settings(){
	    if(!$_REQUEST){
			$error_Info .=  "Save Failed";
	        return $error_Info;
	    }
	    $data['fc_extendserver'] = "2";
	    $room = trim($_REQUEST['fc_room']);
		if($room != ""){
			preg_match("/\w+$/", $room,$matches);
			if(strlen($room) != strlen($matches[0])){
				$error_Info .= "Chat Room can only use numbers,letters and underline!";
				return $error_Info;
			}
			$data['fc_room'] = $room;
		}else{
			$data['fc_room'] = 'Lobby';
		} 
	    //public
	    $getPublic = $this->getPublic($_REQUEST);
		$getPost = $data + $getPublic;
		$updatechat = $this->UpdateChatSettingTabel($getPost);
	    return $updatechat;
	}

	function updateChatSetting(){
		global $wpdb;
		$table_name = $wpdb->prefix . "123flashchat";
		if($_GET['action'] == "setchat"){
			switch ($_REQUEST['fc_extendserver']){
		        case 0:
		            $report_Info = $this->save_Local_Settings();
		            break;
		        case 1:
		            $report_Info = $this->save_Host_Settings();
		            break;
		        default:
		            $report_Info = $this->save_Free_Settings();
		    }
	//	    $updatechat = $this->UpdateChatSettingTabel($report_Info);
		    if(!empty($report_Info)){
		    	return $report_Info;
		    }
		}
	}

	function UpdateChatSettingTabel($report_Info){
		global $wpdb;
		$getvar = "";
		$chattable = $wpdb->update( $this->table_name, $report_Info,array('id' => "1"));
		$n = 0;
		foreach ( $report_Info as $key => $value){
			if($n == 0){
				$getvar .= "$key='".$value."'";
			}else{
				$getvar .= " and $key='".$value."'";
			}
			$n = 2;
		}
	    $updated = $wpdb->get_var("SELECT id FROM {$this->table_name} WHERE $getvar");
	
	    if($updated){
			$updatechat = "Update success!";
	    }else{
	    	$updatechat = "Update Fail!";
	    }
	    return $updatechat;
	}

	function getPublic(){
		//local
	    $data['fc_room_list'] = $_REQUEST['fc_room_list'] ? "1" : "0";
	    $data['fc_user_list'] = $_REQUEST['fc_user_list'] ? "1" : "0";
		//    $fc_popup = isset($_REQUEST['fc_popup']) ? "1" : "0";
	    $data['fc_fullscreen'] = $_REQUEST['fc_fullscreen'];

	    if(!is_numeric($_REQUEST['fc_client_width'] ) || strpos($_REQUEST['fc_client_width'] ,".")!==false){
	        $data['fc_client_width'] = 750;
	    }else{
	        $data['fc_client_width'] = $_REQUEST['fc_client_width'];
	    }
	    if(!is_numeric($_REQUEST['fc_client_height'] ) || strpos($_REQUEST['fc_client_height'] ,".")!==false){
	        $data['fc_client_height'] = 642;
	    }else{
	        $data['fc_client_height'] = $_REQUEST['fc_client_height'];
	    }
	
	    $data['fc_client_lang'] = $_REQUEST['fc_client_lang'];
	    if ($_REQUEST['fc_client_skin']){
	        $data['fc_client_skin'] = $_REQUEST['fc_client_skin'];
	    }else{
	        $data['fc_client_skin'] = 'default';
	    }
		return $data;
	}

	function getServerHostPortLocal($client_location){
		$content = topcmmRequestRemote::requestRemote($client_location.'htmlchat/config/config.js',1);
		if ($content){
			//server host
			preg_match('/init_host = "([a-zA-Z0-9.]*)";/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_host'] = $matches[1];
			}else{
				preg_match('/init_host="([a-zA-Z0-9.]*)";/', $content, $matches);
				if(isset($matches[1])){
					$data['fc_server_host'] = $matches[1];
				}else{
					$data['fc_server_host'] = '';
				}
			}
			//server port
			preg_match('/init_port = ([0-9]*);/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_port'] = $matches[1];
			}else{
				$data['fc_server_port'] = '';
			}
			//server host s
			preg_match('/init_host_s = "([a-zA-Z0-9.]*)";/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_host_s'] = $matches[1];
			}else{
				preg_match('/init_host_s="([a-zA-Z0-9.]*)";/', $content, $matches);
				if(isset($matches[1])){
					$data['fc_server_host_s'] = $matches[1];
				}else{
					$data['fc_server_host_s'] = '';
				}
			}
			//server port s
			preg_match('/init_port_s = ([0-9]*);/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_port_s'] = $matches[1];
			}else{
				$data['fc_server_port_s'] = '';
			}
			//server host h
			preg_match('/init_host_h = "([a-zA-Z0-9.]*)";/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_host_h'] = $matches[1];
			}else{
				preg_match('/init_host_h="([a-zA-Z0-9.]*)";/', $content, $matches);
				if(isset($matches[1])){
					$data['fc_server_host_h'] = $matches[1];
				}else{
					$data['fc_server_host_h'] = '';
				}
			}
			//server port h
			preg_match('/init_port_h = ([0-9]*);/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_port_h'] = $matches[1];
			}else{
				$data['fc_server_port_h'] = '';
			}
			return $data;
		}else{
			return false;
		}
	}
	
	function getServerHostPortHost($client_location){
		$content = topcmmRequestRemote::requestRemote($client_location,1);
		if ($content){
			//server port
			preg_match('/init_port=([0-9]*)/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_port'] = $matches[1];
			}
			//server port s
			preg_match('/init_port_s=([0-9]*)/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_port_s'] = $matches[1];
			}
			//server port h
			preg_match('/init_port_h=([0-9]*)/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_port_h'] = $matches[1];
			}
			//server host
			preg_match('/init_host=([a-zA-Z0-9.]*)/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_host'] = $matches[1];
			}
			//server host s
			preg_match('/init_host_s=([a-zA-Z0-9.]*)&/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_host_s'] = $matches[1];
			}
			//server host h
			preg_match('/init_host_h=([a-zA-Z0-9.]*)&/', $content, $matches);
			if(isset($matches[1])){
				$data['fc_server_host_h'] = $matches[1];
			}
			return $data;
		}else{
			return false;
		}
	}
}
?>