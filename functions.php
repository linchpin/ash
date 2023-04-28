<?php
/**
 * This file adds functions to the Ash WordPress theme.
 *
 * @package Ash
 * @author  Linchpin
 * @license GNU General Public License v2 or later
 * @link    https://frostwp.com/
 */

// x-release-please-start-version
define( 'ASH_THEME_VERSION', '1.1.0' ); // Version number is bumped automatically by release please since 01/04/2023
// x-release-please-end

define( 'ASH_THEME_NAME', esc_html__( 'Ash', 'Ash' ) );
define( 'ASH_THEME_INC', get_stylesheet_directory() . '/includes/' );
define( 'ASH_THEME_DEBUG', false );

if ( ! defined('SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', true ); // Enable script debug by default. Should be disabled in production.
}

// Define our includes
require_once ASH_THEME_INC . 'utilities.php';
require_once ASH_THEME_INC . 'navigation.php';
require_once ASH_THEME_INC . 'core.php';

// Require Composer autoloader if it exists.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

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