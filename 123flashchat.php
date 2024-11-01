<?php
	if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(dirname(dirname(dirname(__FILE__)))) . '/');
	require( ABSPATH . 'wp-config.php' );

	$chatSettings = getConfig();
	
	if($chatSettings->fc_fullscreen == 1){
		
	}else{
		
	}
	$getchat = getChat($chatSettings);
?>
<!DOCTYPE html>
<head>
	<meta charset="UTF-8" />
	<title>123 Flash Chat</title>
	<style>
		html,body{
			height:100%;
		}
		body{
			margin:0px; overflow:hidden;padding:0px;
		}
	</style>	
</head>
<body>
<?php echo $getchat; ?>
</body>
<html>

