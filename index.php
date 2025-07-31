<?php

namespace PWNewsletterForm;

/**
 * Plugin Name: PW Newsletter Form
 * Description: Newsletter form plugin connected to Brevo
 * Version: 0.0.4
 * Tested up to: 6.8
 * Requires PHP: 8.1
 * Author: Patrick Faramaz
 * Author URI: https://wp-performance.com
 * Text Domain: pw-newsletter-form
 * GitHub Plugin URI: https://github.com/wp-performance/pw-newsletter-form
 *
 * Copyright 2025 Patrick Faramaz
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2, as
 * published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 **/

if (!defined('ABSPATH')) {
  exit; // Exit if accessed directly.
}

require_once dirname(__FILE__) . '/inc/rest.php';
require_once dirname(__FILE__) . '/inc/token.php';
require_once dirname(__FILE__) . '/inc/settings_page.php';

/**
 * Load the plugin text domain for translation.
 */ function load_textdomain(): void
{
  load_plugin_textdomain(
    'pw-newsletter-form',
    false,
    basename(dirname(__FILE__)) . '/languages'
  );
}

/**
 * load translations
 */
function set_script_translations(): void
{
  wp_set_script_translations('pw-newsletter-form', 'pw-newsletter-form', plugin_dir_path(__FILE__) . 'languages');
}

add_action('init', __NAMESPACE__ . '\load_textdomain');
add_action('init', __NAMESPACE__ . '\set_script_translations');

// options page
new SettingsPage;

// register rest api
/**
 * add api endpoint
 */
add_action(
  'rest_api_init',
  function () {
    register_rest_route('pw_newsletter_form', '/action', [
      'methods' => 'POST',
      'permission_callback' => '__return_true',
      'callback' => __NAMESPACE__ . '\form_callback',
    ]);
  }
);

/**
 * add api endpoint
 */
add_action(
  'rest_api_init',
  function () {
    register_rest_route('pw_newsletter_form', '/getToken', [
      'methods' => 'GET',
      'permission_callback' => '__return_true',
      'callback' => __NAMESPACE__ . '\get_token',
    ]);
  }
);


function pw_newsletter_form_blocks_block_init()
{
  if (function_exists('wp_register_block_types_from_metadata_collection')) { // Function introduced in WordPress 6.8.
    wp_register_block_types_from_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
  } else if (file_exists(__DIR__ . '/build/blocks-manifest.php')) {
    if (function_exists('wp_register_block_metadata_collection')) { // Function introduced in WordPress 6.7.
      wp_register_block_metadata_collection(__DIR__ . '/build', __DIR__ . '/build/blocks-manifest.php');
    }
    $manifest_data = require __DIR__ . '/build/blocks-manifest.php';
    foreach (array_keys($manifest_data) as $block_type) {
      register_block_type(__DIR__ . "/build/{$block_type}");
    }
  }
}

add_action('init', __NAMESPACE__ . '\pw_newsletter_form_blocks_block_init');
