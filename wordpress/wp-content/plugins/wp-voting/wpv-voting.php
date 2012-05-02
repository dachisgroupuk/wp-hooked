<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
/*
Plugin Name: WP Voting
Plugin URI: http://www.wpclue.com/development/plugins/wordpress-voting-plugin/
Description: Enables site owner to add voting functionality to the blog posts.
Version: 1.7
Author: Tristan Min
Author URI: http://www.wpclue.com/
*/

/*
 * db version
 * @since 1.5.1
 */
global $wpv_voting_db_version;
$wpv_voting_db_version = "1.7";

/*
 * Installation
 * Create two tables (wpv_voting and wpv_voting_meta)
 * wpv_voting tbl to store voted posts
 * wpv_voting_meta tbl to store voted posts' additional data
 * @since 1.0
 */
if(!function_exists('wpv_voting_dbinstall')){
    function wpv_voting_dbinstall() {
        global $wpdb, $wpv_voting_db_version;
        $charset_collate = '';
        
	if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if(!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}
	}

        $query = "CREATE TABLE ".$wpdb->prefix."wpv_voting (
              ID bigint(20) unsigned NOT NULL auto_increment,
              post_id bigint(20) unsigned NOT NULL,
              author_id bigint(20) unsigned NOT NULL,
              vote_count bigint(20) NULL,
              PRIMARY KEY  (ID)
            ); 
              CREATE TABLE ".$wpdb->prefix."wpv_voting_meta (
              post_id bigint(20) unsigned NOT NULL,
              voter_id bigint(20) unsigned NOT NULL,
              vote_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
              voter_ip varchar(40) NOT NULL,
              KEY post_id (post_id)
            ) $charset_collate;";
        require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($query);
        
        ### Create options
        add_option("wpv-voting-db-version", $wpv_voting_db_version);
        add_option('wpv-top-voted-scount', '5');
        
        ### upgrade for WP version below 3.1
        $installed_ver = get_option("wpv-voting-db-version");
        if($installed_ver != $wpv_voting_db_version){
            $query = "CREATE TABLE ".$wpdb->prefix."wpv_voting (
                  ID bigint(20) unsigned NOT NULL auto_increment,
                  post_id bigint(20) unsigned NOT NULL,
                  author_id bigint(20) unsigned NOT NULL,
                  vote_count bigint(20) NULL,
                  PRIMARY KEY  (ID)
                ); 
                  CREATE TABLE ".$wpdb->prefix."wpv_voting_meta (
                  post_id bigint(20) unsigned NOT NULL,
                  voter_id bigint(20) unsigned NOT NULL,
                  vote_date datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                  voter_ip varchar(40) NOT NULL,
                  KEY post_id (post_id)
                ) $charset_collate;";
            require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta($query);

            update_option("wpv-voting-db-version", $wpv_voting_db_version);
        }
    }
    register_activation_hook(__FILE__, 'wpv_voting_dbinstall');
}


/*
 * Upgrade
 * Since WP 3.1 the register_activation_hook is not called when a plugin is updated
 * So need this function to call wp_voting_dbinstall()
 * @since 1.6
 */
if(!function_exists('wpv_voting_upgrade')){
    function wpv_voting_upgrade(){
        global $wpv_voting_db_version;
        if(get_site_option('wpv-voting-db-version' != $wpv_voting_db_version)){
            wpv_voting_dbinstall();
        }
    }
    add_action('plugins_loaded', 'wpv_voting_upgrade');
}


/*
 * Shortcode
 * @since 1.4
 * [wpvoting]
 */
if(!function_exists('wpv_voting_shortcode')){
    function wpv_voting_shortcode($atts){
        global $post;
        return wpv_voting_display_vote($post->ID);
    }
    add_shortcode( 'wpvoting', 'wpv_voting_shortcode');
}


/*
 * Load required files
 * Create menu for admin
 */
include_once ('wpv-admin-voting-list.php');
include_once ('wpv-voting-func.php');
add_action('admin_menu', 'wpv_admin_voting_list');


/*
 * Load widgets
 */
