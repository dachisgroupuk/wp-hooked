<?php
/* 
 * coldharbour_profile
 * 
 * additional profile fields
 */
add_action('show_user_profile', 'coldharbour_user_profile',6);
add_action('edit_user_profile', 'coldharbour_user_profile',6);
add_action('personal_options_update', 'coldharbour_user_profile_update');
add_action('edit_user_profile_update', 'coldharbour_user_profile_update');


/**
 * coldharbour_user_profiles
 * customise profiles to use extra fields
 * heading not required: <h3><?php _e("Additional profile information", "blank"); ?></h3>
 */
function coldharbour_user_profile($user) {
?>
 
<table class="form-table">
<tr>
<th><label for="gender"><?php _e("Gender"); ?></label></th>
<td>
<?php
	$genders = array('','male','female');
	$user_gender = esc_attr(get_the_author_meta('gender', $user->ID));
	print '<select name="gender">';
	foreach( $genders as $gender ) {
		$s = '';
		if ( $user_gender == $gender) {
			$s = ' selected';
		}
		print '<option value="'.$gender.'"'.$s.'>'.ucfirst($gender).'</option>';
	}
	print '</select>';
?>
<br/>
<span class="description"><?php _e("Please select your gender."); ?></span>
</td>
</tr>
<tr>
<th><label for="twitter"><?php _e("Twitter"); ?></label></th>
<td>
<input type="text" name="twitter" id="twitter" value="<?php echo esc_attr( get_the_author_meta( 'twitter', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your twitter id."); ?></span>
</td>
</tr>

<tr>
<th><label for="facebook"><?php _e("Facebook"); ?></label></th>
<td>
<input type="text" name="facebook" id="facebook" value="<?php echo esc_attr( get_the_author_meta( 'facebook', $user->ID ) ); ?>" class="regular-text" /><br />
<span class="description"><?php _e("Please enter your Facebook id."); ?></span>
</td>
</tr>

<tr>
<th><label for="country"><?php _e("Country"); ?></label></th>
<td>
<?php
	$countries = prepopulate_taxonomy_with_countries('country', 'build');
	$user_country = esc_attr(get_the_author_meta('country', $user->ID));
	print '<select name="country">';
	foreach($countries as $country_id => $country ) {
		$s = '';
		if ( $user_country == $country_id ) {
			$s = ' selected';
		}
		print '<option value="'.$country_id.'"'.$s.'>'.$country.'</a>';
	}
	print '</select>';
?>
</td>
</tr>
</table>
<br/>
<?php
}
/**
 * update profile with new fields as usermeta
 */
function coldharbour_user_profile_update($user_ID) {
	if ( current_user_can( 'edit_user', $user_ID ) ) { 
		$fields = array('country','gender','facebook','twitter');
		foreach($fields as $field) {
			if ( isset($_POST[$field] )) {
				update_usermeta( $user_ID, $field, $_POST[$field]);
			}
		}
	} else {
		return false; 
	}
}