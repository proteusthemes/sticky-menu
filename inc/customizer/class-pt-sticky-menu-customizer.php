<?php
/**
 * Customizer settings and controls for the PT sticky menu.
 *
 * @package pt-sticky-menu
 */

namespace ProteusThemes\StickyMenu;

/**
 * Contains settings, controls, methods for the PT sticky menu customizer.
 */
class PT_Sticky_Menu_Customizer {

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
	public function __construct( WP_Customize_Manager $wp_manager ) {

		// Set the private property to instance of wp_manager.
		$this->wp_customize = $wp_manager;

		// Register the settings/panels/sections/controls, main method.
		$this->register();
	}

	/**
	 * This hooks into 'customize_register' and allows you to add
	 * new sections and controls to the Theme Customize screen.
	 */
	public function register() {

		// Section.
		$this->wp_customize->add_section( 'autopt_section_sticky_menu', array(
			'title'       => esc_html__( 'Sticky Menu', 'auto-pt' ),
			'description' => esc_html__( 'Settings for the sticky menu', 'auto-pt' ),
			'priority'    => 31,
			'panel'       => 'panel_autopt',
		) );

		// Settings.
		$this->wp_customize->add_setting( 'sticky_menu_select' );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_select', array( 'default' => 'none' ) );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_custom_text' );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_custom_url' );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_open_in_new_window' );
		$this->wp_customize->add_setting( 'sticky_menu_featured_page_icon' );
		$this->wp_customize->add_setting( 'sticky_menu_bg_color', array( 'default' => '#ffffff' ) );

		// Controls.
		$this->wp_customize->add_control( 'sticky_menu_select', array(
			'type'        => 'checkbox',
			'priority'    => 111,
			'label'       => esc_html__( 'Enable sticky menu', 'auto-pt' ),
			'section'     => 'autopt_section_sticky_menu',
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_select', array(
			'type'        => 'select',
			'priority'    => 113,
			'label'       => esc_html__( 'Featured page', 'auto-pt' ),
			'description' => esc_html__( 'To which page should the Call-to-action button link to?', 'auto-pt' ),
			'section'     => 'autopt_section_sticky_menu',
			'choices'     => $this->get_all_pages_id_title(),
			'active_callback' => array( $this, 'is_sticky_menu_selected' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_custom_text', array(
			'priority'    => 115,
			'label'       => esc_html__( 'Custom Button Text', 'auto-pt' ),
			'section'     => 'autopt_section_sticky_menu',
			'active_callback' => array( $this, 'is_featured_page_custom_url' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_custom_url', array(
			'priority'    => 117,
			'label'       => esc_html__( 'Custom URL', 'auto-pt' ),
			'section'     => 'autopt_section_sticky_menu',
			'active_callback' => array( $this, 'is_featured_page_custom_url' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_open_in_new_window', array(
			'type'        => 'checkbox',
			'priority'    => 120,
			'label'       => esc_html__( 'Open link in a new window/tab.', 'auto-pt' ),
			'section'     => 'autopt_section_sticky_menu',
			'active_callback' => array( $this, 'is_featured_page_selected' ),
		) );

		$this->wp_customize->add_control( 'sticky_menu_featured_page_icon', array(
			'priority'    => 121,
			'label'       => esc_html__( 'Insert font Awesome Icon:', 'auto-pt' ),
			'section'     => 'autopt_section_sticky_menu',
			'active_callback' => array( $this, 'is_featured_page_selected' ),
		) );

		$this->wp_customize->add_control( new WP_Customize_Color_Control(
			$this->wp_customize,
			'sticky_menu_bg_color',
			array(
				'priority'    => 123,
				'label'       => esc_html__( 'Sticky menu background color', 'auto-pt' ),
				'section'     => 'autopt_section_sticky_menu',
				'active_callback' => array( $this, 'is_sticky_menu_selected' ),
			)
		) );
	}

	/**
	 * Returns all published pages (IDs and titles).
	 *
	 * Used by the sticky_menu_featured_page_select control.
	 *
	 * @return map with key: ID and value: title
	 */
	public function get_all_pages_id_title() {
		$args = array(
			'sort_order'  => 'ASC',
			'sort_column' => 'post_title',
			'post_type'   => 'page',
			'post_status' => 'publish',
		);
		$pages = get_pages( $args );

		// Create the pages map with the default value of none and the custom url option.
		$featured_page_choices               = array();
		$featured_page_choices['none']       = esc_html__( 'None', 'auto-pt' );
		$featured_page_choices['custom-url'] = esc_html__( 'Custom URL', 'auto-pt' );

		// Parse through the objects returned and add the key value pairs to the featured_page_choices map.
		foreach ( $pages as $page ) {
			$featured_page_choices[ $page->ID ] = $page->post_title;
		}

		return $featured_page_choices;
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
}
