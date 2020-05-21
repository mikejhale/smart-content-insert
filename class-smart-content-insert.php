<?php
/**
 * Smart_Content_Insert class file.
 *
 * @package Smart_Content_Insert
 */

/*
Plugin Name: Smart Content Insert
Plugin URI: https://stompgear.com/plugins/smart-insert-into-paragraphs
Description: Insert content based on paragraph count.
Version: 1.0.
Author: Mike Hale
Author URI: https://mikehale.me
License: GPL-2.0+
Text Domain: smart-content-insert
Domain Path: /languages
*/

namespace Stompgear;

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/**
 * Class Smart_Content_Insert
 *
 * Handles adding content by paragraph.
 */
class Smart_Content_Insert {

	/**
	 * Smart_Content_Insert class constructor.
	 */
	public function __construct() {
	}

	/**
	 * Insert element into paragraphs from the_content.
	 *
	 * @param string $content      The content.
	 * @param string $delimiter    The delimiter to separate the content.
	 * @param string $insert_value The value to be inserted into the content.
	 * @param int    $insert_after The count of paragraphs to insert the tag.
	 * @param bool   $strict       Ignore blank paragraphs, images, headers, etc.
	 * @return string
	 */
	public function insert_into_paragraphs( $content, $insert_value = '', $insert_after = 1, $strict = true, $delimiter = '' ) {

		$delimiter    = $delimiter ? $delimiter : "\r\n";
		$paragraphs   = explode( $delimiter, $content );
		$index_p      = 0;
		$insert_after = apply_filters( 'smart_content_insert_after', $insert_after );
		$ignored      = apply_filters(
			'smart_content_insert_strict_filters', 
			array(
				'/^<img.*?[^\>]+>$/',        // img tags.
				'/^<strong>.*?<\/strong>$/', // string tags (used sometimes as a header)
				'/<h[1-6]>.*?<\/h[1-6]>$/',  //	h1 - h6 header tags.
			)
		);

		foreach ( $paragraphs as $p ) {

			if ( $strict ) {
				$p    = trim( $p );
				$is_p = true;
				if ( strlen( $p ) !== 0 && '&nbsp;' !== $p ) {
					foreach ( $ignored as $pattern ) {
						if ( preg_match( $pattern, $p ) ) {
							$is_p = false;
							continue;
						}
					}

					if ( $is_p ) {
						$index_p++;
					}
				}
			} else {
				$index_p++;
			}

			if ( $insert_after === $index_p ) {
				$content = substr_replace( $content, $insert_value, strpos( $content, $p ) + strlen( $p ), 0 );
				break;
			}
		}

		return $content;
	}

	/**
	 * Get paragraphs from content.
	 *
	 * @param string $content   The content being parsed.
	 * @param string $delimiter The paragraph delimiter.
	 * @param bool   $strict    If the paragraphs should be filtered.
	 * @return int
	 */
	public function get_paragraph_count( $content, $strict = true, $delimiter = null ) {
		$count        = 0;
		$delimiter    = $delimiter ? $delimiter : "\r\n";
		$paragraphs   = explode( $delimiter, $content );

		foreach ( $paragraphs as $p ) {
			if ( $strict ) {
				$p       = trim( $p );
				$is_p    = true;
				$ignored = apply_filters(
					'smart_content_insert_strict_filters',
					array(
						'/^<img.*?[^\>]+>$/',        // img tags.
						'/^<strong>.*?<\/strong>$/', // string tags (used sometimes as a header).
						'/<h[1-6]>.*?<\/h[1-6]>$/',  //	h1 - h6 header tags.
					)
				);
				if ( strlen( $p ) !== 0 && '&nbsp;' !== $p ) {
					foreach ( $ignored as $pattern ) {
						if ( preg_match( $pattern, $p ) ) {
							$is_p = false;
							continue;
						}
					}

					if ( $is_p ) {
						$count++;
					}
				}
			} else {
				$count++;
			}
		}

		return $count;
	}
}

new Smart_Content_Insert();
