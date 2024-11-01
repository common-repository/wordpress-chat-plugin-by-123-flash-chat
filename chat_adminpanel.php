<?php
if (!defined('CHAT_VERSION')) exit('No direct script access allowed');
	global $current_user;
    $server = $get_chat_settings->fc_extendserver;
?>
<br /><br /><br />
<div text-align="center" align="center">
<?php
    if($server == 2){
?>
	<p>Sorry! Admin Panel is not available in the Free Chat Hosting Edition. 123FlashChat Full Version starts at $30. <br />Click the link below to have a live Admin Panel Demo for testing.</p>
	<p><a href="http://www.123flashchat.com/admin-panel-free.html" target="_blank">Live Demo</a></p>
<?php
    }else{
        $url =  $get_chat_settings->fc_client_loc . "admin_123flashchat.swf?init_host=" . $get_chat_settings->fc_server_host . "&init_port=" . $get_chat_settings->fc_server_port . (($server == "1") ? ("&init_group=" . $get_chat_settings->fc_group) : "");
        if(!empty($current_user->user_login) && !empty($current_user->user_pass) ){
        	$url .= "&init_user=" . rawurlencode($current_user->user_login) . "&init_password=" . rawurlencode($current_user->user_pass);
        }        
?>
		<object width="100%" height="600" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,19,0">
			<param name=movie value="<?php echo $url; ?>">
			<param name=quality value="high">
			<param name="menu" value="false">
			<embed src="<?php echo $url; ?>" width="100%" height="600" quality="high" menu="false" type="application/x-shockwave-flash" pluginspace="http://www.macromedia.com/go/getflashplayer"></embed>
		</object>

<?php   } ?>
</div>