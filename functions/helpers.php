<?php
/**
 * Helpers
 *
 * @package K Framework
 */

if ( ! defined( 'ABSPATH' ) ) {
	die;
} // Cannot access directly.

if ( ! function_exists( 'kfw_array_search' ) ) {
	/**
	 * Array search key & value
	 *
	 * @param array  $array array.
	 * @param string $key array key.
	 * @param mixed  $value array value.
	 * @return mixed
	 */
	function kfw_array_search( $array, $key, $value ) {

		$results = array();

		if ( is_array( $array ) ) {
			if ( isset( $array[ $key ] ) && $array[ $key ] == $value ) {
				$results[] = $array;
			}

			foreach ( $array as $sub_array ) {
				$results = array_merge( $results, kfw_array_search( $sub_array, $key, $value ) );
			}
		}

		return $results;

	}
}

if ( ! function_exists( 'kfw_get_var' ) ) {

	/**
	 * Getting POST Var
	 *
	 * @param string $var var.
	 * @param string $default default.
	 * @return string
	 */
	function kfw_get_var( $var, $default = '' ) {

		if ( isset( $_POST[ $var ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			return sanitize_post( wp_unslash( $_POST[ $var ] ) ); // phpcs:ignore
		}

		if ( isset( $_GET[ $var ] ) ) {
			return sanitize_post( wp_unslash( $_GET[ $var ] ) ); // phpcs:ignore
		}

		return $default;

	}
}

if ( ! function_exists( 'kfw_allowed_html' ) ) {
	/**
	 * Allowed HTML
	 *
	 * @param string $allowed_elements .
	 * @return mixed
	 */
	function kfw_allowed_html( $allowed_elements = '' ) {

		// bail early if parameter is empty.
		if ( empty( $allowed_elements ) ) {
			return array();
		}

		$allowed_html = array();

		$allowed_tags             = wp_kses_allowed_html( 'post' );
		$default_attrs            = array(
			'aria-describedby' => true,
			'aria-details'     => true,
			'aria-label'       => true,
			'aria-labelledby'  => true,
			'aria-hidden'      => true,
			'class'            => true,
			'id'               => true,
			'style'            => true,
			'title'            => true,
			'role'             => true,
			'data-*'           => true,
		);
		$allowed_tags['input']    = array_merge(
			$default_attrs,
			array(
				'disabled'     => array(),
				'name'         => array(),
				'readonly'     => array(),
				'value'        => array(),
				'autocomplete' => array(),
				'placeholder'  => array(),
				'type'         => array(),
				'checked'      => array(),
			)
		);
		$allowed_tags['select']   = array_merge(
			$default_attrs,
			array(
				'disabled' => array(),
				'name'     => array(),
				'value'    => array(),
				'selected' => array(),
				'multiple' => array(),
			)
		);
		$allowed_tags['option']   = array_merge(
			$default_attrs,
			array(
				'name'     => array(),
				'value'    => array(),
				'selected' => array(),
			)
		);
		$allowed_tags['optgroup'] = array_merge(
			$default_attrs,
			array(
				'label' => array(),
			)
		);
		$allowed_tags['form']     = array_merge(
			$default_attrs,
			array(
				'name'         => array(),
				'method'       => array(),
				'action'       => array(),
				'enctype'      => array(),
				'autocomplete' => array(),
			)
		);

		if ( 'all' == $allowed_elements ) {
			return $allowed_tags;
		}

		foreach ( $allowed_elements as $element ) {
			$element = trim( $element );
			if ( array_key_exists( $element, $allowed_tags ) ) {
				$allowed_html[ $element ] = $allowed_tags[ $element ];
			}
		}
		return $allowed_html;
	}
}

