<?php
//Add custom meta box
 
// Add the Properties Meta Boxes
 function add_property_metaboxes() {
    add_meta_box('coldharbour_casestudy_client', 'Clients', 'coldharbour_casestudy_client', 'casestudy', 'advanced', 'default');
    add_meta_box('coldharbour_casestudy_goals', 'Goals', 'coldharbour_casestudy_goals', 'casestudy', 'advanced', 'default');
    add_meta_box('coldharbour_casestudy_target_audience', 'Target audience', 'coldharbour_casestudy_target_audience', 'casestudy', 'advanced', 'default');
    add_meta_box('coldharbour_casestudy_solution', 'Solution', 'coldharbour_casestudy_solution', 'casestudy', 'advanced', 'default');
    add_meta_box('coldharbour_casestudy_results', 'Results', 'coldharbour_casestudy_results', 'casestudy', 'advanced', 'default');
}
 
function coldharbour_casestudy_client() {
    global $post;
    $prop_client = get_post_meta($post->ID, '_client', true);
    echo '<label>Clients</label><textarea class="widefat" name="_client">'.$prop_client .'</textarea>';
}

function coldharbour_casestudy_goals() {
    global $post;
    $prop_goals = get_post_meta($post->ID, '_goals', true); 
    echo '<label>Goals</label><textarea class="widefat" name="_goals">'.$prop_goals .'</textarea>';
}

function coldharbour_casestudy_target_audience() {
    global $post;
    $prop_target = get_post_meta($post->ID, '_target', true); 
    echo '<label>Target audience</label><textarea class="widefat" name="_target">'.$prop_target .'</textarea>';
}

function coldharbour_casestudy_solution() {
    global $post;
    $prop_solution = get_post_meta($post->ID, '_solution', true); 
    echo '<label>Solution</label><textarea class="widefat" name="_solution">'.$prop_solution .'</textarea>';
}

function coldharbour_casestudy_results() {
    global $post;
    $prop_results = get_post_meta($post->ID, '_results', true); 
    echo '<label>Results</label><textarea class="widefat" name="_results">'.$prop_results .'</textarea>';
}

// Save the Metabox Data
 
function coldharbour_save_casestudy_meta($post_id, $post) {
    if ( 'casestudy' == get_post_type() ) {
 
        if ( !current_user_can( 'edit_post' , $post ->ID )) return $post ->ID;
        
        $property_meta['_client'] = $_POST['_client'];
        $property_meta['_goals'] = $_POST['_goals'];  
        $property_meta['_target'] = $_POST['_target'];
        $property_meta['_solution'] = $_POST['_solution'];
        $property_meta['_results'] = $_POST['_results'];
        
        foreach ($property_meta as $key => $value) { // Cycle through the $property_meta array!
            if( $post->post_type == 'revision' ) return; // Don't store custom data twice
            $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
            if(get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
                update_post_meta($post->ID, $key, $value);
            } else { // If the custom field doesn't have a value
                add_post_meta($post->ID, $key, $value);
            }
            if(!$value) delete_post_meta($post->ID, $key); // Delete if blank
        }
    }
}
 
add_action('save_post', 'coldharbour_save_casestudy_meta', 1, 2); // save the custom fields
?>