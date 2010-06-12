<?php

$body = '';

$login_key = get_plugin_setting('login_key', 'twitterlogin');
$login_secret = get_plugin_setting('login_secret', 'twitterlogin');

$body .= "<p><b>" . elgg_echo('twitterlogin:title') . "</b></p>";
$body .= '<br />';
$body .= elgg_echo('twitterlogin:details');
$body .= '<br />';
$body .= elgg_echo('twitterlogin:key') . "<br />";
$body .= elgg_view('input/text',array('internalname'=>'params[login_key]','value'=>$login_key));
$body .= elgg_echo('twitterlogin:secret') . "<br />";
$body .= elgg_view('input/text',array('internalname'=>'params[login_secret]','value'=>$login_secret));

echo $body;

?>