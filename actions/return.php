<?php

// get model

require_once(dirname(dirname(__FILE__)) . "/models/OAuth.php");
require_once(dirname(dirname(__FILE__)) . "/models/twitteroauth.php");
require_once(dirname(dirname(__FILE__)) . "/models/secret.php");
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);
$oauthverifier=get_input('oauth_verifier');
$token = $connection->getAccessToken($oauthverifier);
$connection = new TwitterOAuth($consumer_key, $consumer_secret, $token['oauth_token'], $token['oauth_token_secret']);
$twitterInfo = $connection->get('account/verify_credentials');
//print_r($twitterInfo);

if ($twitterInfo->response && $twitterInfo->response['error']) {
    register_error(sprintf(elgg_echo('twitterlogin:twittererror'),$twitterInfo->response['error']));
} else {	
	// use an alias as the Elgg account username may or may not be the same as the Twitter account username
	$entities = get_entities_from_metadata('twitter_screen_name', $twitterInfo->screen_name, 'user', 'twitter');
	$do_login = false;
	$duplicate_acccount = false;
	
	if (!$entities || $entities[0]->active == 'no') {
		if (!$entities) {
			$entities = get_entities_from_metadata('twitter_screen_name', $twitterInfo->screen_name, 'user');
			if (!$entities) {
				// this account does not exist, so create it
				// currently the username is just set to the Twitter name, but this may change
				
				// check to make sure that a non-Twitter account with the same user name
				// does not already exist
				$username = $twitterInfo->screen_name;
				if(get_user_by_username($username)) {
					// oops, try adding a "_twitter" to the end
					$username .= '_twitter';
					if(get_user_by_username($username)) {
						$duplicate_account = true;			
						register_error(sprintf(elgg_echo("twitterlogin:account_duplicate"),$username));
					}
				}
				if (!$duplicate_account) {								    
			        $user = new ElggUser();
				    $user->email = '';
				    $user->name = $twitterInfo->name;
				    $user->website = $twitterInfo->url;
		    		    $user->location = explode(',',urldecode($twitterInfo->location));
				    $user->access_id = 2;
				    $user->subtype = 'twitter';
				    $user->username = $username;
				    $user->twitter_screen_name = $twitterInfo->screen_name;
                                    $user->twitter_oauth_token = $token['oauth_token'];
                                    $user->twitter_oauth_token_secret = $token['oauth_token_secret'];
				    $user->twitter_controlled_profile = 'yes';
				    $user->icontime = time();

			
				    if ($user->save()) {
				    	$new_account = true;
					    $do_login = true;
					    // need to keep track of subtype because getSubtype does not work
					    // for newly created users in Elgg 1.5
					    $subtype = 'twitter';
				    } else {
			    	    register_error(elgg_echo("twitterlogin:account_create"));
				    }
				} else {
					
				}
			} else {
				$user = $entities[0];
				
				// account is using a Twitter slave login, check to see if this user has been banned
			
			    if (isset($user->banned) && $user->banned == 'yes') { // this needs to change.
			        register_error(elgg_echo("twitterlogin:banned"));
			    } else {
                                    $user->twitter_oauth_token = $token['oauth_token'];
                                    $user->twitter_oauth_token_secret = $token['oauth_token_secret'];
                                    $user->save();

				    $do_login = true;
				    $new_account = false;
				    $subtype = 'elgg';
			    }				
			}
		} else {
			// this is an inactive account
			register_error(elgg_echo("twitterlogin:inactive"));
		}
		
	} else {		
		$user = $entities[0];
		// account is active, check to see if this user has been banned
	    if (isset($user->banned) && $user->banned == 'yes') { // this needs to change.
	        register_error(elgg_echo("twitterlogin:banned"));
	    } else {
                    $user->twitter_oauth_token = $token['oauth_token'];
                    $user->twitter_oauth_token_secret = $token['oauth_token_secret'];
                    $user->save();

		    $do_login = true;
		    $new_account = false;
		    $subtype = 'twitter';
	    }		    
	}
	
	if ($do_login) {				
		$rememberme = get_input('remember',0);
		if (!empty($rememberme)) {
			login($user,true);
		} else {
			login($user);
		}
		
		if (($subtype == 'twitter') && ($user->twitter_controlled_profile != 'no')) {
			// update from Twitter
			$user->briefdescription = $twitterInfo->description;
		    $user->website = $twitterInfo->url;
		    $user->location = explode(',',urldecode($twitterInfo->location));
		    $user->twitter_icon_url_normal = $twitterInfo->profile_image_url;
		    $user->twitter_icon_url_mini = str_replace('_normal.jpg','_mini.jpg',$twitterInfo->profile_image_url);
		}
		
	}
}

forward();

exit;

?>