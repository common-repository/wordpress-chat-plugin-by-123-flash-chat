<?php
/*
 * admin panel check functions
*/
//System Functions
require("requestRemote.php");
define('CACHE_PATH',dirname(dirname(dirname(__FILE__))) . '/');

function getConfig(){
	global $wpdb, $current_user;
	$table_name = $wpdb->prefix . "123flashchat";
	$getChat = $wpdb->get_results( "SELECT * FROM {$table_name} where id = '1'" );
	$get_chat_settings = $getChat[0];
    return $get_chat_settings;
}

function checkSlash($path){
    $path = trim($path);
    if(substr($path,-1,1) != "/" && !empty($path)){
        $path = $path."/";
    }
    return $path;
}

/**
 * Check 123 FlashChat Data api
 * @parm $data_api   Chat Data Api
 * @return integer config status
 */
function validate_data_api($data_api){
    $da = 1;
    if(substr(topcmmRequestRemote::requestRemote($data_api,0), 9, 3) == 200){
        $da = 0;
    }
    return $da;
}

/**
 * Check 123 FlashChat Client Location
 * @parm $client_loc   Chat Client Location
 * @return integer config status
 */
function validate_client($client_loc){
    $c_own_loc = 1;
    $swf = $client_loc . 'admin_123flashchat.swf';
    if($headers = topcmmRequestRemote::requestRemote($swf,0)){
        $c_own_loc = (substr($headers, 9, 3) == '200') ? 0 : 1;
    }
    return $c_own_loc;
}

function getValid($chatSetting){
	$c_server_loc = 1;
	if($chatSetting->fc_server_host){
		if($chatSetting->fc_server_host == $_SERVER['SERVER_NAME']){
			$c_own_loc = validate_client('http://'.$chatSetting->fc_server_host.':'.$chatSetting->fc_server_port_h.'/');
			if(!$c_own_loc){
				$c_server_loc = 0;
			}
		}
	}else{
		$c_own_loc = validate_client('http://'.$_SERVER['SERVER_NAME'].':'.$chatSetting->fc_server_port_h.'/');
		if(!$c_own_loc){
			$c_server_loc = 0;
		}
	}
	return $c_server_loc;
}

function getChat($chatSetting){
	global $current_user,$wpdb;
	$chat = "";
    if(!empty ($chatSetting)){
		if ($chatSetting->fc_extendserver != "2"){
			if(($chatSetting->fc_extendserver == "1" && $chatSetting->fc_clienttype == 1) || ($chatSetting->fc_extendserver == "0" && $chatSetting->fc_servertype != 1 && $chatSetting->fc_clienttype == 1)){
				$client_name = '123flashchat.swf';
				$client_type = 'flash';
			}else{
				$client_name = 'htmlchat/123flashchat.html';
				$client_type = 'html';
			}				
			$url = $chatSetting->fc_client_loc . $client_name . "?init_host=" . $chatSetting->fc_server_host . "&init_port=" . $chatSetting->fc_server_port ;
			$url .= ($chatSetting->fc_server_host_s ? '&init_host_s='.$chatSetting->fc_server_host_s : '') . ($chatSetting->fc_server_port_s ? '&init_port_s='.$chatSetting->fc_server_port_s : '');
			$url .= ($chatSetting->fc_server_host_h ? '&init_host_h='.$chatSetting->fc_server_host_h : '') . ($chatSetting->fc_server_port_h ? '&init_port_h='.$chatSetting->fc_server_port_h : '');
			$url .= ($chatSetting->fc_extendserver == "1") ? ("&init_group=" . $chatSetting->fc_group) : "";
		}else{
			$url = "http://free.123flashchat.com/js.php?room=" . $chatSetting->fc_room . "&skin="  . $chatSetting->fc_client_skin . "&lang="  . $chatSetting->fc_client_lang;
			$client_type = 'free';
		}
		if ($room = isset($_GET['room'])?$_GET['room']:""){
			$url .= "&init_room=" . $room;
		}
		if ($current_user){
			if(!empty($current_user->user_login) && !empty($current_user->user_pass) ){
				$url .= "&init_user=" . $current_user->user_login . "&init_password=" . $current_user->user_pass;
			}
			if(!empty($current_user->user_nicename)){
				$url .= "&init_nickname=".$current_user->user_nicename;
			}
		}	
		if($chatSetting->fc_fullscreen){
			$chatSetting->fc_client_width = '100%';
			$chatSetting->fc_client_height = '100%';
		}
		
		if($client_type == 'free'){
			if($chatSetting->fc_fullscreen == 1){
				$chat .= '<script language="javascript">';
				$chat .= 'var clientWidth = document.body.clientWidth;';
				$chat .= 'var clientHeight = window.innerHeight;';			
				$chat .= 'var htmlcode = \'<script language="javascript" src="'.$url.'&width=\' + clientWidth;';
				$chat .= 'htmlcode += \'&height=\' + clientHeight + \'"></sc\';';
				$chat .= 'htmlcode += \'ript>\';';
				$chat .= 'document.write(htmlcode);';
				$chat .= '</script>';
			}else{
				$chat .= '<script language="javascript" src="'.$url.'&width='.$chatSetting->fc_client_width.'&height='.$chatSetting->fc_client_height.'">';
				$chat .= '<script>';
				$chat .= 'document.getElementById("123flashchat").style.margin = "0 auto";';
				$chat .= '</script>';
			}
		}elseif($client_type == 'html'){
			$chat .= '<script src="'. $chatSetting->fc_client_loc .'123flashchat.js"></script>';
			$chat .= '<iframe width="'.$chatSetting->fc_client_width.'" height="'.$chatSetting->fc_client_height.'" src="'.$url.'" vspale="0" scrolling="no" noresize="noresize" name="htmlchat" marginwidth="0" marginheight="0" framespacing="0" frameborder="0" border="0"></iframe>';
		}else{
			$chat .=  '<script src="'. $chatSetting->fc_client_loc .'123flashchat.js"></script>
				<object width="'.$chatSetting->fc_client_width.'" height="'.$chatSetting->fc_client_height.'" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,19,0" id="topcmm_123flashchat" type="application/x-shockwave-flash">
				<param name=movie value="'.$url.'">
				<param name=quality value="high">
				<param name="menu" value="false">
				<param name="allowScriptAccess" value="always">
				<embed src="'.$url.'" width="'.$chatSetting->fc_client_width.'" height="'.$chatSetting->fc_client_height.'" allowScriptAccess="always" quality="high" menu="false" type="application/x-shockwave-flash" pluginspace="http://www.macromedia.com/go/getflashplayer"  name="topcmm_123flashchat"></embed>
			</object>';
		}
    }
    return $chat;
}

