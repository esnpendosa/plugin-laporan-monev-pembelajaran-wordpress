<?php
/**
 * Plugin Name: Monev Kurikulum
 * Description: Plugin WordPress khusus untuk memposting Laporan Monev Kurikulum dalam desain kartu artikel premium yang dominan biru, interaktif, dan responsif.
 * Version: 1.0.0
 * Author: muhammad as'ad muhibbin akbar
 * Text Domain: monev-kurikulum
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Constants
define( 'MONEV_KURIKULUM_VERSION', '1.0.0' );
define( 'MONEV_KURIKULUM_PATH', plugin_dir_path( __FILE__ ) );
define( 'MONEV_KURIKULUM_URL', plugin_dir_url( __FILE__ ) );

/**
 * Seed default categories on plugin activation
 */
function monev_kurikulum_activate() {
    // Register CPT & Taxonomy temporarily to seed terms
    require_once MONEV_KURIKULUM_PATH . 'includes/class-monev-cpt.php';
    $cpt = new Monev_CPT();
    $cpt->register_post_type();

    $default_terms = [
        'Fakultas Pertanian',
        'Fakultas Ekonomi dan Bisnis',
        'Fakultas Keguruan dan Ilmu Pendidikan',
        'Fakultas Agama Islam',
        'Fakultas Teknik',
        'Fakultas Psikologi',
        'Fakultas Hukum',
        'Pascasarjana',
        'Fakultas Kesehatan'
    ];

    foreach ( $default_terms as $term ) {
        if ( ! term_exists( $term, 'fakultas_kurikulum' ) ) {
            wp_insert_term( $term, 'fakultas_kurikulum' );
        }
    }
}
register_activation_hook( __FILE__, 'monev_kurikulum_activate' );

// Include necessary files
require_once MONEV_KURIKULUM_PATH . 'includes/class-monev-cpt.php';
require_once MONEV_KURIKULUM_PATH . 'includes/class-monev-shortcode.php';

// Instantiate classes
function monev_kurikulum_init() {
    new Monev_CPT();
    new Monev_Shortcode();
}
add_action( 'plugins_loaded', 'monev_kurikulum_init' );
