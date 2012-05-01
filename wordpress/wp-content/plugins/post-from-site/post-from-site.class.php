<?php
/**
 * Plugin Name: Post From Site
 * Plugin URI: http://me.redradar.net/category/plugins/post-from-site/
 * Description: Add a new post/page/{your custom post type} directly from your website.
 * Author: Kelly Dwan
 * Version: 3.0.1
 * Date: 11.20.11
 * Author URI: http://me.redradar.net/
 */
 
/*
TODO
	Focus in Chrome
	Move over to using WP's AJAX handling
	Add ability to post from the toolbar?
*/

/* We need the admin functions to use wp_create_category(). */
require_once(ABSPATH . 'wp-admin' . "/includes/admin.php");
require_once('pfs-widget.php');

class PostFromSite {
	/* global variables */
	protected $linktext = '';
	protected $popup = true;
	protected $cat = '';
	protected $form_id = 0;
	
	public function __construct($id = 0, $linktext = '', $popup = true, $cat = '') {
		$this->form_id = $id;
		$this->linktext = $linktext;
		$this->popup = $popup;
		$this->cat = $cat;
	
		register_activation_hook( __FILE__, array($this,'install') );
		
		// add pfs_domain for translation
		load_plugin_textdomain('pfs_domain');
		
		// add pfs_options group & apply validation filter, add settings fields & section
		add_action('admin_init', array($this, 'admin_init') );
		
		// add js & css
		add_action( 'get_header', array($this,'includes') );
		
		// add admin page & options
		add_action( 'admin_menu', array($this, 'show_settings') );
		
		// add shortcode support
		add_shortcode( 'post-from-site', array($this, 'shortcode') );
		
		// add admin menu item. Probably not going to happen.
		//add_action( 'admin_bar_menu', array($this, 'add_admin_link'), 1000 );
	}
	
	public function install(){
		//nothing here yet, as there's really nothing to 'install' that isn't covered by __construct
	}

	/**
	 * Add options to databases with defaults
	 */
	public function show_settings() {
	    add_options_page('Post From Site', 'Post From Site', 'manage_options', 'pfs', array($this, 'settings') );
	    
	    if (!get_option("pfs_options")) {
	    	$options= array( 0 => array() );
	    	$options[0]['default_author'] = '';
	        $options[0]['allow_image'] = true;
	        $options[0]['wp_image_size'] = 'medium';
	        
	        $options[0]['post_status'] = 'publish';
	        $options[0]['post_type'] = 'post';
	        $options[0]['comment_status'] = 'open';
	        $options[0]['taxonomy'] = array();
	        
	        $options[0]['enable_captcha'] = false;
	        $options[0]['allow_anon'] = false;
	        $options['recaptcha_public_key'] = '';
	        $options['recaptcha_private_key'] = '';
	        add_option ("pfs_options", $options) ;
	    }
	}
	
	/**
	 * What to display in the admin menu
	 */
	public function settings() { ?>
		<div class="wrap pfs">
			<h2><?php _e('Post From Site Settings','pfs_domain'); ?></h2>
	
			<form method="post" action="options.php" id="options">
				<?php settings_fields('pfs_options'); ?>
				
				<?php do_settings_sections('pfs'); ?>
				
				<?php submit_button(); ?>
			</form>
			
		</div>
	<?php 
	}

