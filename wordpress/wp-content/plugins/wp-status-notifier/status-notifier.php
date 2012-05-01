<?php
/*
Plugin Name: WP Status Notifier
Plugin URI: http://wordpresssupplies.com/wordpress-plugins/status-notifier/
Description: Sends email notification of posts pending review.
Version: 1.3.1
Author: iDope
Author URI: http://wordpresssupplies.com/
*/

/*  Copyright 2008  Saurabh Gupta  (email : saurabh0@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// to set default config on activation
register_activation_hook(__FILE__,'status_notifier_defaults');

function status_notifier_defaults() {
    add_option('notificationemails',get_option('admin_email'));
    add_option('approvednotification','yes');
    add_option('declinednotification','yes');
}


// Hook for adding admin menus
add_action('admin_menu', 'sn_add_option_page');

// action function for above hook
function sn_add_option_page() {
    // Add a new submenu under options:
    add_options_page('Status Notifications', 'Status Notifications', 'edit_themes', 'status_notifier', 'sn_options_page');
}

function sn_options_page() {
	if(isset($_POST['save'])) {
      update_option('notificationemails',$_POST['notificationemails']);
      update_option('approvednotification',$_POST['approvednotification']);
      update_option('declinednotification',$_POST['declinednotification']);
	  echo "<div id='message' class='updated fade'><p>Notification settings saved.</p></div>";
    }
    ?>
	<div class="wrap"><h2>Post Status Notifications</h2>
	<form name="site" action="" method="post" id="notifier">

	<div id="review">
	<fieldset id="pendingdiv">
	<legend><b><?php _e('Pending Review Notifications') ?></b></legend>
	<div><input type="text" size="50" name="notificationemails" tabindex="1" id="notificationemails" value="<?php echo attribute_escape(get_option('notificationemails')); ?>"><br />
    Enter email addresses which should be notified of posts pending review (comma separated).
	</div>
	</fieldset>
	<br />

	<fieldset id="reviewdiv">
	<legend><b><?php _e('Post Review Notifications') ?></b></legend>
	<div>
    <label for="approvednotification" class="selectit"><input type="checkbox" tabindex="2" id="approvednotification" name="approvednotification" value="yes" <?php if(get_option('approvednotification')=='yes') echo 'checked="checked"'; ?> /> Notify contributor when their post is approved</label><br />
    <label for="declinednotification" class="selectit"><input type="checkbox" tabindex="3" id="declinednotification" name="declinednotification" value="yes" <?php if(get_option('declinednotification')=='yes') echo 'checked="checked"'; ?> /> Notify contributor when their post is declined (sent back to drafts)</label>
    </div>
	</fieldset>
	<br />
	<p class="submit">
	<input name="save" type="submit" id="savenotifier" tabindex="6" style="font-weight: bold;" value="Save Settings" />
	</p>
	</div>
	</form>
	<small><a href="http://wordpresssupplies.com/wordpress-plugins/status-notifier/">Powered by WP Status Notifier</a></small>
	</div>
	<?php
}

// Hook for post status changes
add_filter('transition_post_status', 'notify_status',10,3);
function notify_status($new_status, $old_status, $post) {
    global $current_user;
	$contributor = get_userdata($post->post_author);
    if ($old_status != 'pending' && $new_status == 'pending') {
      $emails=get_option('notificationemails');
      if(strlen($emails)) {
        $subject='['.get_option('blogname').'] "'.$post->post_title.'" pending review';
        $message="A new post by {$contributor->display_name} is pending review.\n\n";
        $message.="Author   : {$contributor->user_login} <{$contributor->user_email}> (IP: {$_SERVER['REMOTE_ADDR']})\n";
        $message.="Title    : {$post->post_title}\n";
		$category = get_the_category($post->ID);
		if(isset($category[0])) 
			$message.="Category : {$category[0]->name}\n";;
        $message.="Review it: ".get_option('siteurl')."/wp-admin/post.php?action=edit&post={$post->ID}\n\n\n";
        $message.="Powered by: WP Status Notifier <http://wordpresssupplies.com/wordpress-plugins/status-notifier/>";
        wp_mail( $emails, $subject, $message);
      }
	} elseif ($old_status == 'pending' && $new_status == 'publish' && $current_user->ID!=$contributor->ID) {
      if(get_option('approvednotification')=='yes') {
        $subject='['.get_option('blogname').'] "'.$post->post_title.'" approved';
        $message="{$contributor->display_name},\n\nYour post has been approved and published at ".get_permalink($post->ID)." .\n\n";
        $message.="By {$current_user->display_name} <{$current_user->user_email}>\n\n\n";
        $message.="Powered by: WP Status Notifier <http://wordpresssupplies.com/wordpress-plugins/status-notifier/>";
        wp_mail( $contributor->user_email, $subject, $message);
      }
	} elseif ($old_status == 'pending' && $new_status == 'draft' && $current_user->ID!=$contributor->ID) {
      if(get_option('declinednotification')=='yes') {
        $subject='['.get_option('blogname').'] "'.$post->post_title.'" declined';
        $message="{$contributor->display_name},\n\nYour post has not been approved. You can edit the post at ".get_option('siteurl')."/wp-admin/post.php?action=edit&post={$post->ID} .\n\n";
        $message.="By {$current_user->display_name} <{$current_user->user_email}>\n\n\n";
        $message.="Powered by: WP Status Notifier <http://wordpresssupplies.com/wordpress-plugins/status-notifier/>";
        wp_mail( $contributor->user_email, $subject, $message);
      }
	}
}