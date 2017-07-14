<?php

/* Start loading custom js and css */

// add bootstrap js and theme js to the header
function scripts_load_bootstrap_and_custom_js()
{
     
  wp_register_script( 'bootstrap-js-cdn', 
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
    array('jquery'), null, false );
  wp_enqueue_script( 'bootstrap-js-cdn' );
  wp_register_script( 'theme-functions-js', 
    '//f3thefort.com/wp-content/themes/thefort/theme-functions.js',
    array('jquery'), null, false );
  wp_localize_script( 'theme-functions-js', 'myAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));        

  wp_enqueue_script( 'theme-functions-js' );
}
add_action( 'wp_enqueue_scripts', 'scripts_load_bootstrap_and_custom_js' );

// add bootstrap and font-awesome to the header
function styles_load_custom()
{
  wp_register_style( 'google-fonts-roboto-style',
    '//fonts.googleapis.com/css?family=Roboto',
    array(), '', 'all' );
  wp_enqueue_style( 'google-fonts-roboto-style' );

  wp_register_style( 'font-awesome-style-cdn',
    '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css',
    array(), '4.7.0', 'all' );
  wp_enqueue_style( 'font-awesome-style-cdn' );

  wp_register_style( 'bootstrap-style-cdn',
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css',
    array(), '3.3.7', 'all' );
  wp_enqueue_style( 'bootstrap-style-cdn' );

  wp_register_style( 'bootstrap-theme-style-cdn',
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap-theme.min.css',
    array('bootstrap-style-cdn'), '3.3.7', 'all' );
  wp_enqueue_style( 'bootstrap-theme-style-cdn' );

}
add_action( 'wp_enqueue_scripts', 'styles_load_custom' );
/* End loading custom js and css */

/* Start appending custom menu items */
function add_nav_menu_items($items) {
    if(!is_user_logged_in()) {
      $items = $items. '<li class="right-nav menu-item login-link"><a href="/wp-login.php">Login</a></li>';
    }
    if(!wp_is_mobile()) {
      $twitterlink= '<li class="right-nav menu-item"><a href="https://www.twitter.com/F3thefort"><i class="fa fa-twitter"></i></a></li>';
      $slacklink= '<li class="right-nav menu-item"><a href="https://f3thefort.slack.com/"><i class="fa fa-slack"></i></a></li>';
      $items = $items . $twitterlink . $slacklink;
    }
    return $items;
}
add_filter( 'wp_nav_menu_items', 'add_nav_menu_items', 10, 2 );
/* End appending custom menu items */

/* Start custom content wrapping */
function metawrap_content_div( $content ){
  $custom_fields = get_post_custom();
  $premetacontent = '';
  $postmetacontent = '';
  if ($custom_fields["workout_date"][0] || $custom_fields["qic"][0] || $custom_fields["the_pax"][0]) {
    $premetacontent = $premetacontent . '<div class="meta-tags-content"><div class="well"><ul>';
    if ($custom_fields["qic"][0]) {
      $premetacontent = $premetacontent . '<li><strong>QIC:</strong> <span class="qic">' . $custom_fields["qic"][0] . '</span></li>';
    }
    if ($custom_fields["workout_date"][0]) {
      $premetacontent = $premetacontent . '<li><strong>When:</strong> <span class="workout_date">' . $custom_fields["workout_date"][0] . '</span></li>';
    }
    if ($custom_fields["the_pax"][0]) {
      $premetacontent = $premetacontent . '<li><strong>Pax:</strong> ' . $custom_fields["the_pax"][0] . '</li>';
    }
    $premetacontent = $premetacontent . get_the_tag_list('<li><strong>Pax:</strong> ', ', ', '</li>');
    $premetacontent = $premetacontent . '</ul></div>';
    
    $postmetacontent = $postmetacontent . tclaps_snippet();
    
    $postmetacontent = $postmetacontent . '</div>';
    
  }
  $content = $premetacontent . $content . $postmetacontent;
  return $content;
}


/**
 * Function to create code snippet for tclaps button
 */
function tclaps_snippet() {
  global $post;
  $tclaps = get_post_meta($post->ID, "tclaps", true);

  $tclaps = ($tclaps == "") ? 0 : $tclaps;

  $snippet = '<div class="tclapsection">';
  
  $snippet = $snippet . '<span class="tclapsbox user_tclap" data-post_id="' . $post->ID . '">';
  $snippet = $snippet . '<span>';  
  $snippet = $snippet . '<i class="fa fa-sign-language"></i> TClap | ';
  $snippet = $snippet . '</span><span id="tclap_counter">' . $tclaps . '</span>';
  $snippet = $snippet . '</span>';
  
  $snippet = $snippet . '</div>';
  
  return $snippet;
}

add_action('the_content','metawrap_content_div');
/* End Custom content wrapping */

/* Start TClaps Ajax */
// Note that code above for content wrapping creates the controls, 
// This is simply the logic that is invoked when clicked
add_action("wp_ajax_my_user_tclap", "my_user_tclap");
add_action("wp_ajax_nopriv_my_user_tclap", "my_user_tclap");

function my_user_tclap() {
   $tclap_count = get_post_meta($_REQUEST["post_id"], "tclaps", true);
   $tclap_count = ($tclap_count == '') ? 0 : $tclap_count;
   $new_tclap_count = $tclap_count + 1;

   $tclap = update_post_meta($_REQUEST["post_id"], "tclaps", $new_tclap_count);

   if($tclap === false) {
      $result['type'] = "error";
      $result['tclap_count'] = $tclap_count;
   }
   else {
      $result['type'] = "success";
      $result['tclap_count'] = $new_tclap_count;
   }

   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }
   die();
}

