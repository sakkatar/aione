<?php

class Aione_Template {
	/**
	 * The class constructor
	 */
	public function __construct() {

		global $content_width;
		if ( ! isset( $content_width ) || empty( $content_width ) ) {
			$content_width = '669';
		}

		add_filter( 'body_class', array( $this, 'body_classes' ) );
	}

	/**
	 * Detect if we have a sidebar.
	 */
	public function has_sidebar() {

		// Get our extra body classes
		$classes = $this->body_classes( array() );

		if ( in_array( 'has-sidebar', $classes ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Detect if we have double sidebars.
	 */
	public function double_sidebars() {

		// Get our extra body classes
		$classes = $this->body_classes( array() );

		if ( in_array( 'double-sidebars', $classes ) ) {
			return true;
		} else {
			return false;
		}

	}

	/**
	 * Returns the sidebar-1 & sidebar-2 context.
	 *
	 * @var  int 1/2
	 * @return mixed
	 */
	public function sidebar_context( $sidebar = 1 ) {

		$c_pageID = Aione::c_pageID();


		$sidebar_1 = get_post_meta( $c_pageID, 'sbg_selected_sidebar_replacement', true );
		$sidebar_2 = get_post_meta( $c_pageID, 'sbg_selected_sidebar_2_replacement', true );

		if ( is_single() && ! is_singular( 'aione_portfolio' ) && ! is_singular( 'product' ) && ! is_bbpress()  && ! is_buddypress() && ! is_singular( 'tribe_events' ) && ! is_singular( 'tribe_organizer' ) && ! is_singular( 'tribe_venue' ) ) {

			if ( Aione()->theme_options[ 'posts_global_sidebar' ] ) {
				$sidebar_1 = ( 'None' != Aione()->theme_options[ 'posts_sidebar' ] ) ? array( Aione()->theme_options[ 'posts_sidebar' ] ) : '';
				$sidebar_2 = ( 'None' != Aione()->theme_options[ 'posts_sidebar_2' ] ) ? array( Aione()->theme_options[ 'posts_sidebar_2' ] ) : '';
			}

			if ( class_exists( 'Tribe__Events__Main' ) && tribe_is_event( $c_pageID ) && Aione()->theme_options[ 'pages_global_sidebar' ] ) {
				$sidebar_1 = ( 'None' != Aione()->theme_options[ 'pages_sidebar' ] ) ? array( Aione()->theme_options[ 'pages_sidebar' ] ) : '';
				$sidebar_2 = ( 'None' != Aione()->theme_options[ 'pages_sidebar_2' ] ) ? array( Aione()->theme_options[ 'pages_sidebar_2' ] ) : '';
			}

		} elseif ( is_singular( 'aione_portfolio' ) ) {

			if ( Aione()->theme_options[ 'portfolio_global_sidebar' ] ) {
				$sidebar_1 = ( 'None' != Aione()->theme_options[ 'portfolio_sidebar' ] ) ? array( Aione()->theme_options[ 'portfolio_sidebar' ] ) : '';
				$sidebar_2 = ( 'None' != Aione()->theme_options[ 'portfolio_sidebar_2' ] ) ? array( Aione()->theme_options[ 'portfolio_sidebar_2' ] ) : '';
			}

		} elseif ( is_singular( 'product' ) || ( class_exists( 'WooCommerce' ) && is_shop() ) ) {

			if ( Aione()->theme_options[ 'woo_global_sidebar' ] ) {
				$sidebar_1 = ( 'None' != Aione()->theme_options[ 'woo_sidebar' ] ) ? array( Aione()->theme_options[ 'woo_sidebar' ] ) : '';
				$sidebar_2 = ( 'None' != Aione()->theme_options[ 'woo_sidebar_2' ] ) ? array( Aione()->theme_options[ 'woo_sidebar_2' ] ) : '';
			}

		} elseif ( ( is_page() || is_page_template() ) && ( ! is_page_template( '100-width.php' ) && ! is_page_template( 'blank.php' ) ) ) {

			if ( Aione()->theme_options[ 'pages_global_sidebar' ] ) {

				$sidebar_1 = ( 'None' != Aione()->theme_options[ 'pages_sidebar' ] ) ? array( Aione()->theme_options[ 'pages_sidebar' ] ) : '';
				$sidebar_2 = ( 'None' != Aione()->theme_options[ 'pages_sidebar_2' ] ) ? array( Aione()->theme_options[ 'pages_sidebar_2' ] ) : '';

			}

		} else if( is_singular( 'tribe_events' ) ) {

			if ( Aione()->settings->get( 'ec_global_sidebar' ) ) {
				$sidebar_1 = ( 'None' != Aione()->settings->get( 'ec_sidebar' ) ) ? array( Aione()->settings->get( 'ec_sidebar' ) ) : '';
				$sidebar_2 = ( 'None' != Aione()->settings->get( 'ec_sidebar_2' ) ) ? array( Aione()->settings->get( 'ec_sidebar_2' ) ) : '';
			}

		} else if( is_singular( 'tribe_venue' ) || is_singular( 'tribe_organizer' ) ) {

			$sidebar_1 = ( 'None' != Aione()->settings->get( 'ec_sidebar' ) ) ? array( Aione()->settings->get( 'ec_sidebar' ) ) : '';
			$sidebar_2 = ( 'None' != Aione()->settings->get( 'ec_sidebar_2' ) ) ? array( Aione()->settings->get( 'ec_sidebar_2' ) ) : '';
		}

		if ( is_home() ) {
			$sidebar_1 = Aione()->theme_options[ 'blog_archive_sidebar' ];
			$sidebar_2 = Aione()->theme_options[ 'blog_archive_sidebar_2' ];
		}

		if ( is_archive() && ( ! is_buddypress() && ! is_bbpress() && ( class_exists( 'WooCommerce' ) && ! is_shop() ) || ! class_exists( 'WooCommerce' ) ) && !is_post_type_archive( 'aione_portfolio' ) && ! is_tax( 'portfolio_category' ) && ! is_tax( 'portfolio_skills' )  && ! is_tax( 'portfolio_tags' ) && ! is_tax( 'product_cat') && ! is_tax( 'product_tag' ) ) {
			$sidebar_1 = Aione()->theme_options[ 'blog_archive_sidebar' ];
			$sidebar_2 = Aione()->theme_options[ 'blog_archive_sidebar_2' ];
		}

		if ( is_post_type_archive( 'aione_portfolio' ) || is_tax( 'portfolio_category' ) || is_tax( 'portfolio_skills' )  || is_tax( 'portfolio_tags' ) ) {
			$sidebar_1 = Aione()->theme_options[ 'portfolio_archive_sidebar' ];
			$sidebar_2 = Aione()->theme_options[ 'portfolio_archive_sidebar_2' ];
		}

		if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
			$sidebar_1 = Aione()->theme_options[ 'woocommerce_archive_sidebar' ];
			$sidebar_2 = Aione()->theme_options[ 'woocommerce_archive_sidebar_2' ];
		}

		if ( is_search() ) {
			$sidebar_1 = Aione()->theme_options[ 'search_sidebar' ];
			$sidebar_2 = Aione()->theme_options[ 'search_sidebar_2' ];
		}


		if ( class_exists( 'Tribe__Events__Main' ) && is_events_archive() ) {
			$sidebar_1 = Aione()->settings->get( 'ec_sidebar' );
			$sidebar_2 = Aione()->settings->get( 'ec_sidebar_2' );
		}

		if ( 1 == $sidebar ) {
			return $sidebar_1;
		} elseif ( 2 == $sidebar ) {
			return $sidebar_2;
		}

	}

	/**
	 * Calculate any extra classes for the <body> element.
	 * These are then added using the 'body_class' filter.
	 * Documentation: ttps://codex.wordpress.org/Plugin_API/Filter_Reference/body_class
	 */
	public function body_classes( $classes ) {

		$sidebar_1 = $this->sidebar_context( 1 );
		$sidebar_2 = $this->sidebar_context( 2 );
		$c_pageID  = Aione::c_pageID();

		$classes[] = 'oxo-body';

		if ( is_page_template( 'blank.php' ) ) {
			$classes[] = 'body_blank';
		}

		if ( ! Aione()->theme_options[ 'header_sticky_tablet' ] ) {
			$classes[] = 'no-tablet-sticky-header';
		}
		if ( ! Aione()->theme_options[ 'header_sticky_mobile' ] ) {
			$classes[] = 'no-mobile-sticky-header';
		}
		if ( Aione()->theme_options[ 'mobile_slidingbar_widgets' ] ) {
			$classes[] = 'no-mobile-slidingbar';
		}
		if ( Aione()->theme_options[ 'status_totop' ] ) {
			$classes[] = 'no-totop';
		}
		if ( ! Aione()->theme_options[ 'status_totop_mobile' ] ) {
			$classes[] = 'no-mobile-totop';
		}
		if ( 'horizontal' == Aione()->theme_options[ 'woocommerce_product_tab_design' ] && is_singular( 'product' ) ) {
			$classes[] = 'woo-tabs-horizontal';
		}

		if ( 'modern' == Aione()->theme_options[ 'mobile_menu_design' ] ) {
			$classes[] = 'mobile-logo-pos-' . strtolower( Aione()->theme_options[ 'logo_alignment' ] );
		}

		if ( ( 'Boxed' == Aione()->theme_options['layout'] && 'default' == get_post_meta( $c_pageID, 'pyre_page_bg_layout', true ) ) || 'boxed' == get_post_meta( $c_pageID, 'pyre_page_bg_layout', true ) ) {
			$classes[] = 'layout-boxed-mode';
		} else {
			$classes[] = 'layout-wide-mode';
		}

		if ( is_array( $sidebar_1 ) && ! empty( $sidebar_1 ) && ( $sidebar_1[0] || '0' == $sidebar_1[0] ) && ! is_buddypress() && ! is_bbpress() && ! is_page_template( '100-width.php' ) && ! is_page_template( 'blank.php' ) && ( ! class_exists( 'WooCommerce' ) || ( class_exists( 'WooCommerce' ) && ! is_cart() && ! is_checkout() && ! is_account_page() && ! ( get_option( 'woocommerce_thanks_page_id' ) && is_page( get_option( 'woocommerce_thanks_page_id' ) ) ) ) ) ) {
			$classes[] = 'has-sidebar';
		}

		if ( is_array( $sidebar_1 ) && $sidebar_1[0] && is_array( $sidebar_2 ) && $sidebar_2[0] && ! is_buddypress() && ! is_bbpress() && ! is_page_template( '100-width.php' )  && ! is_page_template( 'blank.php' ) && ( ! class_exists( 'WooCommerce' ) || ( class_exists( 'WooCommerce' ) && ! is_cart() && ! is_checkout() && ! is_account_page() && ! ( get_option( 'woocommerce_thanks_page_id' ) && is_page( get_option( 'woocommerce_thanks_page_id' ) ) ) ) ) ) {
			$classes[] = 'double-sidebars';
		}

		if ( is_page_template( 'side-navigation.php' ) && is_array( $sidebar_2 ) && $sidebar_2[0] ) {
			$classes[] = 'double-sidebars';
		}

		if ( is_home() ) {
			if ( 'None' != $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' != $sidebar_1 && 'None' != $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_archive() && ( ! is_buddypress() && ! is_bbpress() && ( class_exists( 'WooCommerce' ) && ! is_shop() ) || ! class_exists( 'WooCommerce' ) ) && ! is_tax( 'portfolio_category' ) && ! is_tax( 'portfolio_skills' )  && ! is_tax( 'portfolio_tags' ) && ! is_tax( 'product_cat') && ! is_tax( 'product_tag' ) ) {
			if ( 'None' != $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' != $sidebar_1 && 'None' != $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_tax( 'portfolio_category' ) || is_tax( 'portfolio_skills' )  || is_tax( 'portfolio_tags' ) ) {
			if ( 'None' != $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' != $sidebar_1 && 'None' != $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_tax( 'product_cat' ) || is_tax( 'product_tag' ) ) {
			if ( 'None' != $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' != $sidebar_1 && 'None' != $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( is_search() ) {
			if ( 'None' != $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' != $sidebar_1 && 'None' != $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		

		if ( class_exists( 'Tribe__Events__Main' ) && is_events_archive() ) {
			if ( 'None' != $sidebar_1 ) {
				$classes[] = 'has-sidebar';
			}
			if ( 'None' != $sidebar_1 && 'None' != $sidebar_2 ) {
				$classes[] = 'double-sidebars';
			}
		}

		if ( 'no' != get_post_meta( $c_pageID, 'pyre_display_header', true) ) {
			if ( 'left' == Aione()->theme_options[ 'header_position' ] || 'right' == Aione()->theme_options[ 'header_position' ] ) {
				$classes[] = 'side-header';
			}
			if ( 'left' == Aione()->theme_options[ 'header_position' ] ) {
				$classes[] = 'side-header-left';
			} elseif ( 'right' == Aione()->theme_options[ 'header_position' ] ) {
				$classes[] = 'side-header-right';
			}
			$classes[] = 'menu-text-align-' . strtolower( Aione()->theme_options[ 'menu_text_align' ] );
		}

		if( class_exists( 'WooCommerce' ) ) {
			$classes[] = 'oxo-woo-product-design-' . Aione()->theme_options[ 'woocommerce_product_box_design' ];
		}

		$classes[] = 'mobile-menu-design-' . Aione()->theme_options[ 'mobile_menu_design' ];

		$classes[] = 'oxo-image-hovers';

		if( Aione()->theme_options[ 'pagination_text_display'] ) {
			$classes[] = 'oxo-show-pagination-text';
		} else {
			$classes[] = 'oxo-hide-pagination-text';
		}

		return $classes;
	}

	public function comment_template( $comment, $args, $depth ) { ?>
		<?php $add_below = ''; ?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID() ?>">
			<div class="the-comment">
				<div class="avatar"><?php echo get_avatar( $comment, 54 ); ?></div>
				<div class="comment-box">
					<div class="comment-author meta">
						<strong><?php echo get_comment_author_link(); ?></strong>
						<?php printf( __( '%1$s at %2$s', 'Aione' ), get_comment_date(),  get_comment_time() ); ?><?php edit_comment_link( __( ' - Edit', 'Aione' ),'  ','' ); ?><?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( ' - Reply', 'Aione' ), 'add_below' => 'comment', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
					</div>
					<div class="comment-text">
						<?php if ( $comment->comment_approved == '0' ) : ?>
							<em><?php _e( 'Your comment is awaiting moderation.', 'Aione' ); ?></em>
							<br />
						<?php endif; ?>
						<?php comment_text() ?>
					</div>
				</div>
			</div>
		<?php
	}
	
	public function title_template( $content = '', $size = '2', $content_align = 'left' ) {
		$margin_top	= Aione()->settings->get( 'title_top_margin' );
		$margin_bottom	= Aione()->settings->get( 'title_bottom_margin' );
		$sep_color = Aione()->settings->get( 'title_border_color' );
		$style_type	= Aione()->settings->get( 'title_style_type' );
		$size_array = array( '1' => 'one', '2' => 'two', '3' => 'three', '4' => 'four', '5' => 'five', '6' => 'six' );
		$classes = '';
		$styles = '';
		$sep_styles = '';
		
		$classes_array = explode( ' ', $style_type );
		foreach ( $classes_array as $class ) {
			$classes .= ' sep-' . $class;
		}		
		
		if ( $margin_top ) {
			$styles .= sprintf( 'margin-top:%s;', Aione_Sanitize::get_value_with_unit( $margin_top ) );
		}
		if ( $margin_bottom ) {
			$styles .= sprintf( 'margin-bottom:%s;', Aione_Sanitize::get_value_with_unit( $margin_bottom ) );
		}		
		
		if ( strpos( $style_type, 'underline' ) !== FALSE || 
			 strpos( $style_type, 'none' ) !== FALSE
		) {
		
			if ( strpos( $style_type, 'underline' ) !== false ) {
				if ( $sep_color ) {
					$styles .= 'border-bottom-color:' . $sep_color;
				}
			} elseif ( strpos( $style_type, 'none' ) !== false ) {
				$classes .= ' oxo-sep-none';
			}		
		
			$html = sprintf( '<div class="oxo-title oxo-title-size-%s%s" style="%s"><h%s class="title-heading-%s">%s</h%s></div>', $size_array[$size], 
							 $classes, $styles, $size, $content_align, $content, $size );
		} else {
			if ( $content_align == 'right' ) {
				$html = sprintf( '<div class="oxo-title oxo-title-size-%s%s" style="%s"><div class="title-sep-container"><div class="title-sep%s"></div></div><h%s class="title-heading-%s">%s</h%s></div>', 
								 $size_array[$size], $classes, $styles, $classes, $size, $content_align, $content, $size );								
			} elseif ( $content_align == 'center' ) {
				$html = sprintf( '<div class="oxo-title oxo-title-center oxo-title-size-%s%s" style="%s"><div class="title-sep-container title-sep-container-left"><div class="title-sep%s"></div></div><h%s class="title-heading-%s">%s</h%s><div class="title-sep-container title-sep-container-right"><div class="title-sep%s"></div></div></div>', 
								 $size_array[$size], $classes, $styles, $classes, $size, $content_align, $content, $size, $classes );	
			} else {	 
				$html = sprintf( '<div class="oxo-title oxo-title-size-%s%s" style="%s"><h%s class="title-heading-%s">%s</h%s><div class="title-sep-container"><div class="title-sep%s"></div></div></div>', 
								 $size_array[$size], $classes, $styles, $size, $content_align, $content, $size, $classes );
			}
		}
		return $html;
	}	
}

// Omit closing PHP tag to avoid "Headers already sent" issues.