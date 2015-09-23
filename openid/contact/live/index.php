<?php
require_once '../../key.php';
require_once '../server.php';
$query =  http_build_query(array(
    'client_id'=> LIVE_APP_ID,
    'display'=>'page',
    'locale'=>'en',
    'redirect_uri'=> LIVE_CALLBACK_URL,
    'response_type'=>'code',
    'scope'=>'wl.signin,wl.basic,wl.contacts_emails',
    'redirect_type'=>'auth',
    'request_ts'=>time(),
    'response_method'=>'cookie',
    'secure_cookie'=>0),null,'&');

$auth_url  = 'https://login.live.com/oauth20_authorize.srf?' . $query;
if(!isset($_REQUEST['r']) or !isset($_REQUEST['code']))
{
    header('location:'. $auth_url);
}else
{
    require('live.php');
    $token = requestAccessTokenByVerifier($_REQUEST['code']);
    $access_token =  $token->access_token;
    $response =  sendRequest('https://apis.live.net/v5.0/me/contacts?access_token='.$access_token);
    $result = json_decode($response,1);
    $contacts = array();
    foreach($result['data'] as $data)
    {
        if(!isset($data['emails']) or !isset($data['emails']['preferred'])){
            continue;
        }
        $contacts[] = array(
            'name'=>$data['name'],
            'email'=>$data['emails']['preferred'],
        );
    }
    
    processResponseDataAndExit($contacts);
    
}