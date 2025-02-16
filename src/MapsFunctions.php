<?php

namespace Maps;

use Xml;

/**
 * A class that holds static helper functions for generic mapping-related functions.
 *
 * @deprecated
 *
 * @licence GNU GPL v2+
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 */
final class MapsFunctions {

	/**
	 * Encode a variable of unknown type to JavaScript.
	 * Arrays are converted to JS arrays, objects are converted to JS associative
	 * arrays (objects). So cast your PHP associative arrays to objects before
	 * passing them to here.
	 *
	 * This is a copy of
	 *
	 * @see Xml::encodeJsVar
	 * which fixes incorrect behaviour with floats.
	 *
	 * @since 0.7.1
	 *
	 * @param mixed $value
	 *
	 * @return string
	 */
	public static function encodeJsVar( $value ) {
		if ( is_bool( $value ) ) {
			$s = $value ? 'true' : 'false';
		} elseif ( is_null( $value ) ) {
			$s = 'null';
		} elseif ( is_int( $value ) || is_float( $value ) ) {
			$s = $value;
		} elseif ( is_array( $value ) && // Make sure it's not associative.
			array_keys( $value ) === range( 0, count( $value ) - 1 ) ||
			count( $value ) == 0
		) {
			$s = '[';
			foreach ( $value as $elt ) {
				if ( $s != '[' ) {
					$s .= ', ';
				}
				$s .= self::encodeJsVar( $elt );
			}
			$s .= ']';
		} elseif ( is_object( $value ) || is_array( $value ) ) {
			// Objects and associative arrays
			$s = '{';
			foreach ( (array)$value as $name => $elt ) {
				if ( $s != '{' ) {
					$s .= ', ';
				}
				$s .= '"' . Xml::encodeJsVar( $name ) . '": ' .
					self::encodeJsVar( $elt );
			}
			$s .= '}';
		} else {
			$s = '"' . Xml::encodeJsVar( $value ) . '"';
		}
		return $s;
	}

	/**
	 * This function returns the definitions for the parameters used by every map feature.
	 *
	 * @return array
	 */
	public static function getCommonParameters() {
		$params = [];

		$params['width'] = [
			'type' => 'dimension',
			'allowauto' => true,
			'units' => [ 'px', 'ex', 'em', '%', '' ],
			'default' => $GLOBALS['egMapsMapWidth'],
			'message' => 'maps-par-width',
		];

		$params['height'] = [
			'type' => 'dimension',
			'units' => [ 'px', 'ex', 'em', '' ],
			'default' => $GLOBALS['egMapsMapHeight'],
			'message' => 'maps-par-height',
		];

		$params['centre'] = [
			'type' => 'string',
			'aliases' => [ 'center' ],
			'default' => false,
			'manipulatedefault' => false,
			'message' => 'maps-par-centre',
		];

		$params['title'] = [
			'name' => 'title',
			'default' => $GLOBALS['egMapsDefaultTitle'],
		];

		$params['label'] = [
			'default' => $GLOBALS['egMapsDefaultLabel'],
			'aliases' => 'text',
		];

		$params['icon'] = [
			'default' => '',
		];

		$params['lines'] = [
			'type' => 'mapsline',
			'default' => [],
			'delimiter' => ';',
			'islist' => true,
		];

		$params['polygons'] = [
			'type' => 'mapspolygon',
			'default' => [],
			'delimiter' => ';',
			'islist' => true,
		];

		$params['circles'] = [
			'type' => 'mapscircle',
			'default' => [],
			'delimiter' => ';',
			'islist' => true,
		];

		$params['rectangles'] = [
			'type' => 'mapsrectangle',
			'default' => [],
			'delimiter' => ';',
			'islist' => true,
		];

		$params['maxzoom'] = [
			'type' => 'integer',
			'default' => false,
			'manipulatedefault' => false,
			'dependencies' => 'minzoom',
		];

		$params['minzoom'] = [
			'type' => 'integer',
			'default' => false,
			'manipulatedefault' => false,
			'lowerbound' => 0,
		];

		$params['copycoords'] = [
			'type' => 'boolean',
			'default' => false,
		];

		$params['static'] = [
			'type' => 'boolean',
			'default' => false,
		];

		// Give grep a chance to find the usages:
		// maps-displaymap-par-title, maps-displaymap-par-label, maps-displaymap-par-icon,
		// aps-displaymap-par-lines, maps-displaymap-par-polygons,
		// maps-displaymap-par-circles, maps-displaymap-par-rectangles,
		// maps-displaymap-par-maxzoom, maps-displaymap-par-minzoom, maps-displaymap-par-copycoords,
		// maps-displaymap-par-static
		foreach ( $params as $name => &$param ) {
			if ( !array_key_exists( 'message', $param ) ) {
				$param['message'] = 'maps-displaymap-par-' . $name;
			}
		}

		return $params;
	}

	/**
	 * Resolves the url of images provided as wiki page; leaves others alone.
	 *
	 * @since 1.0
	 * @deprecated
	 *
	 * @param string $file
	 *
	 * @return string
	 */
	public static function getFileUrl( $file ): string {
		return MapsFactory::globalInstance()->getFileUrlFinder()->getUrlForFileName( $file );
	}

}
