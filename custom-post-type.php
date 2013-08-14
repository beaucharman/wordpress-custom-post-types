<?php
/**
 * Custom Post Type
 * ========================================================================
 * custom-post-type.php
 * @version   2.0 | June 30th 2013
 * @author    Beau Charman | @beaucharman | http://www.beaucharman.me
 * @link      https://github.com/beaucharman/wordpress-custom-post-types
 * @license   MIT license
 *
 * Properties
 *  $PostType->name   {string}
 *  $PostType->lables {array}
 *
 * Methods
 *  $PostType->get()
 *  $PostType->archive_link()
 *
 * To declare a custom post type, simply create a new instance of the
 * LT3_Custom_Post_Type class.
 *
 * Configuration guide:
 * https://github.com/beaucharman/wordpress-custom-post-types
 *
 * For more information on registering post types:
 * http://codex.wordpress.org/Function_Reference/register_post_type
 */

/* ========================================================================
   Custom Post Type class
   ======================================================================== */
class LT3_Custom_Post_Type
{
  public $name;
  public $labels;
  public $options;
  public $help;

  /**
   * Class Constructor
   *  ========================================================================
   * __construct()
   * @param  {string}   $name
   * @param  {array}    $labels
   * @param  {array}    $options
   * @param  {array}    $help
   * @return {instance} post type
   *  ======================================================================== */
  public function __construct($name, $labels = array(), $options = array(), $help = null)
  {
    /**
     * Set class values
     */
    $this->name = $this->uglify_words($name);
    $this->labels = $labels;
    $this->options = $options;
    $this->help = $help;

    /**
     * Create the labels where needed
     */
    /* Post type singluar label */
    if (! isset($this->labels['label_singular']))
    {
      $this->labels['label_singular'] = $this->prettify_words($this->name);
    }

    /* Post type plural label */
    if (! isset($this->labels['label_plural']))
    {
      $this->labels['label_plural'] = $this->plurify_words($this->labels['label_singular']);
    }

    /* Post type menu label */
    if (! isset($this->labels['menu_label']))
    {
      $this->labels['menu_label'] = $this->labels['label_plural'];
    }

    /**
     * If the post type doesn't already exist, create it!
     */
    if (! post_type_exists($this->name))
    {
      add_action('init', array(&$this, 'register_custom_post_type'));
      if ($this->help)
      {
        add_action('contextual_help', array(&$this, 'add_custom_contextual_help'), 10, 3);
      }
    }
  }

  /**
   * Register Custom Post Type
   * ========================================================================
   * register_custom_post_type()
   * @param  null
   * @return post_type
   * ======================================================================== */
  public function register_custom_post_type()
  {
    /**
     * Set up the post type labels
     */
    $labels = array(
      'name'               => __($this->labels['label_plural']),
      'singular_name'      => __($this->labels['label_singular']),
      'menu_name'          => __($this->labels['menu_label']),
      'add_new_item'       => __('Add New ' . $this->labels['label_singular']),
      'edit_item'          => __('Edit ' . $this->labels['label_singular']),
      'new_item'           => __('New ' . $this->labels['label_singular']),
      'all_items'          => __('All ' . $this->labels['label_plural']),
      'view_item'          => __('View ' . $this->labels['label_singular']),
      'search_items'       => __('Search ' . $this->labels['label_plural']),
      'not_found'          => __('No ' . $this->labels['label_plural'] . ' found'),
      'not_found_in_trash' => __('No ' . $this->labels['label_plural'] . ' found in Trash')
    );

    /**
     * Configure the post type options
     */
    $options = array_merge(
      array(
        'has_archive'   => true,
        'labels'        => $labels,
        'menu_position' => 20,
        'public'        => true,
        'rewrite'       => array('slug' => $this->get_slug())
      ),
      $this->options
    );

    /**
     * Register the new post type
     */
    register_post_type($this->name, $options);
  }

  /**
   * Add Custom Contextual Help
   * ========================================================================
   * add_custom_contextual_help()
   * @param  $contextual_help
   * @param  $screen_id | integer
   * @param  $screen
   * @return $contextual_help
   * ======================================================================== */
  public function add_custom_contextual_help($contextual_help, $screen_id, $screen)
  {
    foreach ($this->help as $help)
    {
      if (!$help['context'])
      {
        $context = $this->name;
      }
      else
      {
        $context = $help['context'] . '-' . $this->name;
      }

      if ($context == $screen->id)
      {
        $contextual_help = $help['message'];
      }
    }
    return $contextual_help;
  }

  /**
   * Get
   * ========================================================================
   * get()
   * @param  $user_args | array
   * @return post type data
   *
   * Get all entries assigned to this post type.
   * ======================================================================== */
  public function get($user_args = array(), $single = false)
  {
    $args = array_merge(
      array(
        'posts_per_page' => -1,
        'orderby'        => 'title',
        'order'          => 'ASC',
        'post_type'      => $this->name,
        'post_status'    => 'publish'
      ),
      $user_args
    );

    if ($single)
    {
      $items = get_posts($args);
      return $items[0];
    }
    return get_posts($args);
  }

  /**
   * Archive URI
   * ========================================================================
   * archive_uri()
   * @param  none
   * @return string
   * ======================================================================== */
  public function archive_uri($path = '')
  {
    return home_url('/' . $this->get_slug() . '/' . $path);
  }

  /**
   * Get Slug
   * ========================================================================
   * get_slug()
   * @param  $name {string}
   * @return string
   * ======================================================================== */
  public function get_slug($name = null)
  {
    if (! $name)
    {
      $name = $this->name;
    }

    return strtolower(
      str_replace(' ', '-', str_replace('_', '-', $name))
    );
  }

  /**
   * Prettify Words
   * ========================================================================
   * prettify_words()
   * @param  $words | string
   * @return string
   *
   * Creates a pretty version of a string, like a pug version of a dog.
   * ======================================================================== */
  public function prettify_words($words)
  {
    return ucwords(str_replace('_', ' ', $words));
  }

  /**
   * Uglify Words
   * ========================================================================
   * uglify_words()
   * @param  $words | string
   * @return string
   *
   * Creates a url firendly version of the given string.
   * ======================================================================== */
  public function uglify_words($words)
  {
    return strToLower(str_replace(' ', '_', $words));
  }

  /**
   * Plurify Words
   * ========================================================================
   * plurify_words()
   * @param  $words | string
   * @return $words | string
   *
   * Plurifies most common words. Not currently working proper nouns,
   * or more complex words, for example knife => knives, leaf => leaves.
   * ======================================================================== */
  public function plurify_words($words)
  {
    if (strToLower(substr($words, -1)) == 'y')
    {
      return substr_replace($words, 'ies', -1);
    }
    if (strToLower(substr($words, -1)) == 's')
    {
      return $words . 'es';
    }
    return $words . 's';
  }
}
