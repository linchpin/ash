<?php

/**
 * Ash WordPress theme.
 *
 * It is built on top of Frost, a theme by WP Engine.
 *
 * @package Ash
 * @author  Linchpin
 * @license GNU General Public License v2 or later
 * @link    https://github.com/linchpin/ash/
 */

// x-release-please-start-version
define( 'ASH_THEME_VERSION', '1.0.4' ); // Version number is bumped automatically by release please
// x-release-please-end

define( 'ASH_THEME_NAME', esc_html__( 'Ash', 'Ash' ) );
define( 'ASH_THEME_INC', get_stylesheet_directory() . '/includes/' );
define( 'ASH_THEME_DEBUG', false );

if ( ! defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', true ); // Enable script debug by default. Should be disabled in production.
}

// Define our includes
require_once ASH_THEME_INC . 'utilities.php';
require_once ASH_THEME_INC . 'navigation.php';
require_once ASH_THEME_INC . 'core.php';

if ( ! function_exists( 'ash_setup' ) ) {

	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 *
	 * @since 0.8.0
	 *
	 * @return void
	 */
	function ash_setup() {
		// Make theme available for translation.
		load_theme_textdomain( 'ash', get_template_directory() . '/languages' );

		// Enqueue editor styles and fonts.
		add_editor_style(
			array(
				'./css/ash.css',
			)
		);

		// Remove core block patterns.
		remove_theme_support( 'core-block-patterns' );
	}
}



add_action( 'after_setup_theme', 'ash_setup' );

// Enqueue style sheet.
add_action( 'wp_enqueue_scripts', 'ash_enqueue_style_sheet' );

function ash_enqueue_style_sheet() {
	wp_enqueue_style( 'ash', get_template_directory_uri() . '/css/ash.css', array(), wp_get_theme()->get( 'Version' ) );
}

/**
 * Register block styles.
 *
 * @since 0.9.2
 */
function ash_register_block_styles() {
	$block_styles = array(
		'core/button'          => array(
			'fill-base'    => __( 'Fill Base', 'ash' ),
			'outline-base' => __( 'Outline Base', 'ash' ),
		),
		'core/columns'         => array(
			'columns-reverse' => __( 'Reverse', 'ash' ),
		),
		'core/group'           => array(
			'shadow'       => __( 'Shadow', 'ash' ),
			'shadow-solid' => __( 'Shadow Solid', 'ash' ),
		),
		'core/list'            => array(
			'no-disc' => __( 'No Disc', 'ash' ),
		),
		'core/navigation-link' => array(
			'outline' => __( 'Outline', 'ash' ),
		),
		'core/social-links'    => array(
			'outline' => __( 'Outline', 'ash' ),
		),
	);
}

// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}
add_action( 'init', 'ash_register_block_styles' );

/**
 * Registers block categories, and type.
 *
 * @since 0.9.2
 */
function ash_register_block_pattern_categories() {
	/* Functionality specific to the Block Pattern Explorer plugin. */
	if ( function_exists( 'register_block_pattern_category_type' ) ) {
		register_block_pattern_category_type( 'ash', array( 'label' => __( 'Ash', 'ash' ) ) );
	}

	$block_pattern_categories = array(
		'ash-footer'    => array(
			'label'         => __( 'Footer', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'ash-general'   => array(
			'label'         => __( 'General', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'ash-header'    => array(
			'label'         => __( 'Header', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'ash-page'      => array(
			'label'         => __( 'Page', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'ash-query'     => array(
			'label'         => __( 'Query', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
		'ash-proposals' => array(
			'label'         => __( 'Proposals', 'ash' ),
			'categoryTypes' => array( 'ash' ),
		),
	);

	foreach ( $block_pattern_categories as $name => $properties ) {
		register_block_pattern_category( $name, $properties );
	}
}

add_action( 'init', 'ash_register_block_pattern_categories', 9 );

if ( ! function_exists( 'wp_body_open' ) ) {
	do_action( 'wp_body_open' );
}

// Kick everything off when plugins are loaded.
try {
	Ash\Core\setup();
	Ash\Blocks\setup();
} catch ( Exception $e ) {
	wp_die( esc_html( $e->getMessage() ) );
}
