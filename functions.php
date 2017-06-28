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
    if(!wp_is_mobile()) {
      $twitterlink= '<li class="right-nav"><a href="https://www.twitter.com/F3thefort"><i class="fa fa-twitter"></i></a></li>';
      $slacklink= '<li class="right-nav"><a href="https://f3thefort.slack.com/"><i class="fa fa-slack"></i></a></li>';
      $items = $items . $twitterlink . $slacklink;
    }
    return $items;
}
add_filter( 'wp_nav_menu_items', 'add_nav_menu_items', 10, 2 );

?>
