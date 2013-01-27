WordPress-Custom-Post-Types
===========================

A php script to help generate, register and maintain WordPress custom post types easily.

Add this to your **functions.php** file, or make it an include file to keep your template files clean.

For more information about registering Post Types, visit the [WordPress Codex](http://codex.wordpress.org/Function_Reference/register_post_type).

For information about setting up custom columns, have a read of [this article](http://tareq.wedevs.com/2011/07/add-your-custom-columns-to-wordpress-admin-panel-tables/).

You can also turn the custom post types declarations into a plugin. For more information: http://codex.wordpress.org/Writing_a_Plugin
  
To declare a custom post type, simply add a new custom post type array to the `$custom_post_types` master array, with required key and value pairs of:
```php
  'slug_singluar' => '',
  'slug_plural'   => '',
  'name_singular' => '',
  'name_plural'   => '',
```
and optional pairs of:
```php
  'description'   => '',
  'public'        => true,
  'menu_position' => 20,
  'menu_icon'     => NULL,
  'hierarchical'  => true,
  'supports'      => array(''),
  'taxonomies'    => array(''),
  'has_archive'   => true,  
  'rewrite'       => '' 
```

For example, to create a custom post type of **Cat**:

```php
$custom_post_types = array(
  array(
    'slug_singluar' => 'cat',
    'slug_plural'   => 'cats',
    'name_singular' => 'Cat',
    'name_plural'   => 'Cats',
    'description'   => 'The Cats post type to store the adventures of various cats',
    'public'        => true,
    'menu_position' => 15,
    'supports'      => array('title', 'editor', 'thumbnail'),
    'taxonomies'    => array('color', 'breed')
  )
);
```
