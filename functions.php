<?php

// add bootstrap js to the header
function scripts_load_bootstrap()
{
     
  wp_register_script( 'bootstrap-js-cdn', 
    '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js',
    array('jquery'), null, false );
  wp_enqueue_script( 'bootstrap-js-cdn' );
}
add_action( 'wp_enqueue_scripts', 'scripts_load_bootstrap' );

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

// Filter the wp_nav_menu() to add your new menu item
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
    $postmetacontent = '</div>';
  }
  $content = $premetacontent . $content . $postmetacontent;
  return $content;
}
add_action('the_content','metawrap_content_div');


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
      /* This is for troubleshooting old posts only
      array(
        'id' => $prefix . 'the_pax',
        'type' => 'textarea',
        'name' => esc_html__( 'PAX', 'metabox-online-generator' ),
        'desc' => esc_html__( 'Comma separated list of PAX in Attendance', 'metabox-online-generator' ),
      ),
      */
    ),
  );

  return $meta_boxes;
}
add_filter( 'rwmb_meta_boxes', 'get_blast_metabox' );

// REMOVE POST META BOXES
function remove_my_post_metaboxes() {
  remove_meta_box( 'formatdiv','post','normal' ); // Format Div
  remove_meta_box( 'postcustom','post','normal' ); // Custom Fields
  remove_meta_box( 'trackbacksdiv','post','normal' ); // Trackback and Pingback
  remove_meta_box( 'postexcerpt','post','normal' ); // Custom Excerpt
  remove_meta_box( 'slugdiv','post','normal' ); // Custom Slug
  remove_meta_box( 'commentstatusdiv','post','normal' ); // Allow Comments
}
add_action('admin_menu','remove_my_post_metaboxes');

?>
