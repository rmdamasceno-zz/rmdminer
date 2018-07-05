<?php
//Protege contra acesso direto
if ( !function_exists( 'is_admin' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	die;
}

function addmainscriptapi(){
	return '<script type="text/javascript" src="' . RMDMINERPOOL_ASSETSURI  .  'js/script.js" async></script>';
}

function adduiscriptapi(){
	if (empty(RMDMINERPOOL_TOKEN_PVT)){
		return '
					<script type="text/javascript">console.log(">> Miner: Chave Publica não configurada!")</script>
		';
	}
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
	if (empty(RMDMINERPOOL_TOKEN_PVT)){
		return 'Chave Privada não configurada';
	}
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