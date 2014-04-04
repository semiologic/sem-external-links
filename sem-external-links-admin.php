<?php
/**
 * external_links_admin
 *
 * @package External Links
 **/

class external_links_admin {
	/**
	 * Plugin instance.
	 *
	 * @see get_instance()
	 * @type object
	 */
	protected static $instance = NULL;

	/**
	 * URL to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_url = '';

	/**
	 * Path to this plugin's directory.
	 *
	 * @type string
	 */
	public $plugin_path = '';

	/**
	 * Access this plugin’s working instance
	 *
	 * @wp-hook plugins_loaded
	 * @return  object of this class
	 */
	public static function get_instance()
	{
		NULL === self::$instance and self::$instance = new self;

		return self::$instance;
	}


	/**
	 * Constructor.
	 *
	 *
	 */

	public function __construct() {
		$this->plugin_url    = plugins_url( '/', __FILE__ );
		$this->plugin_path   = plugin_dir_path( __FILE__ );

		$this->init();
    }


	/**
	 * init()
	 *
	 * @return void
	 **/

	function init() {
		// register actions and filters
		add_action('settings_page_external-links', array($this, 'save_options'), 0);
	}

    /**
	 * save_options()
	 *
	 * @return void
	 **/
	
	function save_options() {
		if ( !$_POST || !current_user_can('manage_options') )
			return;
		
		check_admin_referer('external_links');
		
		foreach ( array('global', 'icon', 'target', 'nofollow', 'text_widgets') as $var )
			$$var = isset($_POST[$var]);
		
		update_option('external_links', compact('global', 'icon', 'target', 'nofollow', 'text_widgets'));
		
		echo "<div class=\"updated fade\">\n"
			. "<p>"
				. "<strong>"
				. __('Settings saved.', 'external-links')
				. "</strong>"
			. "</p>\n"
			. "</div>\n";
	} # save_options()
	
	
	/**
	 * edit_options()
	 *
	 * @return void
	 **/
	
	static function edit_options() {
		echo '<div class="wrap">' . "\n"
			. '<form method="post" action="">';

		wp_nonce_field('external_links');
		
		$options = external_links::get_options();
		
		if ( $options['nofollow'] && ( function_exists('strip_nofollow') || class_exists('sem_dofollow') ) ) {
			echo "<div class=\"error\">\n"
				. "<p>"
					. __('Note: Your rel=nofollow preferences is being ignored because the dofollow plugin is enabled on your site.', 'external-links')
				. "</p>\n"
				. "</div>\n";
		}
		
		echo '<h2>' . __('External Links Settings', 'external-links') . '</h2>' . "\n";
		
		echo '<table class="form-table">' . "\n";

		echo '<tr>' . "\n"
			. '<th scope="row">'
			. __('Apply Globally', 'external-links')
			. '</th>' . "\n"
			. '<td>'
			. '<label>'
			. '<input type="checkbox" name="global"'
				. checked($options['global'], true, false)
				. ' />'
			. '&nbsp;'
			. __('Apply these settings to all outbound links on the site except those in scripts, styles and the html head section.', 'external-links')
			. '</label>'
			. '</td>' . "\n"
			. '</tr>' . "\n";

		echo '<tr>' . "\n"
			. '<th scope="row">'
			. __('Apply to Text Widgets', 'external-links')
			. '</th>' . "\n"
			. '<td>'
			. '<label>'
			. '<input type="checkbox" name="text_widgets"'
				. checked($options['text_widgets'], true, false)
				. ' />'
			. '&nbsp;'
			. __('Apply these settings to all text widgets in addition to those in posts and comments.', 'external-links')
			. '</label>'
			. '</td>' . "\n"
			. '</tr>' . "\n";

		echo '<tr>' . "\n"
			. '<th scope="row">'
			. __('Add Icons', 'external-links')
			. '</th>' . "\n"
			. '<td>'
			. '<label>'
			. '<input type="checkbox" name="icon"'
				. checked($options['icon'], true, false)
				. ' />'
			. '&nbsp;'
			. __('Mark outbound links with an icon.', 'external-links')
			. '</label>'
			. '<br />' . "\n"
			. __('Note: You can override this behavior by adding a class="no_icon" to individual links.', 'external-links')
			. '</td>' . "\n"
			. '</tr>' . "\n";
		
		echo '<tr>' . "\n"
			. '<th scope="row">'
			. __('Add No Follow', 'external-links')
			. '</th>' . "\n"
			. '<td>'
			. '<label>'
			. '<input type="checkbox" name="nofollow"'
				. checked($options['nofollow'], true, false)
				. ' />'
			. '&nbsp;'
			. __('Add a rel=nofollow attribute to outbound links.', 'external-links')
			. '</label>'
			. '<br />' . "\n"
			. __('Note: You can override this behavior by adding the attribute rel="follow" to individual links.', 'external-links')
			. '</td>' . "\n"
			. '</tr>' . "\n";
		
		echo '<tr>' . "\n"
			. '<th scope="row">'
			. __('Open in New Windows', 'external-links')
			. '</th>' . "\n"
			. '<td>'
			. '<label>'
			. '<input type="checkbox" name="target"'
				. checked($options['target'], true, false)
				. ' />'
			. '&nbsp;'
			. __('Open outbound links in new windows.', 'external-links')
			. '</label>'
			. '<br />' . "\n"
			. __('Note: Some usability experts discourage this, claiming that <a href="http://www.useit.com/alertbox/9605.html">this can damage your visitors\' trust</a> towards your site. Others highlight that computer-illiterate users do not always know how to use the back button, and encourage the practice for that reason.', 'external-links')
			. '</td>' . "\n"
			. '</tr>' . "\n";
		
		echo '</table>' . "\n";
		
		echo '<p class="submit">'
			. '<input type="submit"'
				. ' value="' . esc_attr(__('Save Changes', 'external-links')) . '"'
				. ' />'
			. '</p>' . "\n";
		
		echo '</form>' . "\n"
			. '</div>' . "\n";
	} # edit_options()
} # external_links_admin

$external_links_admin = external_links_admin::get_instance();
