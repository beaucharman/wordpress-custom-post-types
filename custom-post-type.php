<?php
/**
 * Bamboo - WordPress Custom Post Type
 * ========================================================================
 * wordpress-custom-post-type.php
 * @version   3.0 | November 10th 2013
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
 *
 * To declare a custom post type, simply create a new instance of the
 * Bamboo_Custom_Post_Type class.
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



class Bamboo_Custom_Post_Type
{

	public $name;
	public $labels;
	public $options;
	public $icon;
	public $help;



	/**
	 * Class Constructor
	 *  ========================================================================
	 * @param  {array}   $args
	 * @return {instance} post type
	 */
	public function __construct($args)
	{
		/**
		 * Set class values
		 */
		if (! is_array($args))
		{
			$name = $args;
			$args = array();
		}
		else
		{
			$name = $args['name'];
		}

		$args = array_merge(
			array(
				'name'      => $this->uglify_words($name),
				'labels'    => array(),
				'options'   => array(),
				'menu_icon' => null,
				'help'      => null
			),
			$args
		);

		$this->name = $args['name'];
		$this->labels = $args['labels'];
		$this->options = $args['options'];
		$this->icon = $args['menu_icon'];
		$this->help = $args['help'];

		/**
		 * Create the labels where needed
		 */

		/* Post type singluar label */
		if (! isset($this->labels['singular']))
		{
			$this->labels['singular'] = $this->prettify_words($this->name);
		}

		/* Post type plural label */
		if (! isset($this->labels['plural']))
		{
			$this->labels['plural'] = $this->plurify_words($this->labels['singular']);
		}

		/* Post type menu label */
		if (! isset($this->labels['menu']))
		{
			$this->labels['menu'] = $this->labels['plural'];
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

			if ($this->icon)
			{
				add_action('admin_head', array(&$this, 'icon_style'));
			}
		}
	}



	/**
	 * Register Custom Post Type
	 * ========================================================================
	 * @param  {null}
	 * @return post type
	 */
	public function register_custom_post_type()
	{
		/**
		 * Set up the post type labels
		 */
		$labels = array(
			'name'               => __($this->labels['plural']),
			'singular_name'      => __($this->labels['singular']),
			'menu_name'          => __($this->labels['menu']),
			'add_new_item'       => __('Add New ' . $this->labels['singular']),
			'edit_item'          => __('Edit ' . $this->labels['singular']),
			'new_item'           => __('New ' . $this->labels['singular']),
			'all_items'          => __('All ' . $this->labels['plural']),
			'view_item'          => __('View ' . $this->labels['singular']),
			'search_items'       => __('Search ' . $this->labels['plural']),
			'not_found'          => __('No ' . $this->labels['plural'] . ' found'),
			'not_found_in_trash' => __('No ' . $this->labels['plural'] . ' found in Trash')
		);

		/**
		 * Configure the post type options
		 */
		$options = array_merge(
			array(
				'has_archive'   => true,
				'labels'        => $labels,
				'menu_icon'     => null,
				'menu_position' => 4,
				'public'        => true,
				'rewrite'       => array('slug' => $this->get_slug()),
				'supports'      => array('title', 'editor', 'thumbnail', 'revisions')
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
	 * @param  $contextual_help
	 * @param  $screen_id
	 * @param  $screen
	 * @return $contextual_help
	 */
	public function add_custom_contextual_help($contextual_help, $screen_id, $screen)
	{
		foreach ($this->help as $help)
		{
			if (! isset($help['context']))
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
	 * @param  {array}   $user_args
	 * @param  {boolean} $single
	 * @return {array}   post type data
	 *
	 * Get all entries assigned to this post type.
	 */
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

		$items = get_posts($args);

		if ($single)
		{
		return $items[0];
		}

		return $items;
	}



	/**
	 * Get Slug
	 * ========================================================================
	 * @param  {string} $name
	 * @return {string}
	 */
	public function get_slug($name = null)
	{
		if (! $name)
		{
			$name = $this->name;
		}

		return strtolower(str_replace(' ', '-', str_replace('_', '-', $name)));
	}



	/**
	 * Prettify Words
	 * ========================================================================
	 * @param  {string} $words
	 * @return {string}
	 *
	 * Creates a pretty version of a string, like a pug version of a dog.
	 */
	public function prettify_words($words)
	{
		return ucwords(str_replace('_', ' ', $words));
	}



	/**
	 * Uglify Words
	 * ========================================================================
	 * @param  {string} $words
	 * @return {string}
	 *
	 * Creates a url firendly version of the given string.
	 */
	public function uglify_words($words)
	{
		return strToLower(str_replace(' ', '_', $words));
	}



	/**
	 * Plurify Words
	 * ========================================================================
	 * @param  {string} $words
	 * @return {string}
	 *
	 * Plurifies most common words. Not currently working proper nouns,
	 * or more complex words, for example knife => knives, leaf => leaves.
	 */
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



	/**
   	* Icon Style
   	* ========================================================================
   	* @param  {null}
   	* @return {output} html
   	*/
  	public function icon_style() { ?>
    		<style rel="stylesheet" media="screen">
    			#adminmenu .menu-icon-<?php echo strtolower(str_replace(' ', '', $this->name) ) ; ?> div.wp-menu-image:before {
      				content: '\<?php echo $this->icon; ?>';
    			}
    		</style>
  	<?php }



	/**
	 * Get Font Awesome
	 * http://fortawesome.github.io/Font-Awesome/
	 * ========================================================================
	 * @param  {null}
	 * @return {output} html
	 */
	static function get_font_awesome()
	{
		add_action('admin_head', 'font_awesome_icons');

		function font_awesome_icons()
		{
			echo '<link href="//cdnjs.cloudflare.com/ajax/libs/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">';
		}
	}
}
