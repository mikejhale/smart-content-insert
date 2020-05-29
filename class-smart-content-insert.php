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
	 * @param string $insert_value HTML to be inserted.
	 * @param int    $insert_after The count of paragraphs to insert the tag.
	 * @param bool   $strict       Ignore blank paragraphs, images, headers, etc.
	 * @param string $delimiter    The delimiter to separate the content.
	 * @return string
	 */
	public function insert_into_paragraphs( $content, $insert_value = '', $insert_after = 1 ) {

		if ( empty( $insert_value ) ) {
			return $content;
		}

		$p_index  = 1;
		$document = new \DOMDocument();
		@$document->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ), LIBXML_HTML_NODEFDTD );

		$xpath      = new \DOMXPath( $document );
		$paragraphs = $xpath->query( '//body/p' );
		if ( $paragraphs->length < $insert_after ) {
			return $content;
		}

		foreach ( $paragraphs as $p ) {

			if ( ! $p->hasChildNodes() ) {
				continue;
			}

			if ( $p->childNodes->length > 1 || ( 1 === $p->childNodes->length && ( 'strong' !== $p->firstChild->nodeName && 'img' !== $p->firstChild->nodeName && "\xC2\xA0" !== $p->firstChild->textContent ) ) ) {

				if ( $p_index === $insert_after ) {

					// Create a node from the insert value.
					$value_dom = new \DOMDocument();
					@$value_dom->loadHTML( mb_convert_encoding( $insert_value, 'HTML-ENTITIES', 'UTF-8' ) );

					// add to content.
					$value_node = $document->importNode( $value_dom->childNodes[1], true );
					$p->parentNode->insertBefore( $value_node, $p->nextSibling );

					// remove elements added by DOMDocument.
					$content = $document->saveHTML();
					$content = str_replace( '<html><body>', '', $content );
					$content = str_replace( '</body></html>', '', $content );

					return $content;
				}

				$p_index++;
			}
		}

		return $content;
	}

	/**
	 * Get paragraphs from content.
	 *
	 * @param string $content   The content being parsed.
	 * @param bool   $strict    If the paragraphs should be filtered.
	 * @param string $delimiter The paragraph delimiter.
	 * @return int
	 */
	public function get_paragraph_count( $content, $strict = true, $delimiter = null ) {
		$count        = 0;
		$delimiter    = $delimiter ? $delimiter : '</p>';
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

	/**
	 * Insert element into paragraphs from the_content.
	 *
	 * @param string $content       The content.
	 * @param string $insert_value  The value to be inserted into the content.
	 * @param string $selector      The selector value to match.
	 * @param string $selector_type Selector to match (id or class) Default: id.
	 * @param string $selector_tag  The tag to match.
	 * @param int    $instance      The instance of the match to insert (first, second, etc).
	 * @param bool   $insert_before Insert before the matching tag. Default: false.
	 * @return string
	 */
	public function insert_at_element( $content, $insert_value, $selector, $selector_type = 'id', $selector_tag = 'div', $instance = 1, $insert_before = false ) {

		// if nothing to insert, just return.
		if ( ! $insert_value ) {
			return $content;
		}

		$dom = new \DOMDocument();
		@$dom->loadHTML( mb_convert_encoding( $content, 'HTML-ENTITIES', 'UTF-8' ) );

		if ( 'id' === $selector_type ) {
			// get by ID.
			$insert_node = $dom->getElementByID( $selector );
		} else {
			// get by class.
			$xpath = new \DomXPath( $dom );
			$nodes = $xpath->query(
				sprintf(
					'//%s[contains(concat(\' \', normalize-space(@class), \' \'), \' %s \')]',
					$selector_tag,
					$selector
				)
			);

			// check that we have enough nodes.
			if ( $nodes->length < $instance ) {
				return $content;
			}

			$insert_node = $nodes[ $instance - 1 ];
		}

		// Create a node from the insert value.
		$value_dom = new \DOMDocument();
		@$value_dom->loadHTML( mb_convert_encoding( $insert_value, 'HTML-ENTITIES', 'UTF-8' ) );

		// add to content.
		$value_node = $dom->importNode( $value_dom->childNodes[1], true );

		if ( $insert_before ) {
			$insert_node->parentNode->insertBefore( $value_node, $insert_node );
		} else {
			$insert_node->parentNode->insertBefore( $value_node, $insert_node->nextSibling );
		}

		return $dom->saveHTML();
	}

	/**
	 * Build a DOMElement.
	 *
	 * @param array $args Element args.
	 * @return \DOMElement
	 */
	public function build_element( $args ) {

	}

}

new Smart_Content_Insert();
