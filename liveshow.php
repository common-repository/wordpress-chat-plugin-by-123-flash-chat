<?php
	if ( !defined('ABSPATH') )
		define('ABSPATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
	require( ABSPATH . 'wp-config.php' );
	$chat_Config = getConfig();
	//	$chat_Settings = getChatSettings($chat_Config, $_GET);
	//	$getchat = getChat($chat_Settings);
?>
<?php
//if($chat_Config->fc_show_live == 1 && $chat_Config->fc_extendserver != 2 ) {
?>
<div class="dsp_box-out">
	<div class="dsp_box-in">
	<!-- FOR 123FLASHCHAT CODE BEGIN -->
		<div id="live_show_123flashchat" align="left" style="width:<?php echo $chat_Config->fc_client_width;?>px; height:<?php echo $chat_Config->fc_client_height;?>px">
			<link rel="stylesheet" id="css_123flashchat" type="text/css" href="<?php echo $chat_Config->fc_liveurl;?>css/<?php echo $chat_Config->fc_sty;?>.css" />
			<div id="loading_123flashchat" align="center">
				<div class="loading_main_123flashchat">
					<a href="http://www.123flashchat.com" class="logo" title="123 Flash Chat" target="_blank">
						<img src="<?php echo $chat_Config->fc_client_loc;?>liveshow/images/123flashchat_logo.gif" alt="123 Flash Chat" style="cursor:point;border: 0px none"/>
					</a>
				</div>
				<div class="loading_main_123flashchat"><img src="<?php echo $chat_Config->fc_client_loc;?>liveshow/images/loading_image.gif"></div>
			</div>
			<script language="javascript">
				var init_host_123flashchat = "<?php echo $chat_Config->fc_server_host;?>";
				var init_port_123flashchat = "<?php echo $chat_Config->fc_server_port;?>";
				var init_host_s_123flashchat = "<?php echo $chat_Config->fc_server_host;?>";
				var init_port_s_123flashchat = "";
				var init_host_h_123flashchat = "<?php echo $chat_Config->fc_server_host;?>";
				var init_port_h_123flashchat = "";
				var init_group_123flashchat = "default";
				var init_room_123flashchat = "<?php echo $chat_Config->fc_liveroom;?>";
				var http_root_123flashchat = "<?php echo $chat_Config->fc_client_loc;?>liveshow";
			</script>
			<script language='javascript' src='<?php echo $chat_Config->fc_liveurl;?>js/123flashchat_liveshow.js'></script>
			<script language='javascript' src="<?php echo $chat_Config->fc_liveurl;?>123flashchat_liveshow_core.js"></script>
		</div>
	<!-- FOR 123FLASHCHAT CODE END -->
	</div>
</div>
<?php
//}
?>