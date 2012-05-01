<?php 
/* * *
 * Processed form data into a proper post array, uses wp_insert_post() to add post. 
 * 
 * @param array $pfs_data POSTed array of data from the form
 */
require('../../../wp-load.php');
error_reporting(E_ALL);
ini_set('display_errors',1);

/**
 * Create post from form data, including uploading images
 * @param array $post
 * @param array $files
 * @return string success or error message.
 */
function pfs_submit($post,$files){
	$pfs_options_arr = get_option('pfs_options');
	$pfs_options = $pfs_options_arr[0];
	$pfs_data = $post;
	$pfs_files = $files;
	//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($pfs_data, true)."</pre>\n";
	//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($pfs_files, true)."</pre>\n";
	
    $title = $pfs_data['title'];
    $postcontent = $pfs_data['postcontent'];
    
    $name = (array_key_exists('name',$pfs_data)) ? esc_html($pfs_data['name'],array()) : '';
    $email = (array_key_exists('email',$pfs_data)) ? sanitize_email($pfs_data['email']) : '';
    
    $taxonomies = array();

	$imgAllowed = 0;
	$result = Array(
		'image'=>"",
		'error'=>"",
		'success'=>"",
		'post'=>""
	);
	$success = False;
	$upload = False;
	
	if ( !current_user_can('publish_posts') && $pfs_options['allow_anon'] && $pfs_options['enable_captcha'] ){
	    require_once('recaptchalib.php');
	    $privatekey = $pfs_options_arr['recaptcha_private_key'];
	    $resp = recaptcha_check_answer ($privatekey,
	                                $_SERVER["REMOTE_ADDR"],
	                                $_POST["recaptcha_challenge_field"],
	                                $_POST["recaptcha_response_field"]);
	}
    if ( !current_user_can('publish_posts') && $pfs_options['allow_anon'] && $pfs_options['enable_captcha'] && !$resp->is_valid ) {
        // What happens when the CAPTCHA was entered incorrectly
        $result['error'] = printf(__("Incorrect reCAPTCHA: %s",'pfs_domain'), $resp->error);
    } else {
    	//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($pfs_files['image']['name'], true)."</pre>\n";
        if (array_key_exists('image',$pfs_files)) { 
            /* play with the image */
            switch (True) {
            case (1 < count($pfs_files['image']['name'])):
                // multiple file upload
                $result['image'] = "multiple";
                $file = $pfs_files['image'];
                for ( $i = 0; $i < count($file['tmp_name']); $i++ ){
                    if( ''!=$file['tmp_name'][$i] ){
                        $imgAllowed = (getimagesize($file['tmp_name'][$i])) ? True : (''==$file['name'][$i]);
                        if ($imgAllowed){
                            $upload[$i+1] = upload_image(array('name'=>$pfs_files["image"]["name"][$i], 'tmp_name'=>$pfs_files["image"]["tmp_name"][$i]));
		                    if (False === $upload[$i+1]){
		                        $result['error'] = __("There was an error uploading the image.",'pfs_domain');
		                    } else {
		                        $success[$i+1] = True;
		                    }
                        } else {
                            $result['error'] = __("Incorrect filetype. Only images (.gif, .png, .jpg, .jpeg) are allowed.",'pfs_domain');
                        }
                    }
                }
                break;
            case ((1 == count($pfs_files['image']['name'])) && ('' != $pfs_files['image']['name'][0]) ):
                // single file upload
                $file = $pfs_files['image'];
                $result['image'] = 'single';
                $imgAllowed = (getimagesize($file['tmp_name'][0])) ? True : (''==$file['name'][0]);
                if ($imgAllowed){
                    $upload[1] = upload_image( array( 'name'=>$file["name"][0], 'tmp_name'=>$file["tmp_name"][0] ) );
                    //echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($upload, true)."</pre>\n";
                    if (False === $upload[1]){
                        $result['error'] = __("There was an error uploading the image.",'pfs_domain');
                    } else {
                        $success[1] = True;
                    }
                } else {
                    $result['error'] = __("Incorrect filetype. Only images (.gif, .png, .jpg, .jpeg) are allowed.",'pfs_domain');
                }
                break;
            default: 
                $result['image'] = 'none';
            }
        }
        if ( '' != $result['error'] ) return $result; // fail if the image upload failed.
        
        //echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($upload, true)."</pre>\n";
        //echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($success, true)."</pre>\n";
        
        /* manipulate $pfs_data into proper post array */
        $has_content_things = ($title != '') && ($postcontent != '');
        if ( !current_user_can('publish_posts') && $pfs_options['allow_anon'] ) $has_content_things = $has_content_things && ($name != '') && is_email($email);
        if ( $has_content_things ) {
            $content = $postcontent;
            if ( !current_user_can('publish_posts') && $pfs_options['allow_anon'] ) $content .= apply_filters('pfs_submittedby_text',"<p>Submitted by <a href='mailto:$email'>$name</a></p>");
            if ( is_user_logged_in() ){
	            global $user_ID;
	            get_currentuserinfo();
	        }
            if (is_array($success)){
                foreach(array_keys($success) as $i){
                    $imgtag = "[!--image$i--]";
                    if (False === strpos($content,$imgtag)) $content .= "\n\n$imgtag";
                    $content = str_replace($imgtag, wp_get_attachment_link( $upload[$i], $pfs_options['wp_image_size']), $content);
                }
            } 
            //if any [!--image#--] tags remain, they are invalid and should just be deleted.
            $content = preg_replace('/\[\!--image\d*--\]/','',$content);

			// $terms[{tax name}] = array(term1, term2, etc)
			if ( array_key_exists('terms',$pfs_data) ) {
				foreach ($pfs_data['terms'] as $taxon => $terms){
					if ( !is_taxonomy_hierarchical($taxon) ) {
						$pfs_data['terms'][$taxon] = implode(',',$terms);
					}
				}
			}

            $postarr = array();
            $postarr['post_title'] = $title;
            $postarr['post_content'] = apply_filters('comment_text', $content);
            $postarr['comment_status'] = $pfs_options['comment_status'];
            $postarr['post_status'] = $pfs_options['post_status'];
            $postarr['post_author'] = ( is_user_logged_in() ) ? $user_ID : $pfs_options['default_author'];
            $postarr['tax_input'] = (array_key_exists('terms',$pfs_data)) ? $pfs_data['terms'] : array();
            $postarr['post_type'] = $pfs_options['post_type'];
            //echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($postarr, true)."</pre>\n";
            $post_id = wp_insert_post($postarr);
            
            if (0 == $post_id) {
                $result['error'] = __("Unable to insert post- unknown error.",'pfs_domain');
            } else {
                $result['success'] = __("Post added, please wait to return to the previous page.",'pfs_domain');
                $result['post'] = $post_id;
            }
        } else {
             $result['error'] = __("You've left a field empty. All fields are required",'pfs_domain');
        }
    }
	return $result;
}

