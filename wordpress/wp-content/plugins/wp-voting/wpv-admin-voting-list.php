<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }

/*
 * Register necessary admin menus here
 * Added wpv-allow-author-vote in v1.2
 * Added wpv-voted-custom-txt in v1.3
 * Added wpv-vote-btn-custom-txt in v1.3
 * Added admin_print_styles in v1.3
 * Added wpv-custom-css in v1.3
 * Added wpv-allow-public-vote in v1.6
 * @since 1.0
 */
if(!function_exists('wpv_admin_voting_list')){
    function wpv_admin_voting_list(){
        add_menu_page('Voting', 'Voting', 'administrator', 'wpv-admin-voting-logs','wpv_admin_voting_logs');
        $page = add_submenu_page('wpv-admin-voting-logs', 'Voting Options', 'Voting Options', 'administrator', 'wpv-admin-voting-options', 'wpv_admin_voting_options');
        add_action('admin_print_styles-'. $page, 'wpv_voting_load_admin_styles');
        register_setting('wpv_admin_voting_form_options', 'wpv-voting-onoff', '');
        register_setting('wpv_admin_voting_form_options', 'wpv-allow-author-vote', '');
        register_setting('wpv_admin_voting_form_options', 'wpv-voted-custom-txt', '');
        register_setting('wpv_admin_voting_form_options', 'wpv-vote-btn-custom-txt');
        register_setting('wpv_admin_voting_form_options', 'wpv-custom-css');
        register_setting('wpv_admin_voting_form_options', 'wpv-voting-alert-msg', '');
        register_setting('wpv_admin_voting_form_options', 'wpv-allow-public-vote', '');
    }
}

/*
 * Admin voting logs menu
 * @since 1.0
 */
if(!function_exists('wpv_admin_voting_logs')){
    function wpv_admin_voting_logs(){
    ?>
    <div class="wrap">
        <h2><?php _e('Voting Logs'); ?></h2>
        <?php
            if(current_user_can('manage_options')){
                wpv_list_admin_vote_logs();
            }
        ?>
    </div>
    <?php
    }
}


/*
 * Admin voting options
 * Added wpv-allow-author-vote in v1.2
 * Fixed initial selected state for options in v1.2.1
 * Added wpv-voted-custom-txt in v1.3
 * Added wpv-vote-btn-custom-txt in v1.3
 * Added wpv-custom-css in v1.3
 * Added wpv-allow-public-vote in v1.6
 * @since 1.0
 */
