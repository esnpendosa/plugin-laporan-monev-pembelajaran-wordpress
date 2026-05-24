<?php
/**
 * Plugin Name: Monev Pembelajaran
 * Description: Plugin WordPress khusus untuk memposting data dan file Monitoring dan Evaluasi (Monev) Pembelajaran per-Semester & per-Tahun dengan desain dominan biru yang responsif.
 * Version: 1.0.0
 * Author: muhammad as'ad muhibbin akbar
 * Text Domain: monev-pembelajaran
 * Domain Path: /languages
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Define Constants
define( 'MONEV_PEMBELAJARAN_VERSION', '1.0.0' );
define( 'MONEV_PEMBELAJARAN_PATH', plugin_dir_path( __FILE__ ) );
define( 'MONEV_PEMBELAJARAN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Seed default template on plugin activation
 */
function monev_pembelajaran_activate() {
    $default_template = [
        [
            'id' => 'fakultas_pertanian',
            'name' => 'Fakultas Pertanian',
            'items' => [
                ['id' => 'agroteknologi', 'name' => 'Agroteknologi'],
                ['id' => 'budidaya_perikanan', 'name' => 'Budidaya Perikanan'],
                ['id' => 'teknologi_pangan', 'name' => 'Teknologi Pangan'],
                ['id' => 'agribisnis', 'name' => 'Agribisnis'],
            ]
        ],
        [
            'id' => 'fakultas_ekonomi_dan_bisnis',
            'name' => 'Fakultas Ekonomi dan Bisnis',
            'items' => [
                ['id' => 'manajemen', 'name' => 'Manajemen'],
                ['id' => 'akuntansi', 'name' => 'Akuntansi'],
                ['id' => 'kewirausahaan', 'name' => 'Kewirausahaan'],
            ]
        ],
        [
            'id' => 'fakultas_keguruan_dan_ilmu_pendidikan',
            'name' => 'Fakultas Keguruan dan Ilmu Pendidikan',
            'items' => [
                ['id' => 'pendidikan_matematika', 'name' => 'Pendidikan Matematika'],
                ['id' => 'pendidikan_bahasa_inggris', 'name' => 'Pendidikan Bahasa Inggris'],
                ['id' => 'pendidikan_guru_sd', 'name' => 'Pendidikan Guru SD'],
                ['id' => 'pendidikan_profesi_guru', 'name' => 'Pendidikan Profesi Guru'],
            ]
        ],
        [
            'id' => 'fakultas_agama_islam',
            'name' => 'Fakultas Agama Islam',
            'items' => [
                ['id' => 'pendidikan_agama_islam', 'name' => 'Pendidikan Agama Islam'],
                ['id' => 'pendidikan_islam_anak_usia_dini', 'name' => 'Pendidikan Islam Anak Usia Dini'],
            ]
        ],
        [
            'id' => 'fakultas_teknik',
            'name' => 'Fakultas Teknik',
            'items' => [
                ['id' => 'teknik_industri', 'name' => 'Teknik Industri'],
                ['id' => 'teknik_informatika', 'name' => 'Teknik Informatika'],
                ['id' => 'teknik_elektro', 'name' => 'Teknik Elektro'],
                ['id' => 'teknik_konstruksi_perkapalan', 'name' => 'Teknik Konstruksi Perkapalan'],
                ['id' => 'teknik_kimia', 'name' => 'Teknik Kimia'],
                ['id' => 'teknik_mesin', 'name' => 'Teknik Mesin'],
                ['id' => 'teknik_sipil', 'name' => 'Teknik Sipil'],
                ['id' => 'sistem_informasi', 'name' => 'Sistem Informasi'],
            ]
        ],
        [
            'id' => 'fakultas_psikologi',
            'name' => 'Fakultas Psikologi',
            'items' => [
                ['id' => 'psikologi', 'name' => 'Psikologi'],
            ]
        ],
        [
            'id' => 'fakultas_hukum',
            'name' => 'Fakultas Hukum',
            'items' => [
                ['id' => 'hukum', 'name' => 'Hukum'],
            ]
        ],
        [
            'id' => 'pascasarjana',
            'name' => 'Pascasarjana',
            'items' => [
                ['id' => 's2_manajemen', 'name' => 'S2 Manajemen'],
                ['id' => 's2_pendidikan_bahasa_inggris', 'name' => 'S2 Pendidikan Bahasa Inggris'],
            ]
        ],
        [
            'id' => 'fakultas_kesehatan',
            'name' => 'Fakultas Kesehatan',
            'items' => [
                ['id' => 'ilmu_gizi', 'name' => 'Ilmu Gizi'],
                ['id' => 'kesehatan_masyarakat', 'name' => 'Kesehatan Masyarakat'],
                ['id' => 'ilmu_keperawatan', 'name' => 'Ilmu Keperawatan'],
                ['id' => 'farmasi', 'name' => 'Farmasi'],
                ['id' => 'profesi_ners', 'name' => 'Profesi Ners'],
                ['id' => 'fisioterapi', 'name' => 'Fisioterapi'],
                ['id' => 'teknologi_laboratorium_medis', 'name' => 'Teknologi Laboratorium Medis'],
                ['id' => 's1_kebidanan', 'name' => 'S1 Kebidanan'],
            ]
        ]
    ];

    // Seed default template structure if it doesn't exist
    if ( ! get_option( 'monev_template_structure' ) ) {
        update_option( 'monev_template_structure', $default_template );
    }
}
register_activation_hook( __FILE__, 'monev_pembelajaran_activate' );

// Include necessary files
require_once MONEV_PEMBELAJARAN_PATH . 'includes/class-monev-settings.php';
require_once MONEV_PEMBELAJARAN_PATH . 'includes/class-monev-cpt.php';
require_once MONEV_PEMBELAJARAN_PATH . 'includes/class-monev-shortcode.php';

// Instantiate classes
function monev_pembelajaran_init() {
    new Monev_Settings();
    new Monev_CPT();
    new Monev_Shortcode();
}
add_action( 'plugins_loaded', 'monev_pembelajaran_init' );
