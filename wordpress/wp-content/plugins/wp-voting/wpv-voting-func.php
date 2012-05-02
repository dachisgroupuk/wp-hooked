<?php
if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); }
include_once ('wpv-pagination.class.php');

/*
 * setup the post in wpv_voting tbl
 * @since 1.0
 */
if(!function_exists('wpv_voting_set_post')){
    function wpv_voting_set_post($post_ID, $author_ID) {
        global $wpdb;

        ###prevents SQL injection
        $p_ID = $wpdb->escape($post_ID);
        $a_ID = $wpdb->escape($author_ID);

        ###Check if entry exists
        $id_raw = $wpdb->get_var($wpdb->prepare("SELECT ID FROM ".$wpdb->prefix."wpv_voting WHERE post_id = %d AND author_id = %d", $p_ID, $a_ID));
        if ($id_raw != '') {
                ###entry exists, do nothing
        } else {
                ###entry does not exist
                //$init_count = 0;
                $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wpv_voting (post_id, author_id, vote_count) VALUES (%d, %d, '')", $p_ID, $a_ID));
        }
    }
}

/*
 * Get vote count from wpv_voting tbl
 * @return string vote count
 * @since 1.0
 */
if(!function_exists('wpv_voting_get_vote')){
    function wpv_voting_get_vote($post_ID, $author_ID){
        global $wpdb;

        ###prevents SQL injection
        $p_ID = $wpdb->escape($post_ID);
        $a_ID = $wpdb->escape($author_ID);

        ###Create entries if not existant
        wpv_voting_set_post($p_ID, $a_ID);

        $votes = $wpdb->get_var($wpdb->prepare("SELECT vote_count FROM  ".$wpdb->prefix."wpv_voting WHERE post_id = %d AND author_id = %d", $p_ID, $a_ID));

        return $votes;
    }
}

/*
 * Check an user is already voted the post or not
 * @return boolean
 * @since 1.0
 */
if(!function_exists('wpv_voting_user_voted')){
    function wpv_voting_user_voted($post_ID, $user_ID, $author_ID, $user_IP) {
        global $wpdb;

        ### prevents SQL injection
        $p_ID = $wpdb->escape($post_ID);
        $u_ID = $wpdb->escape($user_ID);
        $a_ID = $wpdb->escape($author_ID);
        $u_IP = $wpdb->escape($user_IP);

        ### Create entry if not existant
        wpv_voting_set_post($p_ID, $a_ID);
        
        if($u_ID == 0)
            $voted = $wpdb->get_var($wpdb->prepare("SELECT voter_ip FROM ".$wpdb->prefix."wpv_voting_meta WHERE post_id = %d AND voter_ip = %s AND voter_id = %s", $p_ID, $u_IP, $u_ID));
        else
            $voted = $wpdb->get_var($wpdb->prepare("SELECT voter_id FROM ".$wpdb->prefix."wpv_voting_meta WHERE post_id = %d AND voter_id = %d", $p_ID, $u_ID));
        
        ### Record not found, so not voted yet
        if(empty ($voted) || $voted == NULL)
            $voted = FALSE;
        else
            $voted = TRUE; // already voted

        return $voted;
    }
}

/*
 * Perform voting action here
 * Update the vote count in wpv_voting tbl
 * Insert the voting metadata to wpv_voting_meta tbl
 * @return boolean
 * @since 1.0
 */
if(!function_exists('wpv_voting_vote')){
    function wpv_voting_vote($post_ID, $user_ID, $author_ID, $user_IP) {
        global $wpdb, $current_user;
        $result = FALSE;

        ###Prevents SQL injection
        $p_ID = $wpdb->escape($post_ID);
        $u_ID = $wpdb->escape($user_ID);
        $a_ID = $wpdb->escape($author_ID);
        $u_IP = $wpdb->escape($user_IP);
        //$dt = date('Y-m-d H:i:s');

        ###Prevents fake userID
        if ( is_user_logged_in() ) { 
            get_currentuserinfo();
            if($current_user->ID != $u_ID)
                return $result;
        }

        wpv_voting_set_post($p_ID, $a_ID);

        $curr_count = $wpdb->get_var($wpdb->prepare("SELECT vote_count FROM  ".$wpdb->prefix."wpv_voting WHERE post_id = %d AND author_id = %d", $p_ID, $a_ID));

        if (!wpv_voting_user_voted($p_ID, $u_ID, $a_ID, $u_IP)) {
            $new_count = $curr_count + 1;
            $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."wpv_voting SET vote_count = %d WHERE post_id = %d AND author_id = %d", $new_count, $p_ID, $a_ID));
            $wpdb->query($wpdb->prepare("INSERT INTO ".$wpdb->prefix."wpv_voting_meta (post_id, voter_id, vote_date, voter_ip) VALUES (%d, %d, NOW(), %s)", array($p_ID, $u_ID, $u_IP)));

            $result = TRUE;
        }
        else {
            $result = FALSE;
        }
        return $result;
    }
}