if(!function_exists('wpv_admin_voting_options')){
    function wpv_admin_voting_options(){
    ?>
    <div class="wrap">
        <h2><?php _e('Voting Options'); ?></h2>
        <div class="postbox-container" style="width: 65%">
            <div class="metabox-holder">
                <div class="meta-box-sortables ui-sortable">
                    <form method="post" action="options.php" id="wpv-admin-voting-options">
                        <?php 
                            $onoff = get_option('wpv-voting-onoff');
                            $allow_author_vote = get_option('wpv-allow-author-vote');
                            $allow_public_vote = get_option('wpv-allow-public-vote');
                        ?>
                        <div id="wpvsettings" class="postbox">
                            <div title="Click to toggle" class="handlediv"><br></div>
                            <h3 class="hndle"><span>WP Voting Settings</span></h3>
                            <div class="inside">
                                <table class="form-table">
                                    <tr>
                                        <!-- Options section -->
                                        <td width="65%">
                                            <table>
                                                <tr valign="top">
                                                    <th scope="row">Voting feature On/Off</th>
                                                    <td>
                                                        <input type="radio" name="wpv-voting-onoff" value="On" <?php if($onoff == 'On') echo 'checked="checked"'; ?> /> On
                                                        <input type="radio" name="wpv-voting-onoff" value="Off" <?php if($onoff == 'Off' || empty($onoff)) echo 'checked="checked"'; ?> /> Off
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row">Allow post author to vote his own posts</th>
                                                    <td>
                                                        <input type="radio" name="wpv-allow-author-vote" value="Yes" <?php if($allow_author_vote == 'Yes') echo 'checked="checked"'; ?> /> Yes
                                                        <input type="radio" name="wpv-allow-author-vote" value="No" <?php if($allow_author_vote == 'No' || empty($allow_author_vote)) echo 'checked="checked"'; ?> /> No
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row">Allow public(unregistered or non logged in) users to vote</th>
                                                    <td>
                                                        <input type="radio" name="wpv-allow-public-vote" value="Yes" <?php if($allow_public_vote == 'Yes') echo 'checked="checked"'; ?> /> Yes
                                                        <input type="radio" name="wpv-allow-public-vote" value="No" <?php if($allow_public_vote == 'No' || empty($allow_public_vote)) echo 'checked="checked"'; ?> /> No
                                                    </td>
                                                </tr>
                                                <tr vlaign="top">
                                                    <th scope="row">Vote count custom text <br /><strong><i>(default: "voted")</i></strong></th>
                                                    <td>
                                                        <input type="text" name="wpv-voted-custom-txt" value="<?php echo get_option('wpv-voted-custom-txt'); ?>" />
                                                    </td>
                                                </tr>
                                                <tr vlaign="top">
                                                    <th scope="row">Vote button custom text <br /><strong><i>(default: "vote")</i></strong></th>
                                                    <td>
                                                        <input type="text" name="wpv-vote-btn-custom-txt" value="<?php echo get_option('wpv-vote-btn-custom-txt'); ?>" />
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row">Custom CSS <br /><strong><i>Especially to override vote and voted buttons images</i></th>
                                                    <td>
                                                        <textarea cols="60" rows="15" name="wpv-custom-css"><?php echo get_option('wpv-custom-css'); ?></textarea><br />
                                                    </td>
                                                </tr>
                                                <tr valign="top">
                                                    <th scope="row">
                                                        Alert message for non logged in users
                                                        <br /><strong><i>If "Allow public users to vote feature" is set to "Yes",
                                                            this alert message will not be shown</i></strong>
                                                    </th>
                                                    <td>
                                                        <textarea cols="60" rows="7" name="wpv-voting-alert-msg"><?php echo get_option('wpv-voting-alert-msg'); ?></textarea><br />
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                        <br /><br />
                        <?php settings_fields('wpv_admin_voting_form_options'); ?>
                        <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
                    </form>
                </div>
            </div>
        </div>
        <div class="postbox-container side" style="width:20%;">
            <div class="metabox-holder">
                <div class="meta-box-sortables ui-sortable">
                    <div id="donate" class="postbox" style="display:block;">
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        <h3 class="hndle"><span>Donate</span></h3>
                        <div class="inside">                           
                            <div class="paypal-donations">
                                <p>
                                    I spent countless hours of work on this plugin. 
                                    If you've found this plugin is useful for you, please
                                    consider to donate.
                                </p>
                                <p class="wpv_icode" style="color:red;">
                                    50% of donations will go to charity or temple
                                </p>
                                <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=komin%2emm%40gmail%2ecom&lc=US&item_name=WP%20Voting&no_note=0&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHostedGuest" target="_blank">
                                    <img src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" />
                                </a>
                                <br /><br />
                            </div>                                     
                        </div>
                    </div>
                    <div id="help" class="postbox">
                        <div class="handlediv" title="Click to toggle"><br /></div>
                        <h3 class="hndle"><span>Help</span></h3>
                        <div class="inside">
                            <table>
                                <tr>
                                    <td><br />
                                        <strong>Custom CSS Guide</strong>
                                        <p>
                                            To change the vote button, please follow below steps <br />                                                
                                        </p>
                                        <ol>
                                            <li>
                                                Upload your custom vote button to <span class="wpv_icode">plugins/wp-voting/images/</span> folder via FTP client
                                            </li>
                                            <li>
                                                Use <span class="wpv_icode">.wpv_vote_icon</span> css class to include your uploaded image.
                                                Write your custom css rule in the Custom CSS text box.<br />
                                                Below is the default vote button css rule. <br /><br />
                                                <span class="wpv_icode">
                                                    .wpv_vote_icon { <br />
                                                        background: url('../images/vote-btn.png') no-repeat; <br />
                                                        width: 21px; <br />
                                                        height: 20px; <br />
                                                        display: inline-block; <br />
                                                    }
                                                </span>    
                                            </li>
                                        </ol><br />

                                        <p>
                                            To change the voted button, please follow below steps <br />                                           
                                        </p>
                                        <ol>
                                            <li>
                                                Upload your custom voted button to <span class="wpv_icode">plugins/wp-voting/images/</span> folder via FTP client
                                            </li>
                                            <li>
                                                Use <span class="wpv_icode">.wpv_voted_icon</span> css class to include your uploaded image.
                                                Write your custom css rule in the Custom CSS text box.<br />
                                                Below is the default voted button css rule. <br /><br />
                                                <span class="wpv_icode">
                                                    .wpv_voted_icon { <br />
                                                        background: url('../images/voted-btn.png') no-repeat; <br />
                                                        width: 21px; <br />
                                                        height: 20px; <br />
                                                        display: inline-block; <br />
                                                    }
                                                </span>    
                                            </li>
                                        </ol><br />
                                        
                                        <p>
                                            To style total vote count widget, please follow below steps <br />
                                        </p>
                                        <ol>
                                            <li>
                                                Use this class <span class="wpv_icode">.wpvtcount</span> to style your
                                                total vote count widget. Write your custom css rule in the Custom CSS text box.
                                                <br /><br /> e.g. <br />
                                                <span class="wpv_icode">
                                                    .wpvtcount { <br />
                                                        color: red; <br />
                                                        font-size: 24px; <br />
                                                    }
                                                </span>
                                            </li>
                                        </ol>

                                        <p class="wpv_icode" style="color:red;">
                                            Note: Please use absolute url for your custom images 
                                            in your custom css. Please see the screenshot for example.
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    }
}
?>