function serviceGetChatStats(){
	global $current_user, $wpdb,$root_link;
	$pageurl=$_REQUEST['pid'];
	$root_link=get_bloginfo('url')."/".$post_page_title_ID->post_name."/";
    $sOutputCode = '';
    
    $fc_config = getConfig();
    //$status = getStatus();
    if ($fc_config->fc_user_list){
        $users  = getUsers($fc_config);
    }
    if (($fc_config->fc_room_list) && ($fc_config->fc_extendserver != "2")){
        $rooms  = getRooms($fc_config);
    }

    /*if (isset($status['ln']))
    {
        $sOutputCode .= '<b>' . $status['ln'] . '</b> login users.';
        if (isset($status['rn']))
        {
            $sOutputCode = '<b>' . $status['rn'] . '</b> rooms, ' . '<b>' . $status['cn'] . '</b> connections, ' . $sOutputCode;
        }
    }*/
    if(isset($rooms)){
        //$sOutputCode .= "<b><p>Rooms:</b> ";
        if(!$rooms){
            $sOutputCode .= "None";
        }else{
            $sOutputCode .= "<ul>";
            $xa = '<li>';
            foreach($rooms as $room){
				$sOutputCode .= $xa . '<a href="'.CHAT_PLUGIN_URL.'/123flashchat.php?room=' . $room['id'].'" target="_blank" >' .$room['name'] .'</a>(' . $room['count'] . ')';
                $sOutputCode .= "</li> ";
            }
			$sOutputCode .= "</ul>";
        }
    }
    if(isset($users)){
        $sOutputCode .= "</p><p><b>Users:</b> ";
        if(!$users){
            $sOutputCode .= "None</p>";
        }else{
            $xb = '';
            foreach($users as $user){
                $sOutputCode .= $xb . $user['name'];
                $xb = ', ';
            }
            $sOutputCode .= "</p>";
        }

    }
	$thishref = CHAT_PLUGIN_URL.'/123flashchat.php?room=';
    $js = ' onclick="window.open(\''.$thishref.'\');return false;"';
	$sOutputCode .=  '<p><a href="http://www.123flashchat.com"' .$js.">" ."<b>Chat Now!</b>" ."</a></p>";
    $sOutputCode = '<div class="dbContentHtml">' . $sOutputCode . '</div>';
    return $sOutputCode;
}