	function admin_init(){
		register_setting( 'pfs_options', 'pfs_options', array($this, 'validate') );
		add_settings_section('pfs_users', 'User Settings', array($this, 'setting_section_users'), 'pfs');
		add_settings_field('pfs_allow_anon', 'Allow anonymous (not logged in) users to create posts?', array($this, 'setting_allow_anon'), 'pfs', 'pfs_users');
		add_settings_field('pfs_default_user', 'Posts by anonymous users should be created by this author:<br /><small><a href="user-new.php">Create new user?</a></small>', array($this, 'setting_default_author'), 'pfs', 'pfs_users');
		add_settings_field('pfs_enable_captcha', 'Enable <a href="http://www.google.com/recaptcha">Recaptcha</a> for not logged in users? (recommended)', array($this, 'setting_enable_captcha'), 'pfs', 'pfs_users');
		add_settings_field('pfs_recaptcha_public_key', '<a href="http://www.google.com/recaptcha">Recaptcha API</a> public key:', array($this, 'setting_recaptcha_public_key'), 'pfs', 'pfs_users');
		add_settings_field('pfs_recaptcha_private_key', '<a href="http://www.google.com/recaptcha">Recaptcha API</a> private key:', array($this, 'setting_recaptcha_private_key'), 'pfs', 'pfs_users');

		add_settings_section('pfs_post', 'Post Creation Settings', array($this, 'setting_section_post'), 'pfs');
		add_settings_field('pfs_post_status', 'Post status:', array($this, 'setting_post_status'), 'pfs', 'pfs_post');
		add_settings_field('pfs_post_type', 'Post type:<br /><small><a href="http://codex.wordpress.org/Custom_Post_Types">Custom Post Types</a> supported!</small>', array($this, 'setting_post_type'), 'pfs', 'pfs_post');
		add_settings_field('pfs_comment_status', 'Comment status:', array($this, 'setting_comment_status'), 'pfs', 'pfs_post');
		add_settings_field('pfs_taxonomy', 'Allowed taxonomies:', array($this, 'setting_taxonomy'), 'pfs', 'pfs_post');
						
		add_settings_section('pfs_image', 'Image Upload Settings', array($this, 'setting_section_image'), 'pfs');
		add_settings_field('pfs_allow_image', 'Allow users to upload an image?', array($this, 'setting_allow_image'), 'pfs', 'pfs_image');
		add_settings_field('pfs_wp_image_size', 'Image size setting to use:', array($this, 'setting_wp_image_size'), 'pfs', 'pfs_image');
		

	}
	function setting_section_users() {
		echo '<p>By default, all logged-in users can use the post-from-site interface to create a post.</p>';
	}
	function setting_allow_anon() {
		$options = get_option('pfs_options');
		$options = $options[0];
		echo "<input id='pfs_allow_anon' name='pfs_options[0][allow_anon]' size='40' type='checkbox' value='1' ";
		if ( $options['allow_anon'] == true ) echo "checked='checked'";
		echo "/>";
	}
	function setting_default_author() {
		$options = get_option('pfs_options');
		$options = $options[0];
		/* listing of authors */
		wp_dropdown_users( array(
			'blog_id' => get_current_blog_id(),
			'name' => 'pfs_options[0][default_author]',
			'id' => 'pfs_default_author',
			'selected' => $options['default_author']
		) );
	}
	function setting_enable_captcha() {
		$options = get_option('pfs_options');
		$options = $options[0];
		echo "<input id='pfs_enable_captcha' name='pfs_options[0][enable_captcha]' size='40' type='checkbox' value='1' ";
		if ( $options['enable_captcha'] == true ) echo "checked='checked'";
		echo "/>";
	}
	function setting_recaptcha_public_key() {
		$options = get_option('pfs_options');
		echo "<input id='pfs_recaptcha_public_key' name='pfs_options[recaptcha_public_key]' size='40' type='text' value='{$options['recaptcha_public_key']}' />";
	}
	function setting_recaptcha_private_key() {
		$options = get_option('pfs_options');
		echo "<input id='pfs_recaptcha_private_key' name='pfs_options[recaptcha_private_key]' size='40' type='text' value='{$options['recaptcha_private_key']}' />";
	}

	function setting_section_post() {
		echo '<p>Settings for posts created by Post From Site. Defaults to a published Post with comments open, and no taxonomies.</p>';
	}
	function setting_post_status() {
		$options = get_option('pfs_options');
		$options = $options[0];
		echo "<select name='pfs_options[0][post_status]'>";
		echo "<option value='draft' ";
		echo ('draft' == $options['post_status']) ? 'selected' : '' ;
		echo ">". __('Draft','pfs_domain')."</option>";
		echo "<option value='pending' ";
		echo ('pending' == $options['post_status']) ? 'selected' : '' ;
		echo ">".__('Pending','pfs_domain')."</option>";
		echo "<option value='publish' ";
		echo ('publish' == $options['post_status']) ? 'selected' : '' ;
		echo ">". __('Publish','pfs_domain')."</option>";
		echo "</select>";
	}
	function setting_post_type() {
		$options = get_option('pfs_options');
		$options = $options[0];
		echo "<select id='pfs_post_type' name='pfs_options[0][post_type]'>";
        $post_types = get_post_types(array('public'=>true),'object'); 
        foreach ($post_types as $post_type ) {
	        if ("attachment" == $post_type->name) continue;
            if ($post_type->name == $options['post_type']) {
                echo '<option value="'.$post_type->name.'" selected>'.$post_type->labels->singular_name.'</option>';
            } else {
                echo '<option value="'.$post_type->name.'">'.$post_type->labels->singular_name.'</option>';
            }
        }
        echo "</select>";
	}
	function setting_comment_status() {
		$options = get_option('pfs_options');
		$options = $options[0];
		echo "<select id='pfs_comment_status' name='pfs_options[0][comment_status]'>";
		echo "<option value='closed' ";
		echo ('closed' == $options['comment_status']) ? 'selected' : '' ;
		echo ">".__('Closed','pfs_domain')."</option>";
		echo "<option value='open' ";
		echo ('open' == $options['comment_status']) ? 'selected' : '' ;
		echo ">".__('Open','pfs_domain')."</option>";
		echo "</select>";
	}
	function setting_taxonomy() {
		$options = get_option('pfs_options');
		$options = $options[0];
		$taxonomies = get_taxonomies(array( 'public' => true ),'object'); 
		echo "<ul>";
		foreach ($taxonomies as $taxonomy ) {
		  echo '<li><label><input type="checkbox" name="pfs_options[0][taxonomy][]" value="'.$taxonomy->name.'" ';
		  if ( array_key_exists('taxonomy',$options) && is_array($options['taxonomy']) && in_array($taxonomy->name, $options['taxonomy']) ) echo ' checked="checked"';
		  echo '/> '. $taxonomy->labels->name. '</label></li>';
		}
		echo "</ul>";
	}

