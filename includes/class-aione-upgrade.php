<?php
class Aione_Upgrade {

	private $database_theme_version;
	private $previous_theme_version;
	private $current_theme_version;
	private $aione_options;
	private $current_user;

	public function __construct() {
		$this->database_theme_version = get_option( 'aione_version', false );
		$this->previous_theme_version = get_option( 'aione_previous_version', false );
		$this->current_theme_version  = Aione::$version;
		$this->aione_options = get_option( 'Aione_options', array() );

		add_action( 'init', array( $this, 'set_user_data' ) );
		add_action( 'after_setup_theme', array( $this, 'migrate' ) );

		if ( empty( $this->aione_options ) ) {
			// This is a fresh installation
			add_action( 'init', array( $this, 'fresh_installation' ) );

		} else {
			// This is an update installation
			add_action( 'init', array( $this, 'update_installation' ) );
		}
	}

	/**
	 * Actions to run on a fresh installation
	 */
	public function fresh_installation() {
		$this->update_version();
	}

	/**
	 * Migrate script to decode theme options
	 */
	public function migrate() {
		if ( get_option( 'aione_38_migrate' ) != 'done' ) {
			$theme_version = get_option( 'aione_theme_version' );

			if ( $theme_version == '1.0.0' ) { // child theme check failure
				Aione()->init->set_theme_version();
			}

			if ( version_compare( $theme_version, '3.8', '>=' ) && version_compare( $theme_version, '3.8.5', '<' ) ) {
				$smof_data_to_decode = get_option( 'Aione_options' );

				$encoded_field_names = array( 'google_analytics', 'space_head', 'space_body', 'content_above_footer', 'custom_css' );

				foreach ( $encoded_field_names as $field_name ) {
					$decoded_field_value = rawurldecode( $smof_data_to_decode[ $field_name ] );

					if ( $decoded_field_value ) {
						$smof_data_to_decode[ $field_name ] = $decoded_field_value;
					}
				}

				update_option( 'Aione_options', $smof_data_to_decode );
				update_option( 'aione_38_migrate', 'done' );
			}
		}
	}

	/**
	 * Actions to run on an update installation
	 */
	public function update_installation() {
		if ( version_compare( $this->current_theme_version, $this->database_theme_version, '>' ) ) {
			// Delete the update notice dismiss flag, so that the flag is reset
			delete_user_meta( $this->current_user->ID, 'aione_pre_385_notice' );
			delete_user_meta( $this->current_user->ID, 'aione_update_notice' );
			
			// Delete the TGMPA update notice dismiss flag, so that the flag is reset
			delete_user_meta( $this->current_user->ID, 'tgmpa_dismissed_notice_tgmpa' );

			$this->update_version();

			// The previous version was less than 3.8.5
			if ( version_compare( $this->database_theme_version, '3.8.5', '<' ) ) {
				$this->pre_385();
			}

			// The previous version was less than 3.8.7
			if ( version_compare( $this->database_theme_version, '3.8.7', '<' ) ) {
				$this->pre_387();
			}

			// The previous version was less than 3.9
			if ( version_compare( $this->database_theme_version, '3.9', '<' ) ) {
				$this->pre_390();
			}
			
			// The previous version was less than 3.9.2
			if ( version_compare( $this->database_theme_version, '3.9.2', '<' ) ) {
				$this->pre_392();
			}			
		}

		// Hook the dismiss notice functionality
		add_action( 'admin_init', array( $this, 'notices_action' ) );

		// Show upgrade notices
		if ( version_compare( $this->current_theme_version, '3.9.2', '<' ) ) {
			add_action( 'admin_notices', array( $this, 'upgrade_notice' ) );
		}
	}

	/**
	 * Update the aione version in the database and reset flags.
	 */
	public function update_version() {
		if ( version_compare( $this->current_theme_version, $this->database_theme_version, '>' ) ) {
			// Update the stored theme versions
			update_option( 'aione_version', $this->current_theme_version );
			update_option( 'aione_previous_version', $this->database_theme_version );

			// Make sure previous version is set, before the update notice action is called
			$this->previous_theme_version = $this->database_theme_version;
		}
	}

