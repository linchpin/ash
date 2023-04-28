<?php
/**
 * Utility functions for the theme.
 *
 * This file is for custom helper functions.
 * These should not be confused with WordPress template
 * tags. Template tags typically use prefixing, as opposed
 * to Namespaces.
 *
 * @link https://developer.wordpress.org/themes/basics/template-tags/
 * @package Ash
 */

namespace Ash\Utility;

/**
 * Get asset info from extracted asset files
 *
 * @param string $slug      Asset slug as defined in build/webpack configuration.
 * @param string $attribute Optional attribute to get. Can be version or dependencies.
 *
 * @return string|array
 */
function get_asset_info( string $slug, $attribute = null ) {
    if ( ! file_exists( ASH_THEME_PATH . 'dist/' . $slug . '.asset.php' ) ) {
        return null;
    }

    $asset = require ASH_THEME_PATH . 'dist/' . $slug . '.asset.php';

    if ( ! empty( $attribute ) && isset( $asset[ $attribute ] ) ) {
        return $asset[ $attribute ];
    }

    return $asset;
}

/**
 * Gets the SVG code for a given icon.
 *
 * @param string $group The icon group.
 * @param string $icon  The icon.
 * @param int    $size  The icon size in pixels.
 *
 * @return string
 * @since Fluval 1.0
 */
function get_icon_svg( string $group, $icon, $size = 24 ): string {
    return Ash\SVG_Icons::get_svg($group, $icon, $size);
}

/**
 * Gets grouped products within a specified category.
 *
 * @param int $category_id The category ID.
 *
 * @return string|void|\WP_Query
 * @since Fluval 1.0
 */
function get_bundled_products($category_id = 0)
{
    if (! $category_id) {
        return;
    }

    $grouped_products_args = [
        'post_type' => 'product',
        'tax_query' => [
            'relation' => 'AND',
            [
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'grouped',
            ],
            [
                'taxonomy' => 'product_cat',
                'field'    => 'term_id',
                'terms'    => get_queried_object_id(),
            ],
        ],
    ];

    $grouped_products = new \WP_Query($grouped_products_args);

    if ($grouped_products->have_posts()) {
        return $grouped_products;
    } else {
        return '';
    }
}

/**
 * Display related posts and videos slider from a specified category.
 *
 * @param int $category_id
 * @param int $count
 *
 * @return void|\WP_Query
 * @since Fluval 1.0
 */
function related_videos_articles(int $term_id = 0, int $count = 6)
{
	global $post;

	if ( empty( $post ) ) {
		return;
	}

	$current_post = $post->ID;

	if ( ! $term_id ) {
		$term_id = get_primary_term_id();
	}

	$related_posts_args = [
		'post_type' => 'post',
		'posts_per_page' => $count,
		'tax_query' => [
			[
				'taxonomy' => 'category',
				'field'    => 'term_id',
				'terms'    => $term_id,
			]
		],
		'post__not_in' => [ $current_post ],
	];

	$related_posts = new \WP_Query( $related_posts_args );

	if ( $related_posts->have_posts() ) {
		get_template_part('partials/swiper/related-slider', null, [ 'posts' => $related_posts->posts ] );
	}
}

/**
 * Get the primary term ID of a given post.
 *
 * @param string $category
 * @param int $post_id
 *
 * @return bool|int
 * @since Fluval 1.0
 */
function get_primary_term_id( string $category = 'category', int $post_id = 0 ) {
	if ( ! $post_id ) {
		global $post;
		$post_id = $post->ID;
	}

	if ( ! $post_id ) {
		return false;
	}

	$primary_term_id = false;

	// Attempt to get ID of the primary term from Yoast
	if ( function_exists( 'yoast_get_primary_term_id' ) ) {
		$primary_term_id = yoast_get_primary_term_id( $category, (int) $post_id );
	}

	if ( ! $primary_term_id || 1 === $primary_term_id ) {
		// Yoast didnt have what we want, try get all the categories
		$post_terms = get_the_terms( $post_id, $category );

		if ( ! is_wp_error( $post_terms ) && false !== $post_terms ) {
			// Grab the first term to return
			$primary_term_id = $post_terms[0]->term_id;

			// Dont return Uncategorized
			if ( 1 === $primary_term_id ) {
				// Grab the next or reset the return value to false
				if ( isset( $post_terms[1] ) ) {
					$primary_term_id = $post_terms[1]->term_id;
				} else {
					$primary_term_id = false;
				}
			}
		}
	}
	
	return $primary_term_id;
}

/**
 * Get the primary term name of a given post.
 *
 * @param string $category
 * @param int $post_id
 *
 * @return mixed
 * @since Fluval 1.0
 */
function get_primary_term_name( string $category = 'category', int $post_id = 0 ) {
	$primary_term_id = get_primary_term_id( $category, $post_id );

	if ( $primary_term_id ) {
		$primary_term = get_term( $primary_term_id );
		$primary_term = $primary_term->name;

		return '<a class="term-link" href="' . get_term_link( $primary_term_id, $category ) . '">' . $primary_term . '</a>';
	} else {
		return '';
	}
}