function getStatus($fc_config){
    $data = topcmmRequestRemote::requestRemote(CACHE_PATH . "status.js",1);
    $status_json = substr($data,10);
    if ((time() > substr($data,0,10)) || !$status_json){
        $server =  $fc_config->fc_extendserver;
        switch ($server){
            case "0":
                $status_js = $fc_config->fc_api_url . "online.js";
                if($rs = topcmmRequestRemote::requestRemote($status_js,1)){
                    $status_json = substr($rs,11,-1);
                }
                break;
            case "1":
                $status_js = $fc_config->fc_api_url . "online.js?group=" .$fc_config->fc_group;
                if($rs = topcmmRequestRemote::requestRemote($status_js,1)){
                    $status_json = substr($rs,11,-1);
                }
                break;
            case "2":
                $status_js = "http://free.123flashchat.com/freeroomnum.php?roomname=" . $fc_config->fc_room;
                if($rs = topcmmRequestRemote::requestRemote($status_js,1)){
                    preg_match("/document.write\('(.*)'\);/",$rs,$matches);
                    $status['ln'] = $matches[1];
                    $status_json = json_encode($status);
                }
                break;
        }
        @file_put_contents(CACHE_PATH . "status.js",(time() + 120) . $status_json);
    }
    return json_decode($status_json,true);
}

function getRooms($fc_config){
    $data = topcmmRequestRemote::requestRemote(CACHE_PATH ."rooms.js", 1);
    $rooms_json = substr($data,10);
    //var_dump($data);
    if ((time() > substr($data,0,10)) || !$rooms_json){
        $server = $fc_config->fc_extendserver;
		switch ($server){
            case "0":
                $room_js = $fc_config->fc_api_url . "rooms.js";
                break;
            case "1":
                $room_js = $fc_config->fc_api_url . "rooms.js?group=" . $fc_config->fc_group;
                break;
        }
        if($rs = topcmmRequestRemote::requestRemote($room_js,1)){
            $rooms_json = substr($rs,10,-1);
        }
        @file_put_contents(CACHE_PATH ."rooms.js",(time() + 120) . $rooms_json);
    }
    return json_decode($rooms_json, true);
}
/**
 * Function will get users;
 */
function getUsers($fc_config){
	// Timeout in seconds
    $context = stream_context_create(array('http' => array('timeout' => 3)));
    $data =  topcmmRequestRemote::requestRemote(CACHE_PATH ."users.js",1);
    $users_json = substr($data,10);
    if ((time() > substr($data,0,10)) || !$users_json){
        $server =  $fc_config->fc_extendserver;
        switch ($server){
            case "0":
                $rooms = getRooms($fc_config);
                $users = array();
                if($rooms){
	                foreach ($rooms as $room){
	                    $user_js = $fc_config->fc_api_url . "roomonlineusers.js?roomid=" . $room['id'];
	                    if($rs = topcmmRequestRemote::requestRemote($user_js,1)){
	                        $users = array_merge($users, json_decode(substr($rs,20,-1),true));
	                    }
	                }
                }
                $users_json = json_encode($users);
                break;
            case "1":
                $user_js = $fc_config->fc_api_url . "roomonlineusers.js?group=" . $fc_config->fc_group;
                if($rs = topcmmRequestRemote::requestRemote($user_js,1)){
                    $users_json = substr($rs,20,-1);
                }
                break;
            case "2":
                $user_js = "http://free.123flashchat.com/freeroomuser.php?roomname=" . $fc_config->fc_room;
                if($rs = topcmmRequestRemote::requestRemote($user_js,1)){
                    preg_match("/document.write\('(.*)'\);/",$rs,$matches);
                    foreach (explode(',', $matches[1]) as $user){
                        $users[] = array('name' => $user);
                    }
                }
                $users_json = json_encode($users);
                break;
        }
		$users_json = filterUserList($users_json);
        @file_put_contents(CACHE_PATH ."users.js",(time() + 120) . $users_json);
    }
    return json_decode($users_json, true);
}

function filterUserList($users_json){
	$user_array =  json_decode($users_json,true);
	$user_regroup_array = array();
	$user_tmp_array = array();
	foreach($user_array as $key => $val){
		if (!in_array($val['name'], $user_tmp_array)){
			$user_tmp_array[] = $val['name'];
			$user_regroup_array[$key] = $user_array[$key];
		}
	}
	return json_encode($user_regroup_array);
}

?>
