<?php 
	if (!defined('CHAT_VERSION')) exit('No direct script access allowed'); 
	if(!(function_exists("curl_init") || ini_get("allow_url_fopen"))){
?>
		<div id="message" class="updated fade"><strong>Please contact ISP if "CURL" or "allow_url_fopen" is enabed in PHP configuration.</strong></div>
<?php
	}
	if(!empty($updateChat)){
?>
		<div id="message" class="updated fade"><strong><?php echo $updateChat; ?></strong></div>
<?php
	}
	$valid = getValid($get_chat_settings);
?>
<style>
	td.forumheader3 label{padding: 6px 10px; background:#e0e0e0;}
	.widefat .check-column{padding:12px 0;}
	#chatsize{padding-left:8px;}
</style>
<div class="wrap" style="line-height:24px;">
	<h2><?php _e('123FlashChat', 'wm_chat') ?></h2>
	<form enctype="multipart/form-data" name="frmgeneralsettings" id="chat" method="post" action="admin.php?page=chat_settings&action=setchat">
		<table class="wp-list-table widefat fixed pages" cellspacing="0" cellpadding="0" border="0" >
		<thead>
			<tr><th class="manage-column column-title" style="width:190px;"><span>Name</span></th>
				<th class="manage-column column-title"><span>Value</span></th>
			</tr>
		</thead>
		<tbody>
			<tr valign="middle">
				<th style="white-space: nowrap;" valign="top" class="forumheader3" rowspan="3">Chat Server Mode:</th>
				<td class="check-column">
					<input type="radio" <?php if($get_chat_settings->fc_extendserver == "0" ) {  echo "checked"; } ?> onclick="changeserver(0);" name="fc_extendserver" id="fc_extendserver0" value="0"> <label for="fc_extendserver0">Chat Server Host By Own</label><br />
					<span style="font-family: verdana,sans-serif;font-size: 0.85em;font-style: italic;">Please download and install 123FlashChat first: <a href="http://www.123flashchat.com/download.html" target="_blank"> http://www.123flashchat.com/download.html</a>,<br />
					and set Chat Client Location to: http://&lt;your chat server domain or ip&gt;:35555/</span>
				</td>
			</tr>
			<tr valign="middle">
				<td class="check-column">
					<input type="radio" <?php if($get_chat_settings->fc_extendserver == "1" ) { echo "checked"; } ?> onclick="changeserver(1);" name="fc_extendserver" id="fc_extendserver1" value="1"> <label for="fc_extendserver1">Chat Server Host By 123 Flash Chat</label><br />
					<span style="font-family: verdana,sans-serif;font-size: 0.85em;font-style: italic;"><a href="http://www.123flashchat.com/host.html" target="_blank">For paid host</a>, please set the Chat Client Location like this, for example: http://host71200.123flashchat.com/wordpress/, <a href="http://www.123flashchat.com/host.html" target="_blank">Buy host</a><br />
					<a href="http://www.123flashchat.com/host/apply.php" target="_blank">For trial host</a>, please setup Chat Client Location like this: http://trial.123flashchat.com/yourhostname<br />
					Just replace "yourhostname" to the real one when you applied, <a href="http://www.123flashchat.com/host/apply.php" target="_blank">Apply trial host</a></span>
				</td>
			</tr>
			<tr valign="middle">
				<td class="check-column">
					<input type="radio" <?php if($get_chat_settings->fc_extendserver == "2" ) { echo "checked"; } ?> onclick="changeserver(2);" name="fc_extendserver" id="fc_extendserver2" value="2"> <label for="fc_extendserver2">Chat Server Host By 123 Flash Chat For Free</label><br />
					<span style="font-family: verdana,sans-serif;font-size: 0.85em;font-style: italic;">This chat server mode aims at testing the basic functions, only supported 1 room, no video chat function, and also you don't have the administrator permission of entering this chat room, you can select the mode 1 or 2 to get the full functions and control your chat room.</span>
				</td>
			</tr>
			<tr class="alternate" valign="middle" id="login_chat" style="<?php if($get_chat_settings->fc_extendserver == "2") { echo "display: none;"; } ?>">
				<th style="white-space: nowrap;" >Integration URL:</th>
				<td class="forumheader3 check-column">
					To integrate users for auto-login to chat, please open the Chat Admin Panel, then System Settings > Integration Panel, set authentication URL to:<br />
					<span style="font-size:14px;color:#cc3333;"><?php echo get_bloginfo('url');?>/wp-content/plugins/123flashchat/login_chat.php?username=%username%&password=%password%<span>
				</td>
			</tr>
			<tr valign="middle" class="alternate" id="servertype" style="<?php if($get_chat_settings->fc_extendserver != "0" ) echo "display: none;";?>">
				<th style="white-space: nowrap;" >Server Type:</th>
				<td class="forumheader3 check-column">
					<label title="123FlashChat"><input type="radio" <?php if($get_chat_settings->fc_servertype == "0") echo 'checked="checked"'; ?> name="fc_servertype" id="servertype0" value="0" onclick="chooseservertype('0')"> 123FlashChat</label>
					<label title="123PPVSoftware"><input type="radio" <?php if($get_chat_settings->fc_servertype == "1") echo 'checked="checked"'; ?> name="fc_servertype" id="servertype1" value="1" onclick="chooseservertype('1')"> 123PPVSoftware</label>
				</td>
			</tr>
			<tr valign="middle" id="chat_host" style="<?php if($get_chat_settings->fc_extendserver != "0" || $valid == 0 ) { echo "display: none;"; } ?>">
				<th style="white-space: nowrap;" class="form-field form-required  ">Chat Server Host:</th>
				<td class="forumheader3 check-column">
					<input type="text" size="50" value="<?php echo $get_chat_settings->fc_server_host; ?>" name="fc_server_host" id="fc_server_host" class="tbox" /> Default: <?php echo $_SERVER['SERVER_NAME'];?>
				</td>
			</tr> 
			<tr  class="alternate" valign="middle" id="chat_port" style="<?php if($get_chat_settings->fc_extendserver != "0" || $valid == 0 ) { echo "display: none;"; } ?>">
				<th style=" white-space: nowrap;" >Chat Server Port:</th>
				<td class="forumheader3 check-column">
					<input type="text" maxlength="5" size="6" value="<?php echo $get_chat_settings->fc_server_port; ?>" name="fc_server_port" id="fc_server_port" class="tbox" /> Default: <span id="default_server_port"><?php if($get_chat_settings->fc_servertype == 0){?>51127<?php }else{ ?>51212<?php } ?></span>
				</td>
			</tr>
			<tr valign="middle" id="http_port" style="<?php if($get_chat_settings->fc_extendserver != "0" || $valid == 0 ) { echo "display: none;"; } ?>">
				<th style="white-space: nowrap;" >Chat Http Port:</th>
				<td class="forumheader3 check-column">
					<input type="text" maxlength="5" size="6" value="<?php echo $get_chat_settings->fc_server_port_h; ?>" name="fc_server_port_h" id="fc_server_port_h" class="tbox"> Default: <span id="default_http_port"><?php if($get_chat_settings->fc_servertype == 0){?>35555<?php }else{ ?>31212<?php } ?></span>
				</td>
			</tr>
			<tr valign="middle" id="client_location" style="<?php if($get_chat_settings->fc_extendserver == "2" ) { echo "display: none;"; } ?>" >
				<th style="white-space: nowrap;" >Chat Client Location:</th>
				<td class="forumheader3 check-column">
					<input type="text" size="50" value="<?php echo $get_chat_settings->fc_client_loc; ?>" name="fc_client_loc" id="fc_client_loc" class="tbox">
					<span id="client_loc0" <?php if($get_chat_settings->fc_extendserver != 0){?>style="display:none;"<?php } ?>>Example: http://<?php echo $_SERVER['SERVER_NAME']; ?>:<?php if($get_chat_settings->fc_servertype == 0){?>35555<?php }else{ ?>31212<?php } ?>/</span>
            		<span id="client_loc1" <?php if($get_chat_settings->fc_extendserver != 1){?>style="display:none;"<?php } ?>>Example: http://hostxxx.123flashchat.com/group_name/</span> 
				</td>
			</tr>
			<tr class="alternate" valign="middle" id="room_name" style="<?php if($get_chat_settings->fc_extendserver != "2" ) { echo "display: none;"; } ?>">
				<th style="white-space: nowrap;" >Chat Room Name:</th>
				<td class="forumheader3 check-column">
					<input type="text" size="50" value="<?php echo $get_chat_settings->fc_room; ?>" name="fc_room" id="fc_room" class="tbox">
				</td>
			</tr>
			<tr valign="middle" id="client_type" style="<?php if($get_chat_settings->fc_extendserver == "2" || ($get_chat_settings->fc_extendserver == "0" && $get_chat_settings->fc_servertype == "1")) { echo "display: none;"; } ?>" >
				<th style="white-space: nowrap;" >Client Type:</th>
				<td class="forumheader3 check-column">
					<label title="Use Html Chat"><input type="radio" name="fc_clienttype" id="fc_clienttype0" value="0" <?php if($get_chat_settings->fc_clienttype == 0){ echo 'checked="checked"';} ?> /> Html Chat</label>
					<label title="Use Flash Chat"><input type="radio" name="fc_clienttype" id="fc_clienttype1" value="1" <?php if($get_chat_settings->fc_clienttype == 1){ echo 'checked="checked"';} ?> /> Flash Chat</label>
				</td>
			</tr>
			<tr valign="middle" id="room_list" style="<?php if($get_chat_settings->fc_extendserver == "2" ) echo "display: none;";?>">
				<th style="white-space: nowrap;" >Show rooms list:</th>
				<td class="forumheader3 check-column">
					<label title="Don't Show Room List"><input type="radio" <?php if($get_chat_settings->fc_room_list == "0") echo 'checked="checked"'; ?> name="fc_room_list" id="fc_room_list0" value="0"> No</label>
					<label title="Show Room List"><input type="radio" <?php if($get_chat_settings->fc_room_list == "1") echo 'checked="checked"'; ?> name="fc_room_list" id="fc_room_list1" value="1"> Yes</label>
				</td>
			</tr> 
			<tr class="alternate" valign="middle">
				<th style="white-space: nowrap;" >Show users list:</th>
				<td class="forumheader3 check-column">
					<label title="Don't Show User List"><input type="radio" <?php if($get_chat_settings->fc_user_list == "0") echo 'checked="checked"'; ?> name="fc_user_list" id="fc_user_list0" value="0"> No</label>
					<label title="Show User List"><input type="radio" <?php if($get_chat_settings->fc_user_list == "1") echo 'checked="checked"'; ?> name="fc_user_list" id="fc_user_list1" value="1"> Yes</label>
				</td>
			</tr>
			<tr valign="middle">
				<th style="white-space: nowrap;" valign="top" >Chat Client Size:</th>
				<td class="forumheader3 check-column">
					<label title="Full Screen"><input type="radio" <?php if($get_chat_settings->fc_fullscreen == "1") echo 'checked="checked"'; ?> name="fc_fullscreen" id="fc_fullscreen1" value="1" onclick="showchatsize(0);"> Full Screen</label>
					<label title="Custom Chat Size"><input type="radio" <?php if($get_chat_settings->fc_fullscreen == "0") echo 'checked="checked"'; ?> name="fc_fullscreen" id="fc_fullscreen0" value="0" onclick="showchatsize(1);"> Custom  
						<span id="chatsize" <?php if($get_chat_settings->fc_fullscreen == "1") echo 'style="display:none;"'; ?>>
							Width:<input type="text" maxlength="5" size="4" value="<?php echo $get_chat_settings->fc_client_width; ?>" name="fc_client_width" id="fc_client_width" class="tbox"> 
							Height:<input type="text" maxlength="5" size="4" value="<?php echo $get_chat_settings->fc_client_height; ?>" name="fc_client_height" id="fc_client_height" class="tbox">
						</span>
					</label>					
				</td>
			</tr>
			<tr class="alternate" valign="middle" >
				<th style="white-space: nowrap;" >Chat Client Language:</th>
				<td class="forumheader3 check-column">
					<select class="tbox" name="fc_client_lang" id="set_lang" style="<?php if($get_chat_settings->fc_extendserver != "2" ) echo "display: none;";?>">
						<option value="auto" <?php if($get_chat_settings->fc_client_lang == "auto") echo "selected"; ?>  >Auto detect</option>
						<option value="en" <?php if($get_chat_settings->fc_client_lang == "en") echo "selected"; ?> >English</option>
						<option value="zh-CN" <?php if($get_chat_settings->fc_client_lang == "zh-CN") echo "selected"; ?> >GB Chinese</option>
						<option value="zh-TW" <?php if($get_chat_settings->fc_client_lang == "zh-TW") echo "selected"; ?> >Big5 Chinese</option>
						<option value="fr" <?php if($get_chat_settings->fc_client_lang == "fr") echo "selected"; ?> >French</option>
						<option value="it" <?php if($get_chat_settings->fc_client_lang == "it") echo "selected"; ?> >Italian</option>
						<option value="de" <?php if($get_chat_settings->fc_client_lang == "de") echo "selected"; ?> >German</option>
						<option value="nl" <?php if($get_chat_settings->fc_client_lang == "nl") echo "selected"; ?> >Dutch</option>
						<option value="hu" <?php if($get_chat_settings->fc_client_lang == "hu") echo "selected"; ?> >Hungarian</option>
						<option value="es" <?php if($get_chat_settings->fc_client_lang == "es") echo "selected"; ?> >Spanish</option>
						<option value="hr" <?php if($get_chat_settings->fc_client_lang == "hr") echo "selected"; ?> >Croatian</option>
						<option value="tr" <?php if($get_chat_settings->fc_client_lang == "tr") echo "selected"; ?> >Turkish</option>
						<option value="ar" <?php if($get_chat_settings->fc_client_lang == "ar") echo "selected"; ?> >Arabic</option>
						<option value="pt" <?php if($get_chat_settings->fc_client_lang == "pt") echo "selected"; ?> >Portuguese</option>
						<option value="ru" <?php if($get_chat_settings->fc_client_lang == "ru") echo "selected"; ?> >Russian</option>
						<option value="ko" <?php if($get_chat_settings->fc_client_lang == "ko") echo "selected"; ?> >Korean</option>
						<option value="serbian" <?php if($get_chat_settings->fc_client_lang == "serbian") echo "selected"; ?> >Serbian</option>
						<option value="no" <?php if($get_chat_settings->fc_client_lang == "no") echo "selected"; ?> >Norwegian</option>
						<option value="ja" <?php if($get_chat_settings->fc_client_lang == "ja") echo "selected"; ?> >Japanese</option>
					</select>
					<span id="u_al" class="description" style="<?php if($get_chat_settings->fc_extendserver == "2" ) echo "display: none;";?>">Note: You can set the language in 123FlashChat Admin Panel -&gt; Client Setting -&gt; Language Setting</span>
				</td>
			</tr>
			<tr valign="middle" >
				<th style="white-space: nowrap;" >Chat Client Skin:</th>
				<td class="forumheader3 check-column">
					<select class="tbox" name="fc_client_skin" id="set_skin" style="<?php if($get_chat_settings->fc_extendserver != "2" ) echo "display: none;";?>">
						<option value="default"  <?php if($get_chat_settings->fc_client_skin == "default") echo "selected"; ?> >Default</option>
						<option value="green" <?php if($get_chat_settings->fc_client_skin == "green") echo "selected"; ?> >Green</option>
						<option value="orange" <?php if($get_chat_settings->fc_client_skin == "orange") echo "selected"; ?> >Orange</option>
						<option value="red" <?php if($get_chat_settings->fc_client_skin == "red") echo "selected"; ?> >Red</option>
						<option value="black" <?php if($get_chat_settings->fc_client_skin == "black") echo "selected"; ?> >Black</option>
						<option value="beige" <?php if($get_chat_settings->fc_client_skin == "beige") echo "selected"; ?> >Beige</option>
						<option value="standard" <?php if($get_chat_settings->fc_client_skin == "standard") echo "selected"; ?> >Standard</option>
						<option value="clean" <?php if($get_chat_settings->fc_client_skin == "clean") echo "selected"; ?> >Clean</option>
						<option value="artistic" <?php if($get_chat_settings->fc_client_skin == "artistic") echo "selected"; ?> >Artistic</option>
					</select>
					<span id="u_as" class="description" style="<?php if($get_chat_settings->fc_extendserver == "2" ) echo "display: none;";?>" >Note: You can set the skin in 123FlashChat Admin Panel -&gt; Client Setting -&gt; Skin</span>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<th class="manage-column column-title"></th>
				<th class="manage-column column-title">
					<input type="submit" name="submit" id="submit" value="Save" class="button-secondary action" style="padding: 3px 8px;">
				</th>
			</tr> 
		</tfoot>		   
		</table>
		<input type="hidden" value=<?php echo $valid; ?> name="server_loc" id="server_loc">
	</form>
</div>

<!-- [END] framework_wrap -->