/*
 * Display voting logs to admin user
 * @echo voting table with pagination
 * @since 1.0
 * @todo reset all feature
 */
if(!function_exists('wpv_list_admin_vote_logs')){
    function wpv_list_admin_vote_logs(){
        global $wpdb;
        $ob_par = '';

        ###Prevents fake admin
        if(!current_user_can('manage_options'))
            wp_die('You do not have permission to do that!');

        if(isset($_GET['reset'])){
            if($_GET['reset'] != 'all'){
                $reset_id = (int)$_GET['reset'];
                $wpdb->query($wpdb->prepare("UPDATE ".$wpdb->prefix."wpv_voting SET vote_count = 0 WHERE post_id = %d", $reset_id));
                $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."wpv_voting_meta WHERE post_id = %d", $reset_id));
            }       
            else {
                $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."wpv_voting"));
                $wpdb->query($wpdb->prepare("DELETE FROM ".$wpdb->prefix."wpv_voting_meta"));
            }
        }

        if(isset($_GET['orderby'])){
            if($_GET['orderby'] == 'vote_count'){
                $orderby = 'vote_count';
                $ob_par = '&orderby=vote_count';
            }
            elseif($_GET['orderby'] == 'vote_date'){
                $orderby = 'vote_date';
                $ob_par = '&orderby=vote_date';
            }
        }
        else {
            $orderby = 'vote_date';
        }

        $items = $wpdb->query($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpv_voting_meta"));

        if($items > 0) {
            $p = new wpv_pagination;
            $p->items($items);
            $p->limit(20); // Limit entries per page
            $p->target("admin.php?page=wpv-admin-voting-logs".$ob_par);
            $p->currentPage($_GET[$p->paging]); // Gets and validates the current page
            $p->calculate(); // Calculates what to show
            $p->parameterName('paging');
            $p->adjacents(1); //No. of page away from the current page

            if(!isset($_GET['paging'])) {
                $p->page = 1;
                $pg_link = '';
            } else {
                $p->page = $_GET['paging'];
                $pg_link = '&paging='.$p->page;
            }

            //Query for limit paging
            $limit = "LIMIT " . ($p->page - 1) * $p->limit  . ", " . $p->limit;

        }
        else {
            echo "No Record Found";
            return;
        }
    ?>
        <a style="display:inline-block;margin:5px 0;" class="button" href="?page=wpv-admin-voting-logs&reset=all" onclick="return confirm('Are you sure you wish to delete this record?');">Reset All</a>
        <div class="tablenav">
            <div class='tablenav-pages'>
                <?php echo $p->show();  // Echo out the list of paging. ?>
            </div>
        </div>
        <table class="widefat">
        <thead>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Voter</th>
                <th><a href="?page=wpv-admin-voting-logs&orderby=vote_date<?php echo $pg_link; ?>" title="Order by vote date">Vote date</a></th>
                <th><a href="?page=wpv-admin-voting-logs&orderby=vote_count<?php echo $pg_link; ?>" title="Order by vote count">Current vote count</a></th>
                <th>Reset vote</th>
            </tr>
        </thead>
        <tbody>
    <?php
        $result = $wpdb->get_results($wpdb->prepare("SELECT ".$wpdb->prefix."wpv_voting.post_id, author_id, voter_id, vote_count, vote_date FROM ".$wpdb->prefix."wpv_voting INNER JOIN ".$wpdb->prefix."wpv_voting_meta ON ".$wpdb->prefix."wpv_voting.post_id = ".$wpdb->prefix."wpv_voting_meta.post_id WHERE vote_count <> 0 ORDER BY $orderby DESC $limit"));

        if($result > 0 && !empty($result)){
            foreach($result as $row){
                $post_data = get_post($row->post_id);
                
                if($row->voter_id > 0){
                    $voter_info = get_userdata($row->voter_id);
                    $voter_name = $voter_info->display_name;
                }
                else {
                    $voter_name = "Guest";
                }
                    
                $post_authorID = $post_data->post_author;
                $post_author_info = get_userdata($post_authorID);
                $vote_date = date('d/m/Y H:i a', strtotime($row->vote_date)); //new DateTime($row->vote_date);
                echo '<tr>';
                echo '<td>';
                echo '<a href="'.get_permalink($row->post_id).'" target="_blank">'.$post_data->post_title.'</a>';
                echo '</td>';

                echo '<td>';
                echo $post_author_info->display_name;
                echo '</td>';

                echo '<td>';
                echo $voter_name;
                echo '</td>';

                echo '<td>';
                echo $vote_date; //$vote_date->format('d/m/Y H:i a');
                echo '</td>';

                echo '<td>';
                echo $row->vote_count;
                echo '</td>';

                echo '<td>';
                echo '<a class="button" href="?page=wpv-admin-voting-logs&reset='.$row->post_id.'" >Reset</a>';
                echo '</td>';
                echo '</tr>';
            }
        }
        else {
            echo "<tr><td colspan=\"5\">No Record Found</td></tr>";
        }
    ?>
        </tbody>
        <tfoot>
            <tr>
                <th>Title</th>
                <th>Author</th>
                <th>Voter</th>
                <th><a href="?page=wpv-admin-voting-logs&orderby=vote_date<?php echo $pg_link; ?>" title="Order by vote date">Vote date</a></th>
                <th><a href="?page=wpv-admin-voting-logs&orderby=vote_count<?php echo $pg_link; ?>" title="Order by vote count">Current vote count</a></th>
                <th>Reset vote</th>
            </tr>
        </tfoot>
        </table>
        <div class="tablenav">
            <div class='tablenav-pages'>
                <?php echo $p->show();  // Echo out the list of paging. ?>
            </div>
        </div>
    <?php
    }
}

