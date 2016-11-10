<?php
namespace ProteusThemes\StickyMenu;

/**
 * Sticky menu for all newer PT themes.
 */

/**
 * Sticky menu class. *Singleton*
 */
class StickyMenu {

	/**
	 * The reference to *Singleton* instance of this class
	 *
	 * @var object
	 */
	private static $instance;

	/**
	 * Default Customizer settings.
	 *
	 * @var array
	 */
	private $default_settings;

	/**
	 * Menu HTML ID attr
	 */
	const MENU_ID = 'pt-sticky-main-menu';

	/**
	 * PT_Sticky_Menu construct function.
	 */
	protected function __construct() {

		// Get default customizer settings.
		$this->default_settings = apply_filters( 'pt-sticky-menu/settings_default', array(
			'sticky_selected' => false,
			'fp_select'       => 'none',
			'fp_custom_text'  => 'Featured Page',
			'fp_cutsom_url'   => '#',
			'fp_new_window'   => false,
			'fp_icon'         => 'fa-phone',
			'fp_bg_color'     => '#ffffff',
			'logo_selected'   => false,
			'logo_img'        => '',
			'logo2x_img'      => '',
		) );

		// Register customizer.
		add_action( 'customize_register', array( $this, 'register_customizer' ) );

		// Display sticky menu HTML output in the footer if sticky menu is enabled in customizer.
		add_action( 'wp_footer', array( $this, 'sticky_menu_output' ) );

		// Add tabindex attr to <a>
		add_filter( 'nav_menu_link_attributes', array( $this, 'nav_menu_link_attributes' ), 10, 3 );

		// Add the body class, when sticky menu is enabled.
		add_filter( 'body_class', array( $this, 'body_class' ) );
	}


	/**
	 * Returns the *Singleton* instance of this class.
	 *
	 * @return Singleton The *Singleton* instance.
	 */
	public static function get_instance() {
		if ( null === static::$instance ) {
			static::$instance = new static();
		}

		return static::$instance;
	}


	/**
	 * Register customizer controls, settings and other things.
	 *
	 * Customizer class will be auto-loaded with PHP composer.
	 *
	 * @param WP_Customize_Manager $wp_customize The customizer manager.
	 */
	public function register_customizer( $wp_customize ) {
		new Customizer( $wp_customize );
	}