include_once ('wpv-voting-widgets.php');


/*
 * Load admin css only on options page
 * @Since 1.3
 */
if(!function_exists('wpv_voting_load_admin_styles')){
    function wpv_voting_load_admin_styles(){
        wp_register_style('wpv-admin-styles', WP_PLUGIN_URL . '/wp-voting/styles/admin.css');
        wp_enqueue_style('wpv-admin-styles');
    }
}


/*
 * Load custom css for frontend
 * @since 1.3
 */
if(!function_exists('wpv_voting_header')){
    function wpv_voting_header(){
        if(get_option('wpv-custom-css'))
            echo "\n<!-- WP Voting custom CSS - begin -->\n<style type='text/css'>\n" . get_option( 'wpv-custom-css' ) . "\n</style>\n<!-- WP Voting custom CSS - end -->\n\n";
    }
    add_action('wp_head', 'wpv_voting_header');
}


/*
 * Create Frontend pop-up box
 * Show pop-up box for voting
 * URLs to login and registration pages
 * @since 1.0
 */
if(!function_exists('wpv_voting_footer')){
    function wpv_voting_footer(){
    ?>
    <div class="wpvregcon" id="wpvregbox">
        <div class="wpvregconbg">&nbsp;</div>
        <div class="wpvregpopup" id="wpvregpopupdiv">
            <a href="javascript:wpv_regclose();" title="Close"><img class="wpvregclosebtn" src="<?php echo WP_PLUGIN_URL ?>/wp-voting/images/closebutton.png" /></a>
            <?php echo wpv_voting_alert_msg(); ?>
        </div>
    </div>
    <?php
    }
    
    ### Load only public vote is not allowed
    $allow_public_vote = get_option('wpv-allow-public-vote');
    if(empty($allow_public_vote) || $allow_public_vote == null || $allow_public_vote == 'No'){
        add_action('wp_footer', 'wpv_voting_footer');
    }
}


/*
 * Load required scripts
 * 'wpv-userregister' for open and close require to login to vote popup box
 * 'wpv-voterajax' for submitting the ajax request
 * define ajaxurl and nonce
 * @since 1.0
 */
if(!function_exists('wpv_voting_load_scripts')){
    function wpv_voting_load_scripts(){
        $wpv_nonce = wp_create_nonce('wpv_submit_nonce');
        wp_enqueue_script('wpv_userregister', WP_PLUGIN_URL.'/wp-voting/scripts/wpv-userregister.js', false, false, false);
        wp_enqueue_script('wpv_voterajax', WP_PLUGIN_URL.'/wp-voting/scripts/wpv-voterajax.js', array('jquery'));
        wp_localize_script('wpv_voterajax', 'wpvAjax', array('ajaxurl' => admin_url('admin-ajax.php'), 'wpv_nonce' => $wpv_nonce,));
    }
    add_action('wp_print_scripts', 'wpv_voting_load_scripts');
}


/*
 * Load required css
 * wpv-voting.css
 * @since 1.0
 */
if(!function_exists('wpv_voting_load_styles')){
    function wpv_voting_load_styles(){
        wp_register_style('wpv_style', WP_PLUGIN_URL.'/wp-voting/styles/wpv-voting.css');
        wp_enqueue_style('wpv_style');
    }
    add_action('wp_print_styles', 'wpv_voting_load_styles');
}


/*
 * Voting ajax
 * Check security via nonce
 * @since 1.0
 */
if(!function_exists('wpv_voting_ajax_submit')){
    function wpv_voting_ajax_submit(){
        $nonce = $_POST['wpv_nonce'];

        if(!wp_verify_nonce($nonce, 'wpv_submit_nonce'))
            wp_die('Don\'t Cheat!');

        $postID = $_POST['postID'];
        $userID = $_POST['userID'];
        $authorID = $_POST['authorID'];
        $userIP = wpv_get_the_ip();

        if(!empty($postID) && ($userID >= 0) && !empty($authorID) && !empty($userIP)){
            if(wpv_voting_vote($postID, $userID, $authorID, $userIP)){
                $response = wpv_voting_get_vote($postID, $authorID);
            }
            else {
                $response = "Error: Voting! Please try again later.";
            }
        }
        echo $response;
        exit;
    }
    ### Non-logged in user
    add_action( 'wp_ajax_nopriv_wpv-submit', 'wpv_voting_ajax_submit');
    ### Logged in user
    add_action( 'wp_ajax_wpv-submit', 'wpv_voting_ajax_submit' );
}

