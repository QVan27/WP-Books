<?php

/**
 * Plugin Name: WP Books
 * Description: Affiche une sélection de livres provenant de l'API Gutendex.
 * Version: 1.0.0
 * Author: Quentin Vannarath
 * Author URI: https://artvannah.fr
 * Text Domain: wp-books
 * Requires at least: 6.0
 * Requires PHP: 8.1
 */

defined('ABSPATH') || exit;

define('WP_BOOKS_VERSION', '1.0.0');
define('WP_BOOKS_FILE', __FILE__);
define('WP_BOOKS_DIR', plugin_dir_path(__FILE__));
define('WP_BOOKS_URL', plugin_dir_url(__FILE__));

require_once WP_BOOKS_DIR . 'includes/class-wp-books-api.php';
require_once WP_BOOKS_DIR . 'includes/class-wp-books-cache.php';
require_once WP_BOOKS_DIR . 'includes/class-wp-books-renderer.php';
require_once WP_BOOKS_DIR . 'includes/class-wp-books-block.php';
require_once WP_BOOKS_DIR . 'includes/class-wp-books-admin.php';

/**
 * Initialise le plugin.
 *
 * @return void
 */
function wp_books_init()
{
  $cache = new WP_Books_Cache();
  $api = new WP_Books_API($cache);
  $renderer = new WP_Books_Renderer($api);

  new WP_Books_Block($renderer);
  new WP_Books_Admin($api, $cache);

  add_shortcode('books_list', array($renderer, 'render_shortcode'));
}

add_action('plugins_loaded', 'wp_books_init');

/**
 * Charge les assets front du plugin.
 *
 * @return void
 */
function wp_books_enqueue_assets()
{
  wp_enqueue_style(
    'wp-books-list',
    WP_BOOKS_URL . 'assets/css/books-list.css',
    array(),
    WP_BOOKS_VERSION
  );
}

add_action('wp_enqueue_scripts', 'wp_books_enqueue_assets');
