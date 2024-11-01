<?php
global $jal_db_version;
$jal_db_version = "1.4";
function jal_install() {
   global $wpdb;
   global $jal_db_version;
   $table_name = $wpdb->prefix . "123flashchat";
   $table_post = $wpdb->prefix . "post";

   $installed_ver = get_option( "jal_db_version" );
   if($wpdb->get_var("show tables like '$table_name'") == null)
   {
      $sql = "CREATE TABLE " . $table_name . " (
				id						TINYINT(1)			NOT NULL auto_increment,
      			fc_extendserver			TINYINT(1)			NOT NULL default '2',
      			fc_servertype			TINYINT(1)			NOT NULL default '0',
      			fc_server_host			VARCHAR(255)		NOT NULL default '',
				fc_server_port			INT(5)				NOT NULL default 0,
      			fc_server_host_s		VARCHAR(255)		NOT NULL default '',
				fc_server_port_s		INT(5)				NOT NULL default 0,
      			fc_server_host_h		VARCHAR(255)		NOT NULL default '',
				fc_server_port_h		INT(5)				NOT NULL default 0,
				fc_client_loc			VARCHAR(255)		NOT NULL default '',
				fc_api_url				VARCHAR(255)		NOT NULL default '',
				fc_group				VARCHAR(255)		default '',
				fc_room					VARCHAR(255)		NOT NULL default 'Lobby',
				fc_room_list			TINYINT(1)			NOT NULL default '1',
				fc_user_list			TINYINT(1)			NOT NULL default '1',
				fc_clienttype			TINYINT(1)			NOT NULL default '0',
				fc_fullscreen			TINYINT(1)			NOT NULL default '1',
				fc_client_width			INT(5)				NOT NULL default '750',
				fc_client_height		INT(5)				NOT NULL default '528',
				fc_client_lang			VARCHAR(20)			NOT NULL default 'auto',
				fc_client_skin			VARCHAR(20)			NOT NULL default 'default',
				PRIMARY KEY (id)
		);";
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		$wpdb->query( $wpdb->prepare("INSERT INTO $table_name set id = %d",1));

      update_option( "jal_db_version", $jal_db_version );
  }
   add_option("jal_db_version", $jal_db_version);
   //add single page for 123flashchat
  // global $userdata;
   $chatid = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->posts WHERE post_name = %s and post_type= %s ",'123flashchat','page'));
	if(!$chatid){
		$postpage = array(
			'post_author'		=> 1,
			'post_date'			=> date('Y-m-d H:i:s'),
			'post_date_gmt'		=> date('Y-m-d H:i:s'),
			'post_content'		=> '<iframe src="wp-content/plugins/123flashchat/123flashchat.php" width="100%" height="600" border="0" frameborder="no"></iframe>',
			'post_title'		=> 'Chat',
			'post_name'			=> '123flashchat',
			'post_type'			=> 'page',
			'guid'				=> ''
	    );
		$wpdb->insert($wpdb->posts ,$postpage, array("%d","%s","%s","%s","%s","%s","%s","%s") );
		$result=$wpdb->get_col($wpdb->prepare("SELECT LAST_INSERT_ID() FROM $wpdb->posts  WHERE %s",'1'));
		if(isset($result[0])){
			$wpdb->query( "UPDATE $wpdb->posts SET  guid = '".site_url()."/?p=".$result[0]."' WHERE ID = ".$result[0]);
		}
	}	
	
}

function myplugin_update_db_check() {
    global $jal_db_version, $wpdb;
	$table_name = $wpdb->prefix . "123flashchat";
    if ($wpdb->get_var("show tables like '$dsp_123flashchat_table'") != $table_name) {
        jal_install();
    }
}
add_action('plugins_loaded', 'myplugin_update_db_check');
?>