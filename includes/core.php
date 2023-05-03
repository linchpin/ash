<?php
/**
 * Ash Core Functionality
 *
 * @author  Linchpin
 * @package Ash
 */

namespace Ash\Core;

use Ash\Utility;

function setup() {
	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_action( 'after_setup_theme', $n( 'i18n' ) );
	add_action( 'after_setup_theme', $n( 'theme_setup' ) );
	add_action( 'wp_enqueue_scripts', $n( 'scripts' ) );
	add_action( 'wp_enqueue_scripts', $n( 'styles' ) );
	add_action( 'wp_head', $n( 'js_detection' ), 0 );
	add_action( 'wp_head', $n( 'add_manifest' ), 10 );

	// Editor / Admin Scripts
	add_action( 'enqueue_block_editor_assets', $n( 'block_scripts' ) );
	add_action( 'admin_enqueue_scripts', $n( 'admin_scripts' ) );

	// Jetpack Scrolling
	add_action( 'after_setup_theme', $n( 'jetpack_scroll_settings' ) );
	add_filter( 'infinite_scroll_js_settings', $n( 'jetpack_scroll_button' ) );

	add_action( 'init', $n( 'remove_woocommerce_infinite_styles' ) );
	add_filter( 'script_loader_tag', $n( 'script_loader_tag' ), 10, 2 );

	add_filter( 'render_block_core/shortcode', $n( 'render_block_core_shortcode' ), 10, 3, );

}

/**
 * Makes Theme available for translation.
 *
 * Translations can be added to the /languages directory.
 * If you're building a theme based on "ash", change the
 * filename of '/languages/ash.pot' to the name of your project.
 *
 * @return void
 */
function i18n() {
	load_theme_textdomain( 'ash', get_template_directory() . '/languages' );
}

/**
 * Render the shortcode in a block
 *
 * @since 1.1.0
 *
 * @param $content
 * @param $parsed_block
 * @param $block
 *
 * @return mixed
 */
function render_block_core_shortcode( $content, $parsed_block, $block ) {
	$content = do_shortcode( $content );
	return $content;

}


/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function theme_setup() {
	add_theme_support( 'woocommerce' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support(
		'html5',
		array(
			'search-form',
			'gallery',
		)
	);

	add_theme_support( 'editor-styles' );

	add_editor_style( get_stylesheet_directory_uri() . 'css/ash-editor.css' );

	// This theme uses wp_nav_menu() in three locations.
	// REGISTER YOUR MENUS HERE
}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function scripts() {
	wp_enqueue_script( 'ash-js', get_stylesheet_directory_uri() . '/js/ash.js', array(), wp_get_theme()->get( 'Version' ), true );
}

/**
 * Enqueue scripts for front-end.
 *
 * @return void
 */
function styles() {
	wp_enqueue_style( 'ash', get_stylesheet_directory_uri() . '/css/ash.css', array(), wp_get_theme()->get( 'Version' ) );
}

/**
 * Enqueue scripts for admin
 *
 * @return void
 */
function admin_scripts() {
	wp_enqueue_style( 'ash-admin-css', get_stylesheet_directory_uri() . '/css/admin.css', array(), wp_get_theme()->get( 'Version' ) );
}

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since 1.1.0
 *
 * @return void
 */
function js_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}


/**
 * Add async/defer attributes to enqueued scripts that have the specified script_execution flag.
 *
 * @link https://core.trac.wordpress.org/ticket/12009
 * @param string $tag    The script tag.
 * @param string $handle The script handle.
 * @return string
 */
function script_loader_tag( $tag, $handle ) {
	$script_execution = wp_scripts()->get_data( $handle, 'script_execution' );

	if ( ! $script_execution ) {
		return $tag;
	}

	if ( 'async' !== $script_execution && 'defer' !== $script_execution ) {
		return $tag;
	}

	// Abort adding async/defer for scripts that have this script as a dependency. _doing_it_wrong()?
	foreach ( wp_scripts()->registered as $script ) {
		if ( in_array( $handle, $script->deps, true ) ) {
			return $tag;
		}
	}

	// Add the attribute if it hasn't already been added.
	if ( ! preg_match( ":\s$script_execution(=|>|\s):", $tag ) ) {
		$tag = preg_replace( ':(?=></script>):', " $script_execution", $tag, 1 );
	}

	return $tag;

}

/**
 * Appends a link tag used to add a manifest.json to the head
 *
 * @return void
 */
function add_manifest() {
	echo "<link rel='manifest' href='" . esc_url( get_stylesheet_directory_uri() . '/manifest.json' ) . "' />";
}

function jetpack_scroll_settings() {

	$n = function ( $function ) {
		return __NAMESPACE__ . "\\$function";
	};

	add_theme_support(
		'infinite-scroll',
		array(
			'container' => 'infinite',
			'type'      => 'click',
			'footer'    => false,
			'wrapper'   => false,
			'render'    => $n( 'infinite_post_render' ),
		)
	);

}

function jetpack_scroll_button( $settings ) {
	$settings['text'] = esc_html__( 'Load More', 'ash' );
	return $settings;
}


/**
 * Infinite Scroll render function
 *
 * @since 1.1.0
 *
 * @return void
 */
function infinite_post_render() {

	while ( have_posts() ) {
		the_post();

		get_template_part( 'partials/loop' );
	}
}