	function setting_section_image() {
		echo '<p>Main description of this section here.</p>';
	}
	function setting_allow_image() {
		$options = get_option('pfs_options');
		$options = $options[0];
		echo "<input id='pfs_allow_image' name='pfs_options[0][allow_image]' size='40' type='checkbox' value='1' ";
		if ( $options['allow_image'] == 1 ) echo "checked='checked'";
		echo "/>";
	}
	function setting_wp_image_size() {
		$options = get_option('pfs_options');
		$options = $options[0];
		$sizes = get_intermediate_image_sizes();
		echo "<select id='pfs_wp_image_size' name='pfs_options[0][wp_image_size]'>";
        foreach ($sizes as $size ) {
            if ($size == $options['wp_image_size']) {
                echo '<option value="'.$size.'" selected>'.$size.'</option>';
            } else {
                echo '<option value="'.$size.'">'.$size.'</option>';
            }
        }
        echo "</select>";
	}

	/**
	 * Sanitize and validate input. 
	 * @param array $input an array to sanitize
	 * @return array a valid array.
	 */
	public function validate($input) {
	    $ok = array('publish','pending','draft');
	    $users = array();
	    $user_objs = get_users( array(
			'blog_id'	=> $GLOBALS['blog_id'],
			'fields'	=> array( 'ID', 'user_login' )
		) );
		foreach ( $user_objs as $u ){
			$users[] = $u->ID;
		}

	    foreach ($input as $i => $val) {
	    	if (is_array($val)){
		    	$input[$i]['allow_anon'] = array_key_exists('allow_anon',$val);
		    	$input[$i]['default_author'] = (in_array($val['default_author'], $users)) ? $val['default_author'] : 'anon' ;
		    	$input[$i]['enable_captcha'] = array_key_exists('enable_captcha',$val);

		    	$input[$i]['post_status'] = (in_array($val['post_status'],$ok) ? $val['post_status'] : 'pending');
			    $input[$i]['post_type'] = (post_type_exists($val['post_type']) ? $val['post_type'] : 'post');
			    $input[$i]['comment_status'] = ($val['comment_status'] == 'open' ? 'open' : 'closed');
				if ( array_key_exists('taxonomy',$val) ){
				    foreach ( $input[$i]['taxonomy'] as $j => $tax) {
				    	if (!taxonomy_exists($tax)) {
				    		unset($input[$i]['taxonomy'][$j]);
				    	}
				    }
				}
		    	
			    $input[$i]['allow_image'] = array_key_exists('allow_image', $val);
			    $input[$i]['wp_image_size'] = (in_array($val['wp_image_size'],get_intermediate_image_sizes())) ? $val['wp_image_size'] : 'medium';
		    }
		}
		$input['recaptcha_public_key'] = urlencode($input['recaptcha_public_key']);
		$input['recaptcha_private_key'] = urlencode($input['recaptcha_private_key']);
	    return $input;
	}

	/**
	 * Add javascript and css to header files.
	 */
	public function includes(){
	    wp_enqueue_script( 'jquery-multi-upload', plugins_url("includes/jquery.MultiFile.pack.js",__FILE__), array('jquery','jquery-form') );
	    wp_enqueue_script( 'pfs-script', plugins_url("includes/pfs-script.js",__FILE__) );
	    wp_enqueue_style( 'pfs-min-style',  plugins_url("includes/minimal.css",__FILE__) );
	    $theme_css = apply_filters( 'pfs_theme_css', plugins_url("includes/twentyeleven.css",__FILE__) );
		wp_enqueue_style( 'pfs-style',  $theme_css );
	}

