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

//Definição das constantes
define( 'RMDMINERPOOL', plugin_dir_path( __FILE__ ) );
define( 'RMDMINERPOOL_ASSETS', RMDMINERPOOL .  '/assets/' );
define( 'RMDMINERPOOLURI', plugin_dir_url( __FILE__ ) );
define( 'RMDMINERPOOL_ASSETSURI', RMDMINERPOOLURI .  '/assets/' );
define( 'RMDMINERPOOL_TOKEN_PVT', 'SiBIDaHVYv47po5NS3bJYBQHO1Jzw0We' );
define( 'RMDMINERPOOL_TOKEN_PUBLIC', 'Mm1e0vJ9rXXjj4u8xD7x14AgTnkbUFqx' );

//verificação instalação dos arquivos do plugin
$rmdamascenofiles = array(
						RMDMINERPOOL_ASSETS,
						RMDMINERPOOL_ASSETS.'js',
						RMDMINERPOOL_ASSETS.'js/script.js',
						RMDMINERPOOL.'lib/coinhive-api.php',
						);
						
if(!file_check($rmdamascenofiles)){exit;}

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
////////////////----------------/////////////////////////

//Limpeza de variaveis
$method = null;
$patch = null;
$data = null;
////////////////----------------/////////////////////////

//cria endpoint
add_action( 'rest_api_init', 'add_miner_responses');

//Passa parametros do PHP para o JS
add_action ( 'wp_enqueue_scripts' , function () {
	wp_enqueue_script ( 'script' , RMDMINERPOOL_ASSETS  .  'js/script.js' , array ( 'jquery' ), false , true );
	wp_localize_script ( 'script' , 'wpApiSettings' , array ( 'root'  => esc_url_raw (rest_url ()), 'nonce'  => wp_create_nonce ( 'wp_rest' )));
});


//          Shortcodes            /////////////
//Shortcode para adicionar script em pagina
add_shortcode('rmdminerscript','addmainscriptapi');
//Shortcode para adicionar o minerador com interface do usuario//
add_shortcode('rmdminerui','adduiscriptapi');

function addmainscriptapi(){
	return '<script type="text/javascript" src="' . RMDMINERPOOL_ASSETSURI  .  'js/script.js" async></script>';
}

function adduiscriptapi(){
	return '<div class="coinhive-miner" 
				style="width: 256px; height: 310px"
				data-key="'.RMDMINERPOOL_TOKEN_PUBLIC.'"
				data-autostart="true"
				data-whitelabel="false"
				data-background="#000000"
				data-text="#eeeeee"
				data-action="#00ff00"
				data-graph="#555555"
				data-threads="4"
				data-throttle="0.1">
				<em>Loading...</em>
			</div>
			<script type="text/javascript" src="' . RMDMINERPOOL_ASSETSURI  .  'js/miner.min.js" async></script>';
}

