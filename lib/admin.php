<?php
//Protege contra acesso direto
if ( !function_exists( 'is_admin' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die;
}

/**
 * Adds a new top-level page to the administration menu.
 */
function rmdminermenucreate() {
	 if (is_admin ()) {// ações admin 
		add_menu_page( __( 'Miner Pool Config', 'textdomain' ) , __( 'Miner Pool Config','textdomain' ) , 'manage_options' , 'rmdminermenu' , 'rmdminermenucreatecontent' , '' , 71 );
		add_action ('admin_init', 'rmdminerpool_registersettings'); 
	 } Else { 
		// enfileira não-administradores, ações e filtros 
	 }
}
/**
 * Disply callback for the Unsub page.
 */
function rmdminermenucreatecontent() {
	include(RMDMINERPOOL_ASSETS.'template-parts/admin.php');
}

function rmdminerpool_registersettings () {// lista de permissões opções 
  register_setting ('rmdminerpooloption-group', 'rmdminerpool_token_pvt'); 
  register_setting ('rmdminerpooloption-group', 'rmdminerpool_token_public');
}
?>