function get_paginated_links( $prev_text = '&laquo;', $next_text = '&raquo;' ) {

	global $wp_query;

	$big        = 999999999; // Need an unlikely integer.
	$pagination = '';
	$current    = max( 1, get_query_var( 'paged' ) );

	$pages = paginate_links(
		array(
			'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
			'format'    => '?paged=%#%',
			'current'   => $current,
			'total'     => $wp_query->max_num_pages,
			'type'      => 'array',
			'prev_next' => true,
			'prev_text' => esc_html( $prev_text ),
			'next_text' => esc_html( $next_text ),
		)
	);

	if ( is_array( $pages ) ) {
		$paged = ( get_query_var( 'paged' ) === 0 ) ? 1 : get_query_var( 'paged' );

		$pagination .= '<ul class="archive-pagination has-text-centered-mobile">';

		$start_page = ( 1 === $current ) ? 1 : 0; // Need to offset if using prev_text / next_text.
		$page_count = $start_page;

		foreach ( $pages as $page ) {
			$pagination .= '<li' . ( ( $page_count === $paged ) ? ' class="current"' : '' ) . ">$page</li>";
			$page_count ++;
		}

		$pagination .= '</ul>';
	}

	return $pagination;
}

function fluval_cart_link() {
	$off_canvas_trigger = ( ! is_cart() && ! is_checkout() ) ? 'off-canvas-trigger' : '';
	?>
	<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" id="cart-flyout-trigger" class="cart-flyout-trigger <?php echo esc_html( $off_canvas_trigger ); ?> has-text-white" <?php if ( ! is_page( 'cart' ) && ! is_page( 'checkout' ) ) echo 'data-off-canvas="cart-flyout"'; ?> title="<?php esc_attr_e( 'View your shopping cart', 'fluval' ); ?>">
		<span class="icon-cart util-link-left"></span>
		<?php /* translators: %d: number of items in cart */ ?>
		<span class="header-cart-count util-link-right"><?php echo sprintf( __( 'Cart (<span class="wc-cart-count update-on-ajax-cart">%d</span>)', 'fluval' ), wp_kses_data( WC()->cart->get_cart_contents_count() ) ); ?></span>
	</a>
	<?php
}

function get_video_duration( $videoID, $post_id ) {
	if ( empty( $post_id ) ) {
		global $post;

		$post_id = $post->ID;
	}

	if ( $duration = get_post_meta( $post_id, 'video_duration', true ) ) {
		return $duration;
	}

	$google_feed = wp_remote_get("https://www.googleapis.com/youtube/v3/videos?part=contentDetails&id=$videoID&key=" . YT_KEY );

	if ( is_wp_error( $google_feed ) ) {
		return '';
	}

	$video_duration = json_decode( wp_remote_retrieve_body( $google_feed ), true );

	// If no duration can be found in the json die early.
	if ( empty( $video_duration ) ) {
		return '';
	}

	foreach ( $video_duration['items'] as $vidTime ) {
		$video_duration = $vidTime['contentDetails']['duration'];
	}

	if ( is_string( $video_duration ) ) {
		preg_match_all('/(\d+)/', $video_duration, $parts );
	}

	if ( empty( $parts ) ) {
		return '';
	}

	$duration_parts = $parts[0];
	$duration_parts = array_filter( $duration_parts );

	if ( $duration_parts[1] < 10 ) {
		$duration_parts[1] = '0' . $duration_parts[1];
	}

	$duration = implode( ':', $duration_parts );

	add_post_meta( $post_id, 'video_duration', $duration );

	return $duration;

}

/**
 * Get the product images for use on PDP primarily.
 *
 * @param \WC_Product|null $product The WooCommerce Product.
 *
 * @return array
 */
function fluval_get_products_images( $product = null ) {

	if ( ! $product ) {
		global $product;
	}

	$image_ids  = [];
	$variations = [];

	if ( ! $product ) {
		return $image_ids;
	}

	$attachment_ids = $product->get_gallery_image_ids();

	if ( $product->is_type( 'variable' ) ) {
		$variations = $product->get_available_variations();
	}

	if ( ! empty( wp_get_attachment_image( $product->get_image_id() ) ) ) {
		$default_img_id = $product->get_image_id();
		$image_ids[ $default_img_id ] = 'default';
	}

	if ( ! empty( $variations ) ) {
		foreach ( $variations as $variation ) {
			$attribute_tag   = array_key_first( $variation['attributes'] );

			if ( empty( $attribute_tag ) ) {
				continue;
			}

			$variation_name  = $variation['attributes'][ $attribute_tag ];

			// If no label fall back to the key?
			if ( empty( $variation_name ) ) {
				$variation_name = $attribute_tag;
			}

			$variation_image = $variation['image_id'];

			// Don't show hidden variations.
			if ( $variation['variation_is_visible'] !== true ) {
				continue;
			}

			if ( array_key_exists( $variation_image, $image_ids ) ) {
				$image_ids[ $variation_image ] = $image_ids[ $variation_image ] . ' img-for-' . $variation_name;
			} else {
				$image_ids[ $variation_image ] = 'img-for-' . $variation_name;
			}
		}
	}

	foreach ( $attachment_ids as $attachment_id ) {
		if ( ! array_key_exists( $attachment_id, $image_ids ) ) {
			$image_ids[ $attachment_id ] = 'attachment';
		}
	}

	return $image_ids;

}