/**
 * Upload images
 */
function upload_image($image){
    $file = wp_upload_bits( $image["name"], null, file_get_contents($image["tmp_name"]));
    //echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">";
    //var_dump($file);
    //echo "</pre>\n";
    if (false === $file['error']) {
        $wp_filetype = wp_check_filetype(basename($file['file']), null );
        $attachment = array(
         'post_mime_type' => $wp_filetype['type'],
         'post_title' => preg_replace('/\.[^.]+$/', '', basename($file['file'])),
         'post_content' => '',
         'post_status' => 'inherit'
        );
        $attach_id = wp_insert_attachment( $attachment, $file['file'] );
        // you must first include the image.php file
        // for the function wp_generate_attachment_metadata() to work
        require_once(ABSPATH . "wp-admin" . '/includes/image.php');
        $attach_data = wp_generate_attachment_metadata( $attach_id, $file['file'] );
        wp_update_attachment_metadata( $attach_id,  $attach_data );
        return $attach_id;
    } else {
        //TODO: er, error handling?
        return false;
    }
}

if (!empty($_POST)){
	$pfs = pfs_submit($_POST,$_FILES);
	echo json_encode($pfs);
	//echo "<pre style=\"border:1px solid #ccc;margin-top:5px;\">".print_r($pfs, true)."</pre>\n";
} else {
	/* TODO: translate following */
	_e('You should not be seeing this page, something went wrong.','pfs_domain');
	echo "<a href='".get_bloginfo('url')."'>" . __('Go home?','pfs_domain') . "</a>";
}

//get_footer();
?>
