<?php
/** 
 * hp_trends_listing.php 
 * include file for functions.php
 *
 * provides tends_listing_callback for ajax call 
 * that requests a list of trends for trend correlation
 *
 * @author Rich Holman
 * @param  ajax call can send: 
 * 	array slugs
 *
 */
function make_query_args($post_type, $slugs, $post_format=null) {
  $out = array(
    'post_type' => $post_type,
    'post_status' => 'publish',
    'posts_per_page' => 50,
    'tax_query' => array(
      array(
        'taxonomy' => 'region',
        'field' => 'slug',
        'terms' => preg_split("/,\s*/", $slugs),
        'operator' => 'AND'
      ),
      array(
        'taxonomy' => 'audience',
        'field' => 'slug',
        'terms' => preg_split("/,\s*/", $slugs),
        'operator' => 'AND'
      ),
      array(
        'taxonomy' => 'general',
        'field' => 'slug',
        'terms' => preg_split("/,\s*/", $slugs),
        'operator' => 'AND'
      ),
      array(
        'taxonomy' => 'mobile',
        'field' => 'slug',
        'terms' => preg_split("/,\s*/", $slugs),
        'operator' => 'AND'
      )
    )
  );
    
  if ($post_type == 'post') {
    $out['tax_query']['relation'] = 'AND';
    if ($post_format != null) {
      $out['tax_query'][] = array(
        'taxonomy' => 'post_format',
        'field' => 'slug',
        'terms' => $post_format
      );
    } else {
      $out['tax_query'][] = array(
        'taxonomy' => 'post_format',
        'field' => 'slug',
        'terms' => get_taxonomy_slugs('post_format'),
        'operator' => 'NOT IN'
      );
    }
  }
  
  return $out;
}

function print_image_URL($postcontent) {
	$img = preg_match('/src=[\"\'](.*?)[\"\']/i', $postcontent, $matched);
	return $matched[1];
}

function print_post($type) {
?>    
        <?php
        global $post;
        
        switch ($type) {
            case "case":
                ?>
                      <article class="grid" id="post-<?php the_ID(); ?>">    
                        <?php
                         if (get_the_post_thumbnail($post->ID, 'grid-study')) {
                             echo get_the_post_thumbnail($post->ID, 'grid-study');
                         }
                         ?>
              	        <div class="grid-item">	
                            <h3>
                              <a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', '' ), the_title_attribute( 'echo=0' ) ); ?>" rel="bookmark">
                                       <?php the_title() ?>
                              </a>
                   	        </h3>
              				      <?php hp_entry_utility(); ?>
                        </div>   
              		</article>
                <?php break;

              case "list":	
          		?>
          	   <!-- Get the items -->
                <?php hp_list_item(); ?>
                  <?php
                  break;

            default:
        ?>
            <div>
                <a href="<?php the_permalink(); ?>" class="result-title"><?php the_title(); ?></a>
                <?php the_excerpt(); ?>
            </div>
        <?php } ?>
<?php
}
function print_posts($title, $div_id, $query, $posttype) {
    if ($query->have_posts()) {
        print '<div class="result" id="' . $div_id . '">';
        while ($query->have_posts()) : $query->the_post();
                print_post($posttype);
        endwhile;
        print '</div>';
    }
}

function get_taxonomy_slugs($taxonomy) {
  $out = array();
  
  foreach (get_terms($taxonomy) as $term) {
    $out[] = $term->slug;
  }
  
  return $out;
}
 
add_action('wp_ajax_trends_listing', 'trends_listing_callback');
add_action('wp_ajax_nopriv_trends_listing', 'trends_listing_callback');

function trends_listing_callback() {
  $slugs = $_REQUEST['slugs'];
  
  $query = new WP_Query(make_query_args('casestudy', $slugs));
  print_posts("Case Study", "casestudy", $query, 'case');
    
  // Print navigator for updated disabled buttons
  the_widget('Facets_Widget');
  
  die();
}