	/**
	 * Add shortcode support.
	 * @param $atts shortcode attributes, cat, link, and popup
	 * cat is the category to post to, link is the display text of the link,
	 * and popup decides whether it's an inline form (false) or a popup box (true).
	 */
	function shortcode($atts, $content=null, $code="") {
	    $a = shortcode_atts( array(
	        'link' => 'quick post',
	        'popup' => false,
	        'cat' => ''
	    ), $atts );
	    $pfs = new PostFromSite(0, $a['link'], $a['popup'], $a['cat']);
	    return $pfs->get_form();
	}

	/**
	 * Add a link to show the form from the admin bar	 
	function add_admin_link() {
		global $wp_admin_bar, $wpdb;
		if ( !is_super_admin() || !is_admin_bar_showing() )
			return;
		$this->popup = false;
	    $form = "</a>".$this->get_form();
		/ * Add the main siteadmin menu item * /
		$wp_admin_bar->add_menu( array( 'id' => 'post_from_site', 'title' => __( 'Write a Post', 'pfs_domain' ), 'href' => FALSE ) );
		$wp_admin_bar->add_menu( array( 'parent' => 'post_from_site', 'title' => $form, 'href' => FALSE ) );
	} */

	/**
	 * Creates link and postbox (initially hidden with display:none), calls pfs_submit on form-submission. Echos the form.
	 * @param string $cat Category ID for posting specifically to one category. Default is '', which allows user to choose from allowed categories.
	 * @param string $linktext Link text for post link. Default is set in admin settings, any text here will override that. 
	 * @param bool $popup Whether the box should be a 'modal-style' popup or always display
	 */
	public function form(){
		echo $this->get_form();
	}

	/**
	 * Creates link and postbox (initially hidden with display:none), calls pfs_submit on form-submission. Returns the form.
	 * @param string $cat Category ID for posting specifically to one category. Default is '', which allows user to choose from allowed categories.
	 * @param string $linktext Link text for post link. Default is set in admin settings, any text here will override that. 
	 * @param bool $popup Whether the box should be a 'modal-style' popup or always display
	 */
	public function get_form(){
		$linktext = $this->linktext;
		$cat = $this->cat;
		$popup = $this->popup;
		$id = $this->form_id;
		$pfs_options = get_option('pfs_options');
		$options = $pfs_options[0];
		
		if (''==$linktext) $linktext = apply_filters( 'pfs_default_link_text', __('Click to post.','pfs_domain') );
		$idtext = $cat.sanitize_html_class($linktext);

		// Javascript displays the box when the link is clicked 
		$out = ($popup) ? "<a href='#' class='pfs-post-link' id='$idtext-link'>$linktext</a>" : '';
		$out .= "<div id='pfs-post-box-$idtext' ";
		$out .= ($popup) ? "style='display:none' class='pfs-post-box pfs_postbox'" : "class='pfs-post-box pfs_postform'";
		$out .= ">\n";
		$out .= ($popup) ? "<div class='closex'>&times;</div>\n" : '';
		if (current_user_can('publish_posts') || $options['allow_anon']){
			$out .= "<div id='pfs-alert' style='display:none;'></div> \n";
			$out .= apply_filters( 'pfs_before_form', '', $idtext );
			$out .= "<form class='pfs' id='pfs_form' method='post' action='".plugins_url("pfs-submit.php",__FILE__). "' enctype='multipart/form-data'>\n";
			$out .= "<input type='hidden' name='MAX_FILE_SIZE' value='" .apply_filters('pfs_maxfilesize',3000000). "' />\n";
			$out .= apply_filters( 'pfs_form_start', '', $idtext );
			$out .= "<label for='pfs_title'>". __('Title:','pfs_domain'). "</label> <input name='title' id='pfs_title' value='' type='text' class='input-text' />\n";
			if (!current_user_can('publish_posts') && $options['allow_anon']){ //if not logged in/able to publish posts, and anon posting allowed, show name/email
				$out .= "<label for='pfs_name'>".__('Name:','pfs_domain')."</label> <input name='name' id='pfs_name' class='input-text' value='' type='text' />";
				$out .= "<label for='pfs_email'>".__('Email:','pfs_domain')."</label> <input name='email' id='pfs_email' class='input-text' value='' type='email' />\n";
			}
			$out .= "<label for='postcontent'>". __('Content:','pfs_domain'). "</label><textarea id='postcontent' name='postcontent' rows='12' cols='50'></textarea>\n";
			if ( array_key_exists('taxonomy',$options) ){
				foreach ($options['taxonomy'] as $i => $tax){
					//if ($tax != 'category' || empty($cat)){
						$out .= $this->get_taxonomy_list($tax);
					//}
				}
			}		
			if ($options['allow_image']) {
				$out .= "<label for='pfs_imgdiv$idtext'>". __('Image:','pfs_domain') ."</label>";
				/*$out .= "<script>function ".$idtext."pfs_auto_browse(){ inputs = document.getElementsByName(\"".$idtext."-image[]\"); inputs[inputs.length-1].click(); }</script>";
				$out .= "<input type='button' name='not-image' value='".__('Upload an image','pfs_domain')."' onclick='".$idtext."pfs_auto_browse();' />";*/
				$out .= "<div id='pfs-imgdiv'><input id ='pfs-imgdiv-input' type='file' class='multi' name='image[]' accept='png|gif|jpg|jpeg'/></div>\n";
			}
			$out .= "<div class='clear'></div>\n";
			if ($options['enable_captcha'] && !current_user_can('publish_posts') && $options['allow_anon'] ){
			    if ( !empty($pfs_options['recaptcha_public_key']) ) {
				    require_once('recaptchalib.php');
				    $publickey = $pfs_options['recaptcha_public_key']; // you got this from the signup page
				    $out .= recaptcha_get_html($publickey);
				} else {
					return "<div id='pfs-alert' style='display:none;'>Need recaptcha</div>";
				}
			}
			$out .= apply_filters( 'pfs_before_submit', '', $idtext );
			$out .= "<input type='submit' id='post' class='submit' name='post' value='".__("Post","pfs_domain")."' />\n";
			$out .= apply_filters( 'pfs_form_end', '', $idtext );
			$out .= "</form>\n<div class='clear'></div>\n";
			$out .= apply_filters( 'pfs_after_form', '', $idtext );
		} else {
			$out .= apply_filters( 'pfs_alert_login', "<p>You must be logged in to post.</p>" );
		}
		$out .= "</div>\n\n";
		return $out;
	}

