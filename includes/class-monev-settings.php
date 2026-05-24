<?php
/**
 * Settings class to manage the global Faculty & Study Program templates
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Monev_Settings {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_settings_page' ] );
        add_action( 'admin_init', [ $this, 'register_settings' ] );
    }

    /**
     * Add settings page under Monev Pembelajaran CPT menu
     */
    public function add_settings_page() {
        add_submenu_page(
            'edit.php?post_type=monev_pembelajaran',
            __( 'Template Fakultas & Prodi', 'monev-pembelajaran' ),
            __( 'Template Struktur', 'monev-pembelajaran' ),
            'manage_options',
            'monev-settings',
            [ $this, 'render_settings_page' ]
        );
    }

    /**
     * Register settings and sanitize callback
     */
    public function register_settings() {
        register_setting(
            'monev_settings_group',
            'monev_template_structure_text',
            [
                'type' => 'string',
                'sanitize_callback' => [ $this, 'sanitize_template_text' ],
                'default' => $this->get_default_template_text()
            ]
        );
    }

    /**
     * Get default raw template text
     */
    private function get_default_template_text() {
        return "Fakultas Pertanian | Agroteknologi, Budidaya Perikanan, Teknologi Pangan, Agribisnis\n" .
               "Fakultas Ekonomi dan Bisnis | Manajemen, Akuntansi, Kewirausahaan\n" .
               "Fakultas Keguruan dan Ilmu Pendidikan | Pendidikan Matematika, Pendidikan Bahasa Inggris, Pendidikan Guru SD, Pendidikan Profesi Guru\n" .
               "Fakultas Agama Islam | Pendidikan Agama Islam, Pendidikan Islam Anak Usia Dini\n" .
               "Fakultas Teknik | Teknik Industri, Teknik Informatika, Teknik Elektro, Teknik Konstruksi Perkapalan, Teknik Kimia, Teknik Mesin, Teknik Sipil, Sistem Informasi\n" .
               "Fakultas Psikologi | Psikologi\n" .
               "Fakultas Hukum | Hukum\n" .
               "Pascasarjana | S2 Manajemen, S2 Pendidikan Bahasa Inggris\n" .
               "Fakultas Kesehatan | Ilmu Gizi, Kesehatan Masyarakat, Ilmu Keperawatan, Farmasi, Profesi Ners, Fisioterapi, Teknologi Laboratorium Medis, S1 Kebidanan";
    }

    /**
     * Sanitize settings text and generate the array template option
     */
    public function sanitize_template_text( $text ) {
        $lines = explode( "\n", str_replace( "\r", "", $text ) );
        $structure = [];

        foreach ( $lines as $line ) {
            $line = trim( $line );
            if ( empty( $line ) ) {
                continue;
            }

            $parts = explode( '|', $line );
            $faculty_name = trim( $parts[0] );
            
            if ( empty( $faculty_name ) ) {
                continue;
            }

            $items = [];
            if ( isset( $parts[1] ) && ! empty( trim( $parts[1] ) ) ) {
                $prodis = explode( ',', $parts[1] );
                foreach ( $prodis as $prodi ) {
                    $prodi = trim( $prodi );
                    if ( ! empty( $prodi ) ) {
                        $items[] = [
                            'id'   => sanitize_title( $prodi ),
                            'name' => $prodi
                        ];
                    }
                }
            }

            $structure[] = [
                'id'    => sanitize_title( $faculty_name ),
                'name'  => $faculty_name,
                'items' => $items
            ];
        }

        // Save the parsed structure in a separate option for easy read
        update_option( 'monev_template_structure', $structure );

        return $text;
    }

    /**
     * Render admin settings page
     */
    public function render_settings_page() {
        // Enqueue media library
        wp_enqueue_media();

        // Get saved settings or default
        $template_text = get_option( 'monev_template_structure_text' );
        if ( ! $template_text ) {
            $template_text = $this->get_default_template_text();
            update_option( 'monev_template_structure_text', $template_text );
        }

        $parsed_structure = get_option( 'monev_template_structure', [] );
        ?>
        <div class="wrap monev-admin-wrap">
            <h1 class="wp-heading-inline" style="display:none;"></h1> <!-- Hide default header -->
            
            <div class="monev-settings-header">
                <div class="monev-header-content">
                    <h2><span class="dashicons dashicons-admin-settings"></span> <?php _e( 'Konfigurasi Template Monev Pembelajaran', 'monev-pembelajaran' ); ?></h2>
                    <p><?php _e( 'Kelola struktur default Fakultas dan Program Studi di bawah ini. Struktur ini akan digunakan sebagai form bawaan saat Anda membuat postingan Monev baru.', 'monev-pembelajaran' ); ?></p>
                </div>
            </div>

            <div class="monev-settings-grid">
                <!-- Left Column: Form Editor -->
                <div class="monev-card monev-editor-card">
                    <div class="monev-card-header">
                        <h3><?php _e( 'Struktur Fakultas & Program Studi', 'monev-pembelajaran' ); ?></h3>
                    </div>
                    <div class="monev-card-body">
                        <form method="post" action="options.php">
                            <?php settings_fields( 'monev_settings_group' ); ?>
                            
                            <div class="monev-form-group">
                                <label for="monev_template_structure_text">
                                    <strong><?php _e( 'Format Teks Struktur Template:', 'monev-pembelajaran' ); ?></strong>
                                </label>
                                <p class="description">
                                    <?php _e( 'Tulis 1 baris per Fakultas dengan format: <code>Nama Fakultas | Prodi A, Prodi B, Prodi C</code>', 'monev-pembelajaran' ); ?>
                                </p>
                                <textarea 
                                    name="monev_template_structure_text" 
                                    id="monev_template_structure_text" 
                                    rows="12" 
                                    class="large-text code" 
                                    style="font-family: Consolas, Monaco, monospace; font-size: 13px; line-height: 1.5; padding: 12px; border-radius: 6px; border: 1px solid #ccd0d4;"
                                ><?php echo esc_textarea( $template_text ); ?></textarea>
                            </div>

                            <div class="monev-form-actions">
                                <?php submit_button( __( 'Simpan Perubahan', 'monev-pembelajaran' ), 'primary button-hero' ); ?>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Right Column: Visual Preview -->
                <div class="monev-card monev-preview-card">
                    <div class="monev-card-header">
                        <h3><?php _e( 'Visualisasi Hasil Parser', 'monev-pembelajaran' ); ?></h3>
                    </div>
                    <div class="monev-card-body">
                        <?php if ( empty( $parsed_structure ) ) : ?>
                            <div class="monev-alert monev-alert-warning">
                                <p><?php _e( 'Belum ada data template yang dikonfigurasi.', 'monev-pembelajaran' ); ?></p>
                            </div>
                        <?php else : ?>
                            <div class="monev-visual-hierarchy">
                                <?php foreach ( $parsed_structure as $faculty ) : ?>
                                    <div class="monev-preview-faculty">
                                        <div class="monev-faculty-title">
                                            <span class="dashicons dashicons-category"></span> 
                                            <strong><?php echo esc_html( $faculty['name'] ); ?></strong>
                                            <span class="monev-badge-count"><?php echo count( $faculty['items'] ); ?> Prodi</span>
                                        </div>
                                        <?php if ( ! empty( $faculty['items'] ) ) : ?>
                                            <ul class="monev-preview-prodi-list">
                                                <?php foreach ( $faculty['items'] as $prodi ) : ?>
                                                    <li>
                                                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                                                        <?php echo esc_html( $prodi['name'] ); ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php else : ?>
                                            <p class="monev-no-prodi"><?php _e( '(Tidak ada program studi)', 'monev-pembelajaran' ); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