	/**
	 * Set the WP user data, done on init hook
	 */
	public function set_user_data() {
		global $current_user;

		$this->current_user = $current_user;

		$this->debug();
	}

	/**
	 * Run if previous version is < 385
	 */
	public function pre_385() {
		$options = $this->aione_options;

		// We no longer have a less compiler.
		// Migrate the less_compiler option to the new dynamic_css_compiler option.
		if ( isset( $options['less_compiler'] ) ) {
			$options['dynamic_css_compiler'] = $options['less_compiler'];
		}

		// We added an independent theme option for content box icons
		if ( isset( $options['icon_color'] ) ) {
			$options['content_box_icon_color'] = $options['icon_color'];
		}

		if ( isset( $options['icon_circle_color'] ) ) {
			$options['content_box_icon_bg_color'] = $options['icon_circle_color'];
		}

		if ( isset( $options['icon_border_color'] ) ) {
			$options['content_box_icon_bg_inner_border_color'] = $options['icon_border_color'];
		}

		$options['post_titles_font_size'] = $options['h2_font_size'];
		$options['post_titles_extras_font_size'] = $options['h2_font_size'];
		$options['post_titles_font_lh'] = $options['h2_font_lh'];

		// Update the options with our modifications.
		update_option( 'Aione_options', $options );

		// Reset the css
		update_option( 'aione_dynamic_css_posts', array() );
	}

	/**
	 * Run if previous version is < 387
	 */
	public function pre_387() {
		$options = $this->aione_options;

		// If top header is used, set the side header mobile break point to 800px to avoid update issues
		if ( isset( $options['header_position'] ) && $options['header_position'] == 'Top' ) {
			$options['side_header_break_point'] = '800px';
		}

		// Update the new form input height with the old search form height if it does not have the default value
		if ( isset( $options['search_form_height'] ) && $options['search_form_height'] != '33px' ) {
			$options['form_input_height'] = $options['search_form_height'];
		}

		// Update the options with our modifications.
		update_option( 'Aione_options', $options );

		// Reset the css
		update_option( 'aione_dynamic_css_posts', array() );
	}

	/**
	 * Run if previous version is < 390
	 */
	public function pre_390() {
		$options = $this->aione_options;

		// Increase the height of top menu dropdown for woo cart change #2006
		if ( isset( $options['topmenu_dropwdown_width'] ) && intval( $options['topmenu_dropwdown_width'] ) <= 180 ) {
			$options['topmenu_dropwdown_width'] = '180px';
		}

		// Increase the height of top menu dropdown for woo cart change #2006
		if ( isset( $options['dropdown_menu_width'] ) && intval( $options['dropdown_menu_width'] ) <= 180 ) {
			$options['dropdown_menu_width'] = '180px';
		}

		// Update the options with our modifications.
		update_option( 'Aione_options', $options );

		// Reset the css
		update_option( 'aione_dynamic_css_posts', array() );
	}

	/**
	 * Run if previous version is < 392
	 */
	public function pre_392() {
		$options = $this->aione_options;

		// Increase the height of top menu dropdown for woo cart change #2006
		if ( ! isset( $options['contact_comment_position'] )  ) {
			$options['contact_comment_position'] = 'below';
		}

		// Update the options with our modifications.
		update_option( 'Aione_options', $options );

		// Reset the css
		update_option( 'aione_dynamic_css_posts', array() );
	}

