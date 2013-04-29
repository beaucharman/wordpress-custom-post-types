<?php
/**
 * Custom Post Types
 * ------------------------------------------------------------------------
 * custom-post-types.php
 * @version 2.0 | April 1st 2013
 * @author  Beau Charman | @beaucharman | http://beaucharman.me
 * @link    https://github.com/beaucharman/WordPress-Custom-Post-Types
 * @license MIT license
 *
 * Properties
 *  $PostType->name   | string
 *  $PostType->lables | array
 *
 * Methods
 *  $PostType->get()
 *  $PostType->archive_link()
 *
 * To declare a custom post type, simply create a new instance of the
 * LT3_Custom_Post_Type class.
 *
 * Configuration:
 * https://github.com/beaucharman/WordPress-Custom-Post-Types
 *
 * For more information on registering post types:
 * http://codex.wordpress.org/Function_Reference/register_post_type
 */

/* ------------------------------------------------------------------------
   Custom post type class
   ------------------------------------------------------------------------ */
class LT3_Custom_Post_Type
{
  public $name;
  public $labels;
  public $options;
  public $help;

  /**
   * Class constructor
   * __construct()
   * @param  $name     | string
   * @param  $labels   | array
   * @param  $options  | array
   * @param  $help     | array
   * @return post_type | class instance
   *  ------------------------------------------------------------------------ */
  public function __construct( $name, $labels = array(), $options = array(), $help = null )
  {
    $this->name    = $this->uglify_words( $name );
    $this->labels  = $labels;
    $this->options = $options;
    $this->help    = $help;

    if ( !post_type_exists( $this->name ) )
    {
      add_action( 'init', array( &$this, 'register_custom_post_type' ) );
      if ( $this->help )
      {
        add_action( 'contextual_help', array( &$this, 'add_custom_contextual_help' ), 10, 3 );
      }
    }
  }

  /**
   * Register custom post type
   * ------------------------------------------------------------------------
   * register_custom_post_type()
   * @param  null
   * @return post_type
   * ------------------------------------------------------------------------ */
  public function register_custom_post_type()
  {
    /* Create the labels */
    $this->labels['label_singular'] = ( isset( $this->labels['label_singular'] ) )
      ? $this->labels['label_singular'] : $this->prettify_words( $this->name );
    $this->labels['label_plural'] = ( isset( $this->labels['label_plural'] ) )
      ? $this->labels['label_plural'] : $this->plurafy_words( $this->labels['label_singular'] );
    $this->labels['menu_label'] = ( isset( $this->labels['menu_label'] ) )
      ? $this->labels['menu_label'] : $this->labels['label_plural'];

    $labels = array(
      'name'               => __( $this->labels['label_plural'] ),
      'singular_name'      => __( $this->labels['label_singular'] ),
      'menu_name'          => __( $this->labels['menu_label'] ),
      'add_new_item'       => __( 'Add New ' . $this->labels['label_singular'] ),
      'edit_item'          => __( 'Edit ' . $this->labels['label_singular'] ),
      'new_item'           => __( 'New ' . $this->labels['label_singular'] ),
      'all_items'          => __( 'All ' . $this->labels['label_plural'] ),
      'view_item'          => __( 'View ' . $this->labels['label_singular'] ),
      'search_items'       => __( 'Search ' . $this->labels['label_plural'] ),
      'not_found'          => __( 'No ' . $this->labels['label_plural'] . ' found' ),
      'not_found_in_trash' => __( 'No ' . $this->labels['label_plural'] . ' found in Trash' )
     );

    /* Configure the options */
    $options = array_merge(
      array(
        'labels'           => $labels,
        'description'      => '',
        'public'           => true,
        'menu_position'    => 20,
        'menu_icon'        => null,
        'hierarchical'     => false,
        'supports'         => array( 'title', 'editor' ),
        'taxonomies'       => array(),
        'has_archive'      => true,
        'rewrite'          => true
       ),
      $this->options
     );

    /* Register the new post type */
    register_post_type( $this->name, $options );
  }

  /**
   * Add Custom Contextual Help
   * ------------------------------------------------------------------------
   * add_custom_contextual_help()
   * @param  $contextual_help
   * @param  $screen_id | integer
   * @param  $screen
   * @return $contextual_help
   * ------------------------------------------------------------------------ */
  public function add_custom_contextual_help( $contextual_help, $screen_id, $screen )
  {
    foreach( $this->help as $help )
    {
      if ( !$help['context'] )
      {
        $context = $this->name;
      }
      else
      {
        $context = $help['context'] . '-' . $this->name;
      }

      if ( $context == $screen->id )
      {
        $contextual_help = $help['message'];
      }
    }
    return $contextual_help;
  }

  /**
   * Get
   * ------------------------------------------------------------------------
   * get()
   * @param  $user_args | array
   * @return post type data
   *
   * Get all entries assigned to this post type.
   * ------------------------------------------------------------------------ */
  public function get( $user_args = array(), $single = false )
  {
    $args = array_merge(
      array(
      'posts_per_page'  => -1,
      'offset'          => 0,
      'orderby'         => 'post_date',
      'order'           => 'DESC',
      'include'         => '',
      'exclude'         => '',
      'meta_key'        => '',
      'meta_value'      => '',
      'post_type'       => $this->name,
      'post_mime_type'  => '',
      'post_parent'     => '',
      'post_status'     => 'publish',
      'suppress_filters' => true
       ),
      $user_args
     );
    if ( $single )
    {
      $items = get_posts( $args );
      return $items[0];
    }
    return get_posts( $args );
  }

  /**
   * Archive Link
   * ------------------------------------------------------------------------
   * archive_link()
   * @param  none
   * @return string
   * ------------------------------------------------------------------------ */
  public function archive_link()
  {
    return home_url('/'.$this->name);
  }

  /**
   * Prettify Words
   * ------------------------------------------------------------------------
   * prettify_words()
   * @param  $words | string
   * @return string
   *
   * Creates a pretty version of a string, like a pug version of a dog.
   * ------------------------------------------------------------------------ */
  public function prettify_words( $words )
  {
    return ucwords( str_replace( '_', ' ', $words ) );
  }

  /**
   * Uglify Words
   * ------------------------------------------------------------------------
   * uglify_words()
   * @param  $words | string
   * @return string
   *
   * Creates a url firendly version of the given string.
   * ------------------------------------------------------------------------ */
  public function uglify_words( $words )
  {
    return strToLower( str_replace( ' ', '_', $words ) );
  }

  /**
   * Plurify Words
   * ------------------------------------------------------------------------
   * plurafy_words()
   * @param  $words | string
   * @return $words | string
   * Plurifies most common words. Not currently working proper nouns,
   * or more complex words, for example knife => knives, leaf => leaves.
   * ------------------------------------------------------------------------ */
  public function plurafy_words( $words )
  {
    if ( strToLower( substr( $words, -1 ) ) == 'y' )
    {
      return substr_replace( $words, 'ies', -1 );
    }
    if ( strToLower( substr( $words, -1 ) ) == 's' )
    {
      return $words . 'es';
    }
    else
    {
      return $words . 's';
    }
  }
}