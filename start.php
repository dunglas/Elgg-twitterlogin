<?php

	/**
	 * Elgg twitterlogin plugin
	 * 
	 * @package ElggTwitterLogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Kevin Jardine <kevin@radagast.biz>
	 * @copyright Curverider Ltd 2009
	 * @link http://elgg.org/
	 */
	 
	 global $CONFIG;

	/**
	 * Twitterlogin initialisation
	 *
	 * These parameters are required for the event API, but we won't use them:
	 * 
	 * @param unknown_type $event
	 * @param unknown_type $object_type
	 * @param unknown_type $object
	 */

		function twitterlogin_init() {
			
	        	        
	        // Extend system CSS with our own styles
			extend_view('css','twitterlogin/css');
                        extend_view('account/forms/login', 'twitterlogin/login');
			
			register_plugin_hook('usersettings:save','user','twitterlogin_user_settings_save');
    			
        }
        
        function twitterlogin_pagesetup() {
        	// make profile edit links invisible for twitter accounts
        	// that do not have twitter control explicitly turned off
        	if ((get_context() == 'profile') 
        		&& ($page_owner_entity = page_owner_entity()) 
        		&& ($page_owner_entity->getSubtype() == "twitter")
        		&& ($page_owner_entity->twitter_controlled_profile != 'no')
        	) {
        		extend_view('metatags','twitterlogin/hide_profile_embed');
        	}
        	
        	extend_elgg_settings_page('twitterlogin/settings/usersettings', 'usersettings/user');
        }
				
		register_elgg_event_handler('init','system','twitterlogin_init');
		register_elgg_event_handler('pagesetup','system','twitterlogin_pagesetup');
		
		// TODO: remove this permissions hook if it turns out not to be necessary
		
		function twitterlogin_can_edit($hook_name, $entity_type, $return_value, $parameters) {
         
         $entity = $parameters['entity'];
         $context = get_context();
         if ($context == 'twitterlogin' && $entity->getSubtype() == "twitter") {
             // should be able to do anything with Twitter user data
             return true;
         }
         return null;
  
     }
     
     function twitterlogin_icon_url($hook_name,$entity_type, $return_value, $parameters) {
     	$entity = $parameters['entity'];
     	if (($entity->getSubtype() == "twitter") && ($entity->twitter_controlled_profile != 'no')) {
     		if (($parameters['size'] == 'tiny') || ($parameters['size'] == 'topbar')) {
     			return $entity->twitter_icon_url_mini;
     		} else {
     			return $entity->twitter_icon_url_normal;
     		}
     	}
     }
     
     function twitterlogin_user_settings_save() {    	
    	gatekeeper();
    	
    	$user = page_owner_entity();
    	if (!$user) {    	
    		$user = $_SESSION['user'];
    	}
    	
    	$subtype = $user->getSubtype();
    	
    	if ($subtype == 'twitter') {
    	
    	    $twitter_controlled_profile = get_input('twitter_controlled_profile','yes');
    	
	    	if ((!$user->twitter_controlled_profile && ($twitter_controlled_profile == 'no'))
	    		|| ($user->twitter_controlled_profile && ($user->twitter_controlled_profile != $twitter_controlled_profile))
	    	) {    	
	    		$user->twitter_controlled_profile = $twitter_controlled_profile;	    
	    		system_message(elgg_echo('twitterlogin:twitter_user_settings:save:ok'));
	    	}
    	} else if (!$subtype) {
    		
    		// currently on users with no subtype (regular Elgg users) are allowed a
    		// slave Twitter login
    		$twitter_screen_name = get_input('twitter_screen_name');
    		if ($twitter_screen_name != $user->twitter_screen_name) {
    			$user->twitter_screen_name = $twitter_screen_name;
    			system_message(elgg_echo('twitterlogin:twitter_login_settings:save:ok'));
    		}
    	}
	}

      register_plugin_hook('permissions_check','user','twitterlogin_can_edit');
      register_plugin_hook('entity:icon:url','user','twitterlogin_icon_url');
		
		// Register actions
		global $CONFIG;
		register_action("twitterlogin/login",true,$CONFIG->pluginspath . "twitterlogin/actions/login.php");
		register_action("twitterlogin/return",true,$CONFIG->pluginspath . "twitterlogin/actions/return.php");
				
?>