	/**
	 * return the categories
	 * @param string $excluded Categories which are excluded 
	 */
	public function get_taxonomy_list( $taxonomy ){
		$terms = get_terms($taxonomy, array(
			'hide_empty' => 0
		));
		if (!$terms || empty($terms)) return '';
		//preg_match_all('/\s*<option class="(\S*)" value="(\S*)">(.*)<\/option>\s*/', $terms, $matches, PREG_SET_ORDER);
		$out = apply_filters( 'pfs_taxonomy_label', "<label for='terms_$taxonomy'>$taxonomy</label>", $taxonomy );
		$out .= "<select id='terms_$taxonomy' name='terms[$taxonomy][]' size='4' multiple='multiple'>";
		foreach ($terms as $term){
			if (is_taxonomy_hierarchical($taxonomy))
				$out .= "<option value='{$term->term_taxonomy_id}'>{$term->name}</option>";
			else
				$out .= "<option value='{$term->name}'>{$term->name}</option>";
		}
		$out .= "</select><br />\n";
		return apply_filters("pfs_{$taxonomy}_list",$out);
	}

}
$pfs = new PostFromSite();

/**  === === HELPER FUNCTIONS === ===  **/
/**  unused, but left in, 'just in case'  **/
/**
 * Convert number in bytes into human readable format (KB, MB etc)
 * @param int $filesize number in bytes to be converted
 * @return string bytes in human readable form
 */
function display_filesize($filesize){
    if(is_numeric($filesize)) {
        $decr = 1024; $step = 0;
        $prefix = array('B','KB','MB','GB','TB','PB');
        while(($filesize / $decr) > 0.9){
            $filesize = $filesize / $decr;
            $step++;
        }
        return round($filesize,2).$prefix[$step];
    } else {
        return 'NaN';
    }
}

/**
 * Convert string filesize in KB (or MB etc) into integer bytes
 * @param string $filesize size to be converted
 * @return int filesize in bytes
 */
function filesize_bytes($filesize){
    $prefix = array('B'=>0,'KB'=>1,'MB'=>2,'GB'=>3,'TB'=>4);
    preg_match('/([0-9]*{\.[0-9]*}?)([KMGT]?B)/', strtoupper($filesize), $match);
    if ('' != $match[0]) {
        $size = $match[1];
        for ($i = 0; $i < $prefix[$match[2]]; $i++) $size *= 1000;
    }
    return $size;
}

/**
 * Backwards compatibility
 */
function post_from_site($cat = '', $linktext = ''){
	$pfs = new PostFromSite(0, $linktext, true, $cat);
	$pfs->form();
}