/*
 * Ajax updating widget
 * @since 1.7
 */
if(!function_exists('wpv_top_ajax_submit')){
    function wpv_top_ajax_submit(){
        $nonce = $_POST['wpv_nonce'];

        if(!wp_verify_nonce($nonce, 'wpv_submit_nonce'))
            wp_die('Don\'t Cheat!');

        $showcount = get_option('wpv-top-voted-scount');

        if($showcount !== FALSE){
            echo wpv_top_voted_calc($showcount);
        }
        exit;
    }
    ### Non-logged in user
    add_action( 'wp_ajax_nopriv_wpv-top-widget', 'wpv_top_ajax_submit');
    ### Logged in user
    add_action( 'wp_ajax_wpv-top-widget', 'wpv_top_ajax_submit' );
}

/*
 * Implement voting function to show it on the frontend
 * Integrate admin voting feature on/off here
 * Check allow post author to vote his own posts here
 * Integrate custom vote and voted text
 * Intefrate custom vote and voted button
 * @since 1.0
 */
if(!function_exists('wpv_voting_display_vote')){
    function wpv_voting_display_vote($postID){
        global $user_ID, $user_login;
        $user_IP = wpv_get_the_ip();
        $author_ID = get_the_author_meta('ID');

        ### Get current vote count
        $curr_votes = wpv_voting_get_vote($postID, $author_ID);
        
        ### Allow or disallow post author to vote his own posts
        $allow_author_vote = get_option('wpv-allow-author-vote');
        if(empty ($allow_author_vote) || $allow_author_vote == null || $allow_author_vote == 'No'){
            $allow_author_vote = false;
        }
        else {
            $allow_author_vote = true;
        }
        
        ### Allow or disallow public vote check
        $allow_public_vote = get_option('wpv-allow-public-vote');
        if(empty($allow_public_vote) || $allow_public_vote == null || $allow_public_vote == 'No'){
            $allow_public_vote = false;
        }
        else {
            $allow_public_vote = true;
        }
        
        ### Get custom vote count text
        $voted_custom_txt = get_option('wpv-voted-custom-txt');
        if(empty($voted_custom_txt))
            $voted_custom_txt = 'voted';
        
        ### Get custom vote button text
        $vote_btn_custom_txt = get_option('wpv-vote-btn-custom-txt');
        if(empty($vote_btn_custom_txt))
            $vote_btn_custom_txt = 'vote';

        ### Voting feature in On
        if(get_option ('wpv-voting-onoff') == 'On'){

            ### Registered user
            if (is_user_logged_in() || $allow_public_vote) {
                
                ### Unlogged in
                if(!is_user_logged_in() && $allow_public_vote)
                    $user_ID = 0;
                
                ### Cannot vote their own post (Voting is disallowed) and show vote count and voted btn
                if($user_ID == $author_ID && !$allow_author_vote){
                    ?>
                    <div class="wpv_postvote">
                        <span class="wpv_votewidget" id="wpvvotewidget<?php the_ID(); ?>">
                            <span class="wpv_votecount" id="wpvvotecount<?php the_ID(); ?>">
                                <span class="wpv_vcount"><?php echo $curr_votes; ?> </span>
                                <?php echo $voted_custom_txt; ?>
                            </span>
                            <span class="wpv_votebtncon">
                                <span class="wpv_votebtn" id="wpvvoteid<?php the_ID(); ?>">
                                    <span class="wpv_voted_icon"></span>
                                    <span class="wpv_votebtn_txt wpv_votedbtn_txt"><?php echo $vote_btn_custom_txt; ?></span>
                                </span>
                            </span>
                        </span>
                    </div>
                    <?php
                }
                ### Voting is allowed
                else {

                    ### New vote, so allowed and show vote count and vote btn
                    if(!wpv_voting_user_voted($postID, $user_ID, $author_ID, $user_IP)) {
                        ?>
                        <div class="wpv_postvote">
                            <span class="wpv_votewidget" id="wpvvotewidget<?php the_ID(); ?>">
                                <span class="wpv_votecount" id="wpvvotecount<?php the_ID(); ?>">
                                    <img title="Loading" alt="Loading" src="<?php bloginfo('url') ?>/wp-content/plugins/wp-voting/images/ajax-loader.gif" class="loadingimage" style="visibility: hidden; display: none;"/>
                                    <span class="wpv_vcount"><?php echo $curr_votes; ?> </span>
                                    <?php echo $voted_custom_txt; ?>
                                </span>

                                <span class="wpv_votebtncon">
                                    <span class="wpv_votebtn" id="wpvvoteid<?php the_ID(); ?>">
                                        <a title="vote" class="wpv_voting" href="javascript:void(0)" >
                                            <span class="wpv_vote_icon"></span>
                                            <span class="wpv_votebtn_txt"><?php echo $vote_btn_custom_txt; ?></span>
                                            <input type="hidden" class="postID" value="<?php echo $postID; ?>" />
                                            <input type="hidden" class="userID" value="<?php echo $user_ID;  ?>" />
                                            <input type="hidden" class="authorID" value="<?php echo $author_ID; ?>" />
                                        </a>
                                        <span class="wpv_voted_icon" style="display: none;"></span>
                                        <span class="wpv_votebtn_txt wpv_votedbtn_txt" style="display: none;"><?php echo $vote_btn_custom_txt; ?></span>
                                    </span>
                                </span>
                            </span>
                        </div>
                        <?php
                    }
                    ### Already voted, so disallowed and show vote count and voted btn
                    else {
                        ?>
                        <div class="wpv_postvote">
                            <span class="wpv_votewidget" id="wpvvotewidget<?php the_ID(); ?>">
                                <span class="wpv_votecount" id="wpvvotecount<?php the_ID(); ?>">
                                    <span class="wpv_vcount"><?php echo $curr_votes; ?> </span>
                                    <?php echo $voted_custom_txt; ?>
                                </span>
                                <span class="wpv_votebtncon">
                                    <span class="wpv_votebtn" id="wpvvoteid<?php the_ID(); ?>">
                                        <span class="wpv_voted_icon"></span>
                                        <span class="wpv_votebtn_txt wpv_votedbtn_txt"><?php echo $vote_btn_custom_txt; ?></span>
                                    </span>
                                </span>
                            </span>
                        </div>
                        <?php
                    }
                }
            }
            ### Public vote is not allowed
            else {
                ?>
                <div class="wpv_postvote">
                    <span class="wpv_votewidget" id="wpvvotewidget<?php the_ID(); ?>">
                        <span class="wpv_votecount" id="wpvvotecount<?php the_ID(); ?>">
                            <span class="wpv_vcount"><?php echo $curr_votes; ?> </span><?php echo $voted_custom_txt; ?>
                        </span>
                        <span class="wpv_votebtncon">
                            <span class="wpv_votebtn" id="wpvvoteid<?php the_ID(); ?>">
                                <a title="vote" href="javascript:wpv_regopen();">
                                    <span class="wpv_vote_icon"></span>
                                    <span class="wpv_votebtn_txt"><?php echo $vote_btn_custom_txt; ?></span>
                                </a>
                            </span>
                        </span>
                    </span>
                </div>
                <?php
            }
        }
        ### Voting feature is off, so show only vote count
        else {
            ?>
            <div class="wpv_postvote">
                <span class="wpv_votewidget" id="wpvvotewidget<?php the_ID(); ?>">
                    <span class="wpv_votecount" id="wpvvotecount<?php the_ID(); ?>">
                        <span class="wpv_vcount"><?php echo $curr_votes; ?> </span>
                        <?php echo $voted_custom_txt; ?>
                    </span>
                </span>
            </div>
            <?php
        }
    }
}
?>