<?php
//Protege contra acesso direto
if ( !function_exists( 'is_admin' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die;
}
if (!is_admin ()) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die;
}
	
?><div class = "wrap"> 
<h1> Miner Pool Config </ h1>
<form method = "post" action = "options.php"> 
<?php 
	settings_fields ('rmdminerpooloption-group');
	do_settings_sections ('rmdminerpooloption-group');
?>
	<table class="form-table">
        <tr valign="top">
        <th scope="row">rmdminerpool_token_pvt</th>
        <td><input type="text" name="rmdminerpool_token_pvt" value="<?php echo esc_attr( get_option('rmdminerpool_token_pvt') ); ?>" /></td>
        </tr>
         
        <tr valign="top">
        <th scope="row">rmdminerpool_token_public</th>
        <td><input type="text" name="rmdminerpool_token_public" value="<?php echo esc_attr( get_option('rmdminerpool_token_public') ); ?>" /></td>
        </tr>
    </table>
<?php
submit_button (); ?> 
</form> 
</div>