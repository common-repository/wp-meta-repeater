<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 *
 * @package    wpmetarepeater
 * @subpackage wpmetarepeater/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    wpmetarepeater
 * @subpackage wpmetarepeater/includes

 */
class Wpmetarepeater_i18n {

	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wpmetarepeater',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