	/**
	 * Sticky menu HTML output.
	 */
	public function sticky_menu_output() {

		// Display sticky menu if sticky menu is enabled in customizer.
		// The condition has to be here, otherwise the customizer refresh is not working.
		if ( get_theme_mod( 'sticky_menu_select', $this->default_settings['sticky_selected'] ) ) :
	?>

		<div class="pt-sticky-menu__container  js-pt-sticky-menu" style="background-color: <?php echo esc_attr( get_theme_mod( 'sticky_menu_bg_color', $this->default_settings['fp_bg_color'] ) ); ?>;">
			<div class="container">
				<div class="pt-sticky-menu">
					<!-- Logo and site name -->
					<div class="pt-sticky-menu__logo">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" tabindex="-1">
							<?php
							$logo = $logo2x = '';
							$logo_dimensions_mod = 'logo_dimensions_array';

							// Use sticky menu specific logos if selected, otherwise use the default theme logos.
							$sticky_logo_selected = get_theme_mod( 'sticky_logo_selected', $this->default_settings['logo_selected'] );
							if ( ! empty( $sticky_logo_selected ) ) {
								$logo   = get_theme_mod( 'sticky_logo_img', $this->default_settings['logo_img'] );
								$logo2x = get_theme_mod( 'sticky_logo2x_img', $this->default_settings['logo2x_img'] );
								$logo_dimensions_mod = 'sticky_logo_dimensions_array';
							}
							else {
								// Get logo theme_mod names for the logo.
								$logo_settings = apply_filters( 'pt-sticky-menu/logo_mod_names', array(
									'logo'        => 'logo_img',
									'retina_logo' => 'logo2x_img',
								) );
								$logo   = get_theme_mod( $logo_settings['logo'], false );
								$logo2x = get_theme_mod( $logo_settings['retina_logo'], false );
							}

							if ( ! empty( $logo ) ) :
							?>
								<img src="<?php echo esc_url( $logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" srcset="<?php echo esc_attr( $logo ); ?><?php echo empty( $logo2x ) ? '' : ', ' . esc_url( $logo2x ) . ' 2x'; ?>" class="img-fluid" <?php echo $this->get_logo_dimensions( $logo_dimensions_mod ); ?> />
							<?php
							else :
							?>
								<h1><?php bloginfo( 'name' ); ?></h1>
							<?php
							endif;
							?>
						</a>
					</div>
					<!-- Main Navigation -->
					<nav class="pt-sticky-menu__navigation" aria-label="<?php esc_html_e( 'Main Menu', 'pt-sticky-menu' ); ?>">
							<?php

							// Get menu location.
							$menu_location = apply_filters( 'pt-sticky-menu/theme_menu_location', 'main-menu' );

							if ( has_nav_menu( $menu_location ) ) {
								wp_nav_menu( array(
									'theme_location' => $menu_location,
									'container'      => false,
									'menu_class'     => 'main-navigation  js-dropdown',
									'items_wrap'     => '<ul id="%1$s" class="%2$s" role="menubar">%3$s</ul>',
									'menu_id'        => self::MENU_ID, // this differentiates footer menu from main menu in header
								) );
							}
							?>
						<?php

						// Display the Call-to-Action button if the page is selected in customizer.
						$selected_page = get_theme_mod( 'sticky_menu_featured_page_select', $this->default_settings['fp_select'] );

						if ( 'none' !== $selected_page ) :
							$cta = array();
							$cta['target'] = get_theme_mod( 'sticky_menu_featured_page_open_in_new_window', $this->default_settings['fp_new_window'] ) ? '_blank' : '_self';
							$cta['icon']   = get_theme_mod( 'sticky_menu_featured_page_icon', $this->default_settings['fp_icon'] );
							$cta['text']   = '';
							$cta['url']    = '';

							if ( 'custom-url' === $selected_page ) {
								$cta['text'] = get_theme_mod( 'sticky_menu_featured_page_custom_text', $this->default_settings['fp_custom_text'] );
								$cta['url']  = get_theme_mod( 'sticky_menu_featured_page_custom_url', $this->default_settings['fp_cutsom_url'] );
							}
							else {
								$cta['text'] = get_the_title( absint( $selected_page ) );
								$cta['url']  = get_permalink( absint( $selected_page ) );
							}

							// Start output buffer for displaying the CTA html output.
							ob_start();
						?>
							<!-- Call to Action -->
							<div class="pt-sticky-menu__call-to-action  hidden-md-down">
								<a class="btn  <?php echo apply_filters( 'pt-sticky-menu/cta_button_class', 'btn-primary' ); ?>" target="<?php echo esc_attr( $cta['target'] ); ?>" href="<?php echo esc_url( $cta['url'] ); ?>" tabindex="-1">
									<?php if ( ! empty( $cta['icon'] ) ) : ?>
										<i class="fa  <?php echo esc_attr( $cta['icon'] ); ?>"></i>
									<?php endif; ?>
									<?php echo esc_html( $cta['text'] ); ?>
								</a>
							</div>
						<?php

							// End and collect CTA buffer output.
							$cta_html_output = ob_get_clean();

							// Display the CTA HTML output (can be replaced via filter).
							echo wp_kses_post( apply_filters( 'pt-sticky-menu/cta_html_output', $cta_html_output, $cta ) );
						endif;
						?>
					</nav>
					<!-- Hamburger Menu for mobile/tablet -->
					<div class="pt-sticky-menu__hamburger  hidden-lg-up">
						<a href="#" class="btn  <?php echo apply_filters( 'pt-sticky-menu/mobile_menu_button_class', 'btn-primary' ); ?>  js-pt-sticky-menu-back-to-top-open-menu" tabindex="-1">
							<i class="fa  fa-bars"></i> <span class="pt-sticky-menu__hamburger-text"><?php esc_html_e( 'MENU' , 'pt-sticky-menu' ); ?></span>
						</a>
					</div>
				</div>
			</div>
		</div>
	<?php
		endif;
	}


	/**
	 * Add tabindex to the sticky menu links
	 * @param  array $atts existing attributes
	 * @param  object $item
	 * @param  stdObj $args
	 * @return array
	 */
	public function nav_menu_link_attributes( $atts, $item, $args ) {
		if ( $args->menu_id === self::MENU_ID && ! array_key_exists( 'tabindex', $atts ) ) {
			$atts['tabindex'] = '-1';
		}

		return $atts;
	}


	/**
	 * Append the right body classes to the <body>
	 *
	 * @param  array $classes Array of already defined classes.
	 * @return array
	 */
	public function body_class( $classes ) {
		if ( get_theme_mod( 'sticky_menu_select', $this->default_settings['sticky_selected'] ) ) {
			$classes[] = 'js-sticky-navigation';
		}

		return $classes;
	}


	/**
	 * Helper function: Get logo dimensions from the db.
	 *
	 * @param  string $theme_mod theme mod where the array with width and height is saved.
	 * @return string
	 */
	private function get_logo_dimensions( $theme_mod = 'logo_dimensions_array' ) {
		$width_height_array = get_theme_mod( $theme_mod );

		if ( is_array( $width_height_array ) && 2 === count( $width_height_array ) ) {
			return sprintf( ' width="%d" height="%d" ', absint( $width_height_array['width'] ), absint( $width_height_array['height'] ) );
		}
		else {
			return '';
		}
	}


	/**
	 * Private clone method to prevent cloning of the instance of the *Singleton* instance.
	 *
	 * @return void
	 */
	private function __clone() {}


	/**
	 * Private unserialize method to prevent unserializing of the *Singleton* instance.
	 *
	 * @return void
	 */
	private function __wakeup() {}
}
