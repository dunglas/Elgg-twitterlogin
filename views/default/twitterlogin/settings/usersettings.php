<?php
	/**
	 * User settings for twitterlogin.
	 * 
	 * @package twitterlogin
	 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html GNU Public License version 2
	 * @author Kevin Jardine <kevin@radagast.biz>
	 * @copyright Curverider 2009
	 * @link http://elgg.org/
	 */

	$options = array(elgg_echo('twitterlogin:settings:yes')=>'yes',
		elgg_echo('twitterlogin:settings:no')=>'no',
	);
	
	$user = page_owner_entity();
    if (!$user) {    	
    	$user = $_SESSION['user'];
    }
    
    $subtype = $user->getSubtype();

	if( $subtype == 'twitter') {
		$twitter_controlled_profile = $user->twitter_controlled_profile;
	
		if (!$twitter_controlled_profile) {
			$twitter_controlled_profile = 'yes';
		}
?>
	<h3><?php echo elgg_echo('twitterlogin:twitter_user_settings_title'); ?></h3>
	
	<p><?php echo elgg_echo('twitterlogin:twitter_user_settings_description'); ?></p>
	
<div><p><?php
	echo elgg_view('input/radio',array('internalname' => "twitter_controlled_profile", 'options' => $options, 'value' => $twitter_controlled_profile));
?></p></div>	 
<?php 
} else if (!$subtype) {
	 	$twitter_screen_name = $user->twitter_screen_name;
		?>
	<h3><?php echo elgg_echo('twitterlogin:twitter_login_title'); ?></h3>
	
	<p><?php echo elgg_echo('twitterlogin:twitter_login_description'); ?></p>
	<table><tr><td style="width:200px"><p><?php echo elgg_echo('twitterlogin:twitter_login_title'); ?>:</p></td><td style="width:200px"><p>
<?php
		echo elgg_view('input/text',array('internalname' => "twitter_screen_name", 'options' => $options, 'value' => $twitter_screen_name));
	 }
?></p></td></tr></table>