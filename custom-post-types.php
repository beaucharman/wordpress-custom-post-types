<?php
/*	
  
  lt3 Custom Post Types
  
------------------------------------------------
  custom-post-types.php 1.0 
  Sunday, 27th January 2013
  Beau Charman | @beaucharman | http://beaucharman.me

  For more information about registering Post Types:
  http://codex.wordpress.org/Function_Reference/register_post_type

  For information about setting up custom columns:
  http://tareq.wedevs.com/2011/07/add-your-custom-columns-to-wordpress-admin-panel-tables/

  You can also turn the custom post types declarations into a plugin. 
  For more information: http://codex.wordpress.org/Writing_a_Plugin
  
  To declare a custom post type, simply add a new custom post type array to the 
  `$custom_post_types` master array, with required key and value pairs of:
    'slug_singluar' => '',
    'slug_plural'   => '',
    'name_singular' => '',
    'name_plural'   => '',
  
  and optional pairs of:       
    'description'   => '',
    'public'        => true,
    'menu_position' => 20,
    'menu_icon'     => NULL,
    'hierarchical'  => true,
    'supports'      => array(''),
    'taxonomies'    => array(''),
    'has_archive'   => true,  
    'rewrite'       => '' 
------------------------------------------------ */

/* 

 Declare custom post types

------------------------------------------------ */
$custom_post_types = array();

/*

  Register custom post types

------------------------------------------------ */
add_action('init', 'lt3_create_custom_post_types');
function lt3_create_custom_post_types() 
{
  global $custom_post_types;
  foreach($custom_post_types as $cpt)
  {
    $labels = array(
      'name'               => __($cpt['name_plural']),
      'singular_name'      => __($cpt['name_singular']),
      'add_new_item'       => __('Add New '. $cpt['name_singular']),
      'edit_item'          => __('Edit '. $cpt['name_singular']),
      'new_item'           => __('New '. $cpt['name_singular']),
      'view_item'          => __('View '. $cpt['name_singular']),
      'search_items'       => __('Search '. $cpt['name_plural']),
      'not_found'          => __('No '. $cpt['name_plural'] .' found'),
      'not_found_in_trash' => __('No '. $cpt['name_plural'] .' found in Trash')
    );
    register_post_type(
      $cpt['slug_singlular'], array(
        'labels'        => $labels,
        'description'   => ($cpt['description']) ? $cpt['description'] : '',
        'public'        => ($cpt['public']) ? $cpt['public'] : true,
        'menu_position' => ($cpt['menu_position']) ? $cpt['menu_position'] : 20,
        'menu_icon'     => ($cpt['menu_icon']) ? $cpt['menu_icon'] : NULL,
        'hierarchical'  => ($cpt['hierarchical']) ? $cpt['hierarchical'] : false,
        'supports'      => ($cpt['supports']) ? $cpt['supports'] : array('title', 'editor', 'thumbnail'),
        'taxonomies'    => ($cpt['taxonomies']) ? $cpt['taxonomies'] : array(),
        'has_archive'   => ($cpt['has_archive']) ? $cpt['has_archive'] : true,
        'rewrite'       => ($cpt['rewrite']) ? $cpt['rewrite'] : $cpt['name_plural']
      )
    );
  }
}

/* 

  Change title placeholder for custom post types
  
------------------------------------------------ */
add_filter('enter_title_here', 'custom_post_type_title_text');
function custom_post_type_title_text()
{
  global $custom_post_types;
  $screen = get_current_screen();
  foreach($custom_post_types as $cpt)
  {
    if ($cpt['slug_plural'] == $screen->post_type) 
    {
      $title = 'Enter '. $cpt['name_singular'] .' Title Here';
      break;
    }
  }
  return $title;
}

/* 

  Flush permalink rewrites after creating custom post types and taxonomies
  
------------------------------------------------ */
// add_action('init', 'lt3_post_type_and_taxonomy_flush_rewrites');
function lt3_post_type_and_taxonomy_flush_rewrites() 
{
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