	/**
	 * Notices that will show to users that upgrade
	 */
	public function upgrade_notice() {
		/* Check that the user hasn't already clicked to ignore the message */
		if ( $this->previous_theme_version && current_user_can( 'edit_theme_options' ) && ! get_user_meta( $this->current_user->ID, 'aione_update_notice', TRUE ) ) {
			echo '<div class="updated error oxo-upgrade-notices">';
				if ( version_compare( $this->previous_theme_version, '3.8.5', '<' ) ) {
					?>
					<p><strong>The following important changes were made to Aione 3.8.5:</strong></p>
					<ol>
					<li><strong>CHANGED:</strong> Sidebar, Footer and Sliding Bar widget title HTML tag is changed from h3 to h4 for SEO improvements.</li>
					<li><strong>DEPRECATED:</strong> Icon Flip shortcode option was deprecated from flip boxes, content boxes and fontawesome shortcode. Alternatively, you can use the icon rotate option.</li>
					</ol>
					<?php
				}
				if ( version_compare( $this->previous_theme_version, '3.8.6', '<' ) ) {
					?>
					<p><strong>The following important changes were made to Aione 3.8.6:</strong></p>
					<ol>
					<li><strong>DEPRECATED:</strong> Fixed Mode for iPad will be deprecated in Aione 3.8.7. Fixed Mode will be moved into a plugin.</li>
					<li><strong>CHANGED:</strong> Titles for "Related Posts" and "Comments" on single post page are changed from H2 to H3 for SEO improvements.</li>
					</ol>
					<?php
				}
				if ( version_compare( $this->previous_theme_version, '3.8.7', '<' ) ) {
					?>
					<p><strong>The following important changes were made to Aione 3.8.7:</strong></p>
					<ol>
					<li><strong>REMOVED:</strong> Fixed Mode for iPad is removed as a theme option. Fixed Mode is moved into a free plugin. <a href="https://oxosolutions.com/aione-doc/fixed-mode-for-ipad-portrait/" target="_blank">Download</a>.</li>
					<li><strong>CHANGED:</strong> The left/right padding for the 100% Width Page Template &amp; 100% Full Width Container Now Applies To Mobile.</li>
					<li><strong>CHANGED:</strong> <strong><em>Theme Options -> Header Content Options -> Side Header Responsive Breakpoint</em></strong> was replaced by <strong>Mobile Header Responsive Breakpoint</strong>. It can now be used to control the side header breakpoint as well as the mobile header break point for top headers.</li>
					<li><strong>CHANGED:</strong> <strong><em>Theme Options -> Menu Options -> Menu Text Align</em></strong> will be followed by header 5. If your menu is no longer in center, please use that option to change the position of the menu.</li>
					<li><strong>CHANGED:</strong> <strong><em>Theme Options -> Search Page -> Search Field Height</em></strong> was removed and combined with the new <strong>Form Input and Select Height</strong> option in the Extra tab. All form inputs and selects can be controlled with the new option.</li>
					</ol>
					<?php
				}

				if ( version_compare( $this->previous_theme_version, '3.9.0', '<' ) ) {
					?>
					<p><strong>The following important changes were made to Aione 3.9:</strong></p>
					<ol>
					<li><strong>CHANGED:</strong> The woo cart / my account dropdown width is now controlled by the dropdown width theme option for main and top menu.</li>
					<li><strong>CHANGED:</strong> The footer center option now allows each column to be fully centered.</li>
					</ol>
					<?php
				}
	    		printf( '<p><strong>' . __( '<a href="%1$s" class="%2$s" target="_blank">View Changelog</a>', 'Aione' ), 'http://oxosolutions.com/products/wordpress-themes/aione/', 'view-changelog button-primary' );
	        	printf( __( '<a href="%1$s" class="%2$s" style="margin:0 4px;">Dismiss this notice</a>', 'Aione' ) . '</strong></p>', esc_url( add_query_arg( 'aione_update_notice', '1' ) ), 'dismiss-notice button-secondary' );
	    	echo '</div>';
		}
	}

	/**
	 * Action to take when user clicks on notices button
	 */
	public function notices_action() {
		// Set update notice dismissal, so that the notice is no longer shown
		if ( isset( $_GET['aione_update_notice'] ) && $_GET['aione_update_notice'] ) {
			add_user_meta( $this->current_user->ID, 'aione_update_notice', '1', true );
		}
	}

	private function debug( $debug_mode = FALSE ) {
		if ( $debug_mode ) {
			global $current_user;

			delete_user_meta( $current_user->ID, 'aione_update_notice' );
			delete_option( 'aione_version' );
			update_option( 'aione_version', '3.9' );
			delete_option( 'aione_previous_version' );
			delete_option( 'Aione_options' );
			var_dump("Current Version: " . Aione::$version);
			var_dump("DB Version: " . get_option( 'aione_version', false ));
			var_dump("Previous Version: " . get_option( 'aione_previous_version', false ));
			var_dump("Update Notice: " . get_user_meta( $current_user->ID, 'aione_update_notice', TRUE ));
		}

		return;
	}
}

// Omit closing PHP tag to avoid "Headers already sent" issues.