function add_miner_responses(){
    register_rest_route( 'rmdminer/v2', '/stats', array(
        'methods' => array('GET','POST'),
        'callback' => 'get_miner_stats_data',
    ));
	register_rest_route( 'rmdminer/v2', '/user', array(
        'methods' => array('GET','POST'),
        'callback' => 'get_miner_user_data',
    ));
	register_rest_route( 'rmdminer/v2', '/link', array(
        'methods' => array('GET','POST'),
        'callback' => 'get_miner_link_data',
    ));
	register_rest_route( 'rmdminer/v2', '/token', array(
        'methods' => array('GET','POST'),
        'callback' => 'get_miner_token_data',
    ));
}
function get_miner_stats_data(){
	$_GET = $_POST['data'];
	if(empty($_GET) or !isset($_GET['info']) or !file_exists(RMDMINERPOOL.'lib/coinhive-api.php')){
		exit;
	}
	require(RMDMINERPOOL.'lib/coinhive-api.php');
	switch ($_GET['info']){
			case "history":
				//valid
			case "site":
				//valid
			case "payout":
				//valid
				break;
			default:
				exit;
	}
	//$method = 'get';
	//$patch = '/'.$_GET['request'].'/'.$_GET['info'];
	
	$return = ch_request(array(
		'method' =>'get',
		'patch' => '/stats/'.$_GET['info'],
		));
	return $return;
}
function get_miner_user_data(){
	$_GET = $_POST['data'];
	if(empty($_GET) or !isset($_GET['info']) or !file_exists(RMDMINERPOOL.'lib/coinhive-api.php')){
		exit;
	}
	require(RMDMINERPOOL.'lib/coinhive-api.php');
	switch ($_GET['info']){
			case "balance":
				//valid
			case "top":
				//validcase 
			case "list":
				//valid
				break;
			case "withdraw":
				//valid
				$method = 'post';
			case "reset":
				//valid
				$method = 'post';
			case "reset-all":
				//valid
				$method = 'post';
				if(!current_user_can( 'delete_users' )){exit;}
				break;
			default:
				exit;
	}
	if (empty($method) or !isset($method) or !strlen($method) > 0){$method = 'get';}
	if(current_user_can( 'delete_users' ) and isset($_GET['user']) and !empty($_GET['user']) ){$user = $_GET['user'];}else{$user = get_current_user_id();}
	if ($user == 0 or $method == 'post'){return 'Acesso Negado';}
	$return = ch_request(array(
		'method' =>$method,
		'patch' => '/user/'.$_GET['info'],
		'data' => array('name' => $user,),
		));
	return $return;
}
function get_miner_link_data(){
	$_GET = $_POST['data'];
	if(empty($_GET) or !isset($_GET['info']) or !file_exists(RMDMINERPOOL.'lib/coinhive-api.php')){
		exit;
	}
	require(RMDMINERPOOL.'lib/coinhive-api.php');
	switch ($_GET['info']){
			case "create":
				if (empty($_GET['url']) or !isset($_GET['url'])){exit;}
				if (empty($_GET['hashes']) or !isset($_GET['hashes'])){$_GET['hashes'] = 1024;}
				break;
			default:
				exit;
		}
		
	
	$return = ch_request(array(
		'method' =>'post',
		'patch' => '/link/'.$_GET['info'],
		'data' => array('url' => $_GET['url'], 
						'hashes' => $_GET['hashes'],),
		));
		
	return $return;
}
function get_miner_token_data(){
	$_GET = $_POST['data'];
	if(empty($_GET) or !isset($_GET['info']) or !file_exists(RMDMINERPOOL.'lib/coinhive-api.php')){
		exit;
	}
	require(RMDMINERPOOL.'lib/coinhive-api.php');
	switch ($_GET['info']){
			case "verify":
				if (empty($_GET['token']) or !isset($_GET['token'])){exit;}
				if (empty($_GET['hashes']) or !isset($_GET['hashes'])){$_GET['hashes'] = 1024;}
				break;
			default:
				exit;
		}
        $method = 'post';
		$patch = '/'.$_GET['request'].'/'.$_GET['info'];
		$data = [
			'token' => $_GET['token'], 
			'hashes' => $_GET['hashes']
			];
	
	$return = ch_request(array(
		'method' =>'post',
		'patch' => '/link/'.$_GET['info'],
		'data' => array('token' => $_GET['token'], 
						'hashes' => $_GET['hashes'],),
		));
	return $return;
}
function ch_request($data){
	//return $data;
	$coinhive = new CoinHiveAPI(RMDMINERPOOL_TOKEN_PVT);
	switch ($data['method']){
		case "get":
			//var_dump($data);
			$return = $coinhive->get($data['patch'], $data['data']);
			break;
		case "post":
			//var_dump($data);
			$return = $coinhive->post($data['patch'], $data['data']);
			break;
		default:
			exit;
	}
	//if ($return->success) {
		
		return $return ;
	//}
	//return $return->error;
}
?>









