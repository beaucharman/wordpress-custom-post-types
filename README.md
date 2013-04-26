# WordPress Custom Post Types

A php class to help register and maintain WordPress custom post types easily. It also comes with some rad built in properties and methods that can be used in templates to maintain clean code modular development.

For more information about registering Post Types, visit the [WordPress Codex](http://codex.wordpress.org/Function_Reference/register_post_type).

For information about setting up custom columns, have a read of [this article](http://tareq.wedevs.com/2011/07/add-your-custom-columns-to-wordpress-admin-panel-tables/).

### Declaring New Post Types

Include `custom-post-types.php` in your `functions.php` file.

Declare the various argument arrays to set the nerw post type up as needed (`$name` is required):

```PHP
// Required
$name = '';

// Optional
$labels = array(
  'label_singular' => '',
  'label_plural'   => '',
  'menu_label'     => ''
);

// Optional
$options = array(
  'description'    => '',
  'public'         => true,
  'menu_position'  => 20,
  'menu_icon'      => null,
  'hierarchical'   => false,
  'supports'       => array( '' ),
  'taxonomies'     => array( '' ),
  'has_archive'    => true,
  'rewrite'        => true
);

// Optional
$help = array(
  array(
    'message'      => ''
  ),
  array(
    'context'      => 'edit',
    'message'      => ''
  )
);
```
Then create variable (for future refernce, but is not required) from an instance of the `LT3_Custom_Post_Type` class:

```PHP
$PostType = new LT3_Custom_Post_Type( $name, $labels, $options, $help );
```

### Flush Rewrites

If there are issues with permalinks and the new post types, even after flushing them in the administrator area (Settings > Permalinks > Save Changes) use the following function to flush permalink rewrites after creating custom post types and taxonomies.

```PHP
add_action( 'init', 'lt3_post_type_and_taxonomy_flush_rewrites' );
function lt3_post_type_and_taxonomy_flush_rewrites()
{
  global $wp_rewrite;
  $wp_rewrite->flush_rules();
}
```
### Usage

The class creates a handfull of useful properties and methods that can be access through post type's instance variable.

#### Properties

**$PostType->name**

The post type slug.

**$PostType->lables**

A an array of the singular, plural and menu lables.

#### Methods

**$PostType->archive_link()**

Get the absolute permalink to the post type's archive page.

**$PostType->get()**

Get all entries assigned to this post type. Accepts an array of arguments, and a boolean value to retrieve just a single value (true, useful to use along side 'include' => $single_id) or an array of results (false).

See the [get_posts #Default_Usage](http://codex.wordpress.org/Template_Tags/get_posts#Default_Usage) codex reference for the list of possible arguments, and the [get_pages #Return](http://codex.wordpress.org/Function_Reference/get_pages#Return) codex reference for the list of return items.

For example:

```PHP
$post_types = $PostType->get();
```

**Note:** A declaration of `global $PostType;` might be required on some template files.