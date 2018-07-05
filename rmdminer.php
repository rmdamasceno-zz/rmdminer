<?php
/*
Plugin Name:  RMD miner pool
Plugin URI:   http://www.rmdamasceno.com.br/portifolio/rmdminerpool
Description:  Ferramenta para grupo de mineração.
Version:      18.6062
Author:       RMDamasceno
Author URI:   http://www.rmdamasceno.com.br
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
*/
/*
RMD miner pool is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.
 
RMD miner pool is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
 
You should have received a copy of the GNU General Public License
along with RMD miner pool. If not, see {URI to Plugin License}.
*/

//Protege contra acesso direto
if ( !function_exists( 'is_admin' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die;
}

//Definição das constantes
define( 'RMDMINERPOOL', plugin_dir_path( __FILE__ ) );
define( 'RMDMINERPOOL_ASSETS', RMDMINERPOOL .  '/assets/' );
define( 'RMDMINERPOOLURI', plugin_dir_url( __FILE__ ) );
define( 'RMDMINERPOOL_ASSETSURI', RMDMINERPOOLURI .  '/assets/' );

//verificação instalação dos arquivos do plugin
$rmdamascenofiles = array(
						RMDMINERPOOL_ASSETS,
						RMDMINERPOOL_ASSETS.'template-parts',
						RMDMINERPOOL_ASSETS.'js',
						RMDMINERPOOL_ASSETS.'template-parts/admin.php',
						RMDMINERPOOL_ASSETS.'js/script.js',
						RMDMINERPOOL_ASSETS.'js/miner.min.js',
						RMDMINERPOOL.'lib/coinhive-api.php',
						RMDMINERPOOL.'lib/admin.php',
						RMDMINERPOOL.'lib/functions.php',
						);

//Define Tokens
define( 'RMDMINERPOOL_TOKEN_PVT', get_option( 'rmdminerpool_token_pvt' ) );
define( 'RMDMINERPOOL_TOKEN_PUBLIC', get_option( 'rmdminerpool_token_public' ) );

if(!file_check($rmdamascenofiles)){exit;}

//Inclusão de Arquivos
if (is_admin){
	require(RMDMINERPOOL.'lib/admin.php');
}
require(RMDMINERPOOL.'lib/functions.php');

////////////////----------------/////////////////////////

//Limpeza de variaveis
$method = null;
$patch = null;
$data = null;
////////////////----------------/////////////////////////

//Inclui Actions

//cria endpoint
add_action( 'rest_api_init', 'add_miner_responses');
//Passa parametros do PHP para o JS
add_action ( 'wp_enqueue_scripts' , function () {
	wp_enqueue_script ( 'script' , RMDMINERPOOL_ASSETS  .  'js/script.js' , array ( 'jquery' ), false , true );
	wp_localize_script ( 'script' , 'wpApiSettings' , array ( 'root'  => esc_url_raw (rest_url ()), 'nonce'  => wp_create_nonce ( 'wp_rest' )));
});
//cria Menu
add_action('admin_menu', 'rmdminermenucreate');

//          Shortcodes            /////////////
//Shortcode para adicionar script em pagina
add_shortcode('rmdminerscript','addmainscriptapi');
//Shortcode para adicionar o minerador com interface do usuario//
add_shortcode('rmdminerui','adduiscriptapi');

////////////////----------------/////////////////////////
function file_check($files){
	$return = True;
	foreach($files as $file){
		if(!is_dir($file)){
			if(!file_exists($file)){
				echo $file;
				$return = False;
			}
		}
	}
	return $return;
}
?>