/*
 * Display alert message if an user is vote a post without login.
 * @return string alert message body
 * @since 1.0
 * @todo add custom login and registration URLs
 */
if(!function_exists('wpv_voting_alert_msg')){
    function wpv_voting_alert_msg(){
        $content = get_option('wpv-voting-alert-msg');
        if(empty ($content) || $content == null){
            $content = '<h3>Please log in to vote</h3>'.
                       '<p>You need to log in to vote. If you already had an account, you may '.
                       '<a href="'. get_option('siteurl').'/wp-login.php" title="Log in">log in</a> here</p>'.
                       '<p>Alternatively, if you do not have an account yet you can '.
                       '<a href="'. get_option('siteurl').'/wp-login.php?action=register" title="Register account">create one here</a>.</p>';
        }
        return $content;
    }
}

/*
 * Get IP address of non-logged in user
 * @since 1.6
 */
if(!function_exists('wpv_get_the_ip')) {
    function wpv_get_the_ip() {
        if (empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["REMOTE_ADDR"];
        } else {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        if(strpos($ip, ',') !== false) {
            $ip = explode(',', $ip);
            $ip = $ip[0];
        }
        return esc_attr($ip);
    }
}

/*
 * Vote count calculator for total vote count widget
 * @since 1.5
 */
if(!function_exists('wpv_total_vote_calc')){
    function wpv_total_vote_calc(){
        global $wpdb;
        $result = $wpdb->get_var($wpdb->prepare("SELECT SUM(vote_count) AS vote_count_sum FROM ".$wpdb->prefix."wpv_voting"));
        return $result;
    }
}

/*
 * Top voted func for top voted widget
 * @since 1.7
 */
if(!function_exists('wpv_top_voted_calc')){
    function wpv_top_voted_calc($showcount){
        global $wpdb;
        $result = $wpdb->get_results($wpdb->prepare("SELECT * FROM ".$wpdb->prefix."wpv_voting INNER JOIN ".$wpdb->prefix."posts ON ".$wpdb->prefix."wpv_voting.post_id = ".$wpdb->prefix."posts.ID WHERE vote_count > 0 ORDER BY ".$wpdb->prefix."wpv_voting.vote_count DESC, ".$wpdb->prefix."posts.post_date DESC LIMIT %d", $showcount));
        if(!empty($result)){
            $output = '<ul>';
            foreach($result as $r){
                $post_data = get_post($r->post_id);
                $post_url = get_permalink($r->post_id);
                $vote_count = $r->vote_count;
                $output .= '<li><a title="'.$post_data->post_title.' - Total voted ('.$vote_count.')" href="'.$post_url.'">'.$post_data->post_title.' ('.$vote_count.')</a></li>';
            }
            $output .= '</ul>';
        }
        return $output;
    }
}
?>