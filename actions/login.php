<?php

// get model
global $CONFIG;
require_once(dirname(dirname(__FILE__)) . "/models/OAuth.php");
require_once(dirname(dirname(__FILE__)) . "/models/twitteroauth.php");
require_once(dirname(dirname(__FILE__)) . "/models/secret.php");

$connection = new TwitterOAuth($consumer_key, $consumer_secret);
$return_url=elgg_add_action_tokens_to_url($CONFIG->wwwroot . "action/twitterlogin/return");
$request_token = $connection->getRequestToken($return_url);
$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

$url = $connection->getAuthorizeURL($token, FALSE);
forward($url);
exit;
?>