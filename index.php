<?php
/*
Plugin Name: 123 Flash Chat
Plugin URI: http://www.123flashchat.com/wordpress-chat.html
Description: 123FlashChat Plugin can be used to put your facebook bar (<a href="http://www.123flashchat.com" target="_blank">demo page</a>) in WordPress. And you may easily get a free host service by simply selecting a domain name. If you are 123FlashChat.com's license buyer or host service buyer, you may get more professional technique support.
Version: 3.5
Author: 123flashchat.com
Author URI: http://www.123flashchat.com
*/

/**
 * Definitions
 *
 * @since 1.0.0
 */
define( 'CHAT_VERSION', '3.5' );
define( 'CHAT_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . dirname( plugin_basename( __FILE__ ) ) );
define( 'CHAT_PLUGIN_URL', WP_PLUGIN_URL . '/' . dirname( plugin_basename( __FILE__ ) ) );

/**
 * Required Files
 *
 * @since 1.0.0
 */

require_once( CHAT_PLUGIN_DIR . '/chatwidget/chat-widget.php' );
require (CHAT_PLUGIN_DIR."/lib/functions.php");
require_once (CHAT_PLUGIN_DIR."/chatdb.php");
require_once( CHAT_PLUGIN_DIR . '/chatadmin/wp_chat_admin.php' );
require_once( CHAT_PLUGIN_DIR . '/chatwidget/chat-sider.php' );


/**
 * Instantiate Classe
 *
 * @since 1.0.0
 */
$chat_admin = new Chat_Admin();
$chatsider = new chatSider();

/**
 * Wordpress Activate/Deactivate
 *
 * @uses register_activation_hook()
 * @uses register_deactivation_hook()
 *
 * @since 1.0.0
 */

register_activation_hook( __FILE__, array( $chat_admin, 'chat_active' ) );
$chatsider->w_init('chatSider');
register_deactivation_hook( __FILE__, array( $chat_admin, 'chat_deactivate' ) );
/**
 * Required action filters
 *
 * @uses add_action()
 *
 * @since 1.0.0
 */
add_action('admin_init', array( $chat_admin, 'my_chat_admin_init' ) );
add_action( 'admin_menu', array( $chat_admin, 'wm_add_chat_page' ) );

?>