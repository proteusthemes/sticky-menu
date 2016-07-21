<?php
namespace ProteusThemes\StickyMenu;

use ProteusThemes\CustomizerUtils\Helpers;

/**
 * Customizer settings and controls for the PT sticky menu.
 */

/**
 * Contains settings, controls, methods for the PT sticky menu customizer.
 */
class Customizer {

	/**
	 * The singleton manager instance
	 *
	 * @see wp-includes/class-wp-customize-manager.php
	 * @var WP_Customize_Manager
	 */
	protected $wp_customize;

	/**
	 * Construct/initialization method of PT_Sticky_Menu_Customizer class.
	 *
	 * @param WP_Customize_Manager $wp_manager The customizer manager.
	 */
	public function __construct( \WP_Customize_Manager $wp_manager ) {

		// Set the private property to instance of wp_manager.
		$this->wp_customize = $wp_manager;

		// Register the sections/settings/controls, main method.
		$this->register();

		// Save sticky menu specific logo width/height dimensions.
		add_action( 'customize_save_sticky_logo_img' , array( $this, 'save_sticky_logo_dimensions' ), 10, 1 );
	}

	/**
	 * This hooks into 'customize_register' and allows you to add
	 * new sections and controls to the Theme Customize screen.
	 */
	public function register() {

		// Get filter data.
		$theme_panel = apply_filters( 'pt-sticky-menu/theme_panel', array(
			'panel'    => 'panel_autopt',
			'priority' => 31,
		) );

		$settings_defaults = apply_filters( 'pt-sticky-menu/settings_default', array(
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

		// Section.
		$this->wp_customize->add_section( 'sticky_menu_section', array(
			'title'       => esc_html__( 'Sticky Menu', 'pt-sticky-menu' ),
			'description' => esc_html__( 'Settings for the sticky menu', 'pt-sticky-menu' ),
			'priority'    => $theme_panel['priority'],
			'panel'       => $theme_panel['panel'],
		) );

		// Settings.
		$this->wp_customize->add_setting( 'sticky_menu_select', array(
			'default' => $settings_defaults['sticky_selected'],
		) );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_select', array(
			'default' => $settings_defaults['fp_select'],
		) );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_custom_text', array(
			'default' => $settings_defaults['fp_custom_text'],
		) );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_custom_url', array(
			'default' => $settings_defaults['fp_cutsom_url'],
		) );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_open_in_new_window', array(
			'default' => $settings_defaults['fp_new_window'],
		) );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_icon', array(
			'default' => $settings_defaults['fp_icon'],
		) );
		$this->wp_customize->add_setting( 'sticky_menu_bg_color', array(
			'default' => $settings_defaults['fp_bg_color'],
		) );
		$this->wp_customize->add_setting( 'sticky_logo_selected', array(
			'default' => $settings_defaults['logo_selected'],
		) );
		$this->wp_customize->add_setting( 'sticky_logo_img', array(
			'default' => $settings_defaults['logo_img'],
		) );
		$this->wp_customize->add_setting( 'sticky_logo2x_img', array(
			'default' => $settings_defaults['logo2x_img'],
		) );

		// Controls.
		$this->wp_customize->add_control( 'sticky_menu_select', array(
			'type'     => 'checkbox',
			'priority' => 10,
			'label'    => esc_html__( 'Enable sticky menu', 'pt-sticky-menu' ),
			'section'  => 'sticky_menu_section',
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_select', array(
			'type'            => 'select',
			'priority'        => 20,
			'label'           => esc_html__( 'Featured page', 'pt-sticky-menu' ),
			'description'     => esc_html__( 'To which page should the Call-to-action button link to?', 'pt-sticky-menu' ),
			'section'         => 'sticky_menu_section',
			'choices'         => Helpers::get_all_pages_id_title(),
			'active_callback' => array( $this, 'is_sticky_menu_selected' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_custom_text', array(
			'priority'        => 30,
			'label'           => esc_html__( 'Custom button text', 'pt-sticky-menu' ),
			'section'         => 'sticky_menu_section',
			'active_callback' => array( $this, 'is_featured_page_custom_url' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_custom_url', array(
			'priority'        => 40,
			'label'           => esc_html__( 'Custom URL', 'pt-sticky-menu' ),
			'section'         => 'sticky_menu_section',
			'active_callback' => array( $this, 'is_featured_page_custom_url' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_open_in_new_window', array(
			'type'            => 'checkbox',
			'priority'        => 50,
			'label'           => esc_html__( 'Open link in a new window/tab?', 'pt-sticky-menu' ),
			'section'         => 'sticky_menu_section',
			'active_callback' => array( $this, 'is_featured_page_selected' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_icon', array(
			'priority'        => 60,
			'label'           => esc_html__( 'Font Awesome icon', 'pt-sticky-menu' ),
			'description'     => sprintf( esc_html__( 'Insert a %s icon. Example: fa-phone.', 'pt-sticky-menu' ), '<a href="http://fontawesome.io/icons/" target="_blank">Font Awesome</a>' ),
			'section'         => 'sticky_menu_section',
			'active_callback' => array( $this, 'is_featured_page_selected' ),
		) );

		$this->wp_customize->add_control( new \WP_Customize_Color_Control(
			$this->wp_customize,
			'sticky_menu_bg_color',
			array(
				'priority'        => 70,
				'label'           => esc_html__( 'Sticky menu background color', 'pt-sticky-menu' ),
				'section'         => 'sticky_menu_section',
				'active_callback' => array( $this, 'is_sticky_menu_selected' ),
			)
		) );

		$this->wp_customize->add_control( 'sticky_logo_selected', array(
			'type'            => 'checkbox',
			'priority'        => 80,
			'label'           => esc_html__( 'Set custom logos for sticky menu?', 'pt-sticky-menu' ),
			'section'         => 'sticky_menu_section',
			'active_callback' => array( $this, 'is_sticky_menu_selected' ),
		) );

		$this->wp_customize->add_control( new \WP_Customize_Image_Control(
			$this->wp_customize,
			'sticky_logo_img',
			array(
				'priority'        => 90,
				'label'           => esc_html__( 'Logo Image', 'pt-sticky-menu' ),
				'section'         => 'sticky_menu_section',
				'active_callback' => array( $this, 'is_custom_logo_selected' ),
			)
		) );
		$this->wp_customize->add_control( new \WP_Customize_Image_Control(
			$this->wp_customize,
			'sticky_logo2x_img',
			array(
				'priority'        => 100,
				'label'           => esc_html__( 'Retina Logo Image', 'pt-sticky-menu' ),
				'description'     => esc_html__( '2x logo size, for screens with high DPI.', 'pt-sticky-menu' ),
				'section'         => 'sticky_menu_section',
				'active_callback' => array( $this, 'is_custom_logo_selected' ),
			)
		) );
	}

	/**
	 * Returns if the sticky menu is selected.
	 *
	 * Used by the sticky_menu_featured_page_select and sticky_menu_bg_color controls.
	 *
	 * @return boolean
	 */
	public function is_sticky_menu_selected() {
		return get_theme_mod( 'sticky_menu_select' );
	}

	/**
	 * Returns if sticky menu and the featured page are selected.
	 *
	 * Used by the featured_page_open_in_new_window and sticky_menu_featured_page_icon controls.
	 *
	 * @return boolean
	 */
	public function is_featured_page_selected() {
		return $this->is_sticky_menu_selected() && ( 'none' !== get_theme_mod( 'sticky_menu_featured_page_select', 'none' ) );
	}

	/**
	 * Returns if sticky menu and Custom URL page are selected.
	 *
	 * Used by the sticky_menu_featured_page_custom_text and sticky_menu_featured_page_custom_url controls.
	 *
	 * @return boolean
	 */
	public function is_featured_page_custom_url() {
		return $this->is_sticky_menu_selected() && ( 'custom-url' === get_theme_mod( 'sticky_menu_featured_page_select', 'none' ) );
	}

	/**
	 * Returns if sticky menu and the custom logo checkboxes are selected.
	 *
	 * Used by the sticky_logo_img and sticky_logo2x_img controls.
	 *
	 * @return boolean
	 */
	public function is_custom_logo_selected() {
		return $this->is_sticky_menu_selected() && get_theme_mod( 'sticky_logo_selected' );
	}

	/**
	 * Set the dimensions of the sticky logo image when the setting is saved.
	 * This is purely a performance improvement.
	 *
	 * Used by hook: add_action( 'customize_save_sticky_logo_img' , array( $this, 'save_sticky_logo_dimensions' ), 10, 1 );
	 *
	 * @return void
	 */
	public static function save_sticky_logo_dimensions( $setting ) {
		$logo_width_height = array();
		$img_data          = getimagesize( esc_url( $setting->post_value() ) );

		if ( is_array( $img_data ) ) {
			$logo_width_height = array_slice( $img_data, 0, 2 );
			$logo_width_height = array_combine( array( 'width', 'height' ), $logo_width_height );
		}

		set_theme_mod( 'sticky_logo_dimensions_array', $logo_width_height );
	}
}