/* End TClaps Ajax */

/* Start Metabox Plugin Configuration for Custom Fields */
function get_blast_metabox( $meta_boxes ) {
  $prefix = '';

  $meta_boxes[] = array(
    'id' => 'blast-metabox',
    'title' => esc_html__( 'Backblast/Preblast Details', 'metabox-online-generator' ),
    'post_types' => array( 'post', 'page' ),
    'context' => 'advanced',
    'priority' => 'default',
    'autosave' => false,
    'fields' => array(
      array(
        'id' => $prefix . 'qic',
        'type' => 'text',
        'name' => esc_html__( 'QIC', 'metabox-online-generator' ),
        'desc' => esc_html__( 'Workout Q', 'metabox-online-generator' ),
      ),
      array(
        'id' => $prefix . 'workout_date',
        'type' => 'date',
        'name' => esc_html__( 'Workout Date', 'metabox-online-generator' ),
        'desc' => esc_html__( 'The actual date of the workout', 'metabox-online-generator'),
        'js_options' => array(
          'dateFormat' => 'mm/dd/yy',
        ),
      ),
      array(
        'id' => $prefix . 'pax_instructions',
        'type' => 'heading',
        'name' => esc_html__( 'Other Instructions', 'metabox-online-generator' ),
        'desc' => esc_html__( 'List Pax at the workout as tags in the box on the right side of this page.  Please include the Q(s) in this list', 'metabox-online-generator' ),
        'std' => 'Header Default',
      ),
    ),
    'validation' => array(
      'rules'    => array(
        "{$prefix}qic" => array(
          'required'  => true,
        ),
        "{$prefix}workout_date" => array(
          'required'  => true,
        ),
      ),
    ),
  );

  return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'get_blast_metabox' );
/* End Metabox Plugin Configuration for Custom Fields */

/* Start filtering post editing metaboxes */
function remove_my_post_metaboxes() {
  remove_meta_box( 'formatdiv','post','normal' ); // Format Div
  remove_meta_box( 'postcustom','post','normal' ); // Custom Fields
  remove_meta_box( 'trackbacksdiv','post','normal' ); // Trackback and Pingback
  remove_meta_box( 'postexcerpt','post','normal' ); // Custom Excerpt
  remove_meta_box( 'slugdiv','post','normal' ); // Custom Slug
  remove_meta_box( 'commentstatusdiv','post','normal' ); // Allow Comments
}
add_action('admin_menu','remove_my_post_metaboxes');
/* End filtering post editing metaboxes */

?>
