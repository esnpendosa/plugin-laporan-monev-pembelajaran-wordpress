<?php
/**
 * Class to register Custom Post Type, Taxonomy, and Meta Box for Monev Kurikulum
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Monev_CPT {

    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'init', [ $this, 'register_taxonomy' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_monev_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_monev_meta_data' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Register CPT: monev_kurikulum
     */
    public function register_post_type() {
        $labels = [
            'name'                  => _x( 'Monev Kurikulum', 'Post type general name', 'monev-kurikulum' ),
            'singular_name'         => _x( 'Monev Kurikulum', 'Post type singular name', 'monev-kurikulum' ),
            'menu_name'             => _x( 'Monev Kurikulum', 'Admin Menu text', 'monev-kurikulum' ),
            'name_admin_bar'        => _x( 'Monev Kurikulum', 'Add New on Toolbar', 'monev-kurikulum' ),
            'add_new'               => __( 'Tambah Laporan', 'monev-kurikulum' ),
            'add_new_item'          => __( 'Tambah Laporan Baru', 'monev-kurikulum' ),
            'new_item'              => __( 'Laporan Baru', 'monev-kurikulum' ),
            'edit_item'             => __( 'Edit Laporan', 'monev-kurikulum' ),
            'view_item'             => __( 'Lihat Laporan', 'monev-kurikulum' ),
            'all_items'             => __( 'Semua Laporan', 'monev-kurikulum' ),
            'search_items'          => __( 'Cari Laporan', 'monev-kurikulum' ),
            'not_found'             => __( 'Tidak ada Laporan ditemukan.', 'monev-kurikulum' ),
            'not_found_in_trash'    => __( 'Tidak ada Laporan di Sampah.', 'monev-kurikulum' )
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'monev-kurikulum' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-welcome-write-blog', // Blog/Article style icon
            'supports'           => [ 'title', 'editor', 'thumbnail' ], // Support title, content (excerpt), and featured image
            'show_in_rest'       => true // Enable Gutenberg block editor support!
        ];

        register_post_type( 'monev_kurikulum', $args );
    }

    /**
     * Register Taxonomy: fakultas_kurikulum (Categories)
     */
    public function register_taxonomy() {
        $labels = [
            'name'              => _x( 'Fakultas / Prodi', 'taxonomy general name', 'monev-kurikulum' ),
            'singular_name'     => _x( 'Fakultas / Prodi', 'taxonomy singular name', 'monev-kurikulum' ),
            'search_items'      => __( 'Cari Fakultas/Prodi', 'monev-kurikulum' ),
            'all_items'         => __( 'Semua Fakultas/Prodi', 'monev-kurikulum' ),
            'parent_item'       => __( 'Parent Fakultas/Prodi', 'monev-kurikulum' ),
            'parent_item_colon' => __( 'Parent Fakultas/Prodi:', 'monev-kurikulum' ),
            'edit_item'         => __( 'Edit Fakultas/Prodi', 'monev-kurikulum' ),
            'update_item'       => __( 'Update Fakultas/Prodi', 'monev-kurikulum' ),
            'add_new_item'      => __( 'Tambah Kategori Baru', 'monev-kurikulum' ),
            'new_item_name'     => __( 'Nama Kategori Baru', 'monev-kurikulum' ),
            'menu_name'         => __( 'Kategori (Fakultas/Prodi)', 'monev-kurikulum' ),
        ];

        $args = [
            'hierarchical'      => true, // Like standard Categories
            'labels'            => $labels,
            'show_ui'           => true,
            'show_admin_column' => true, // Show category in CPT table column!
            'query_var'         => true,
            'rewrite'           => [ 'slug' => 'fakultas-kurikulum' ],
            'show_in_rest'      => true // Block Editor support
        ];

        register_taxonomy( 'fakultas_kurikulum', [ 'monev_kurikulum' ], $args );
    }

    /**
     * Add Meta Box to monev_kurikulum CPT
     */
    public function add_monev_meta_box() {
        add_meta_box(
            'monev_kurikulum_details',
            __( 'Informasi Tambahan & Berkas Laporan', 'monev-kurikulum' ),
            [ $this, 'render_meta_box_content' ],
            'monev_kurikulum',
            'normal',
            'high'
        );
    }

    /**
     * Enqueue Admin JS & CSS
     */
    public function enqueue_admin_assets( $hook ) {
        global $post;

        $is_post_editor = ( $hook === 'post.php' || $hook === 'post-new.php' ) && isset( $post ) && $post->post_type === 'monev_kurikulum';

        if ( $is_post_editor ) {
            wp_enqueue_media();

            wp_enqueue_style(
                'monev-admin-style',
                MONEV_KURIKULUM_URL . 'assets/css/admin.css',
                [],
                MONEV_KURIKULUM_VERSION
            );

            wp_enqueue_script(
                'monev-admin-js',
                MONEV_KURIKULUM_URL . 'assets/js/admin.js',
                [ 'jquery' ],
                MONEV_KURIKULUM_VERSION,
                true
            );
        }
    }

    /**
     * Render the Meta Box HTML content
     */
    public function render_meta_box_content( $post ) {
        wp_nonce_field( 'save_monev_kurikulum_action', 'monev_kurikulum_nonce' );

        // Load saved values
        $file_id = get_post_meta( $post->ID, '_monev_pdf_id', true );
        $file_name = get_post_meta( $post->ID, '_monev_pdf_name', true );
        $tahun_akademik = get_post_meta( $post->ID, '_monev_tahun_akademik', true );
        $semester = get_post_meta( $post->ID, '_monev_semester', true );
        $custom_link = get_post_meta( $post->ID, '_monev_custom_link', true );
        ?>
        <div class="monev-meta-box-container monev-kurikulum-meta">
            <p class="monev-intro">
                <?php _e( 'Tambahkan berkas PDF laporan Monev Kurikulum dan isi detail semester terkait untuk ditampilkan pada kartu artikel.', 'monev-kurikulum' ); ?>
            </p>

            <div class="monev-admin-grid">
                <!-- File Upload -->
                <div class="monev-admin-field">
                    <label for="monev_pdf_id"><strong><?php _e( 'Unggah Laporan PDF:', 'monev-kurikulum' ); ?></strong></label>
                    <div class="monev-media-uploader-wrapper" style="margin-top: 8px;">
                        <input type="hidden" name="monev_pdf_id" class="monev-file-id" value="<?php echo esc_attr( $file_id ); ?>">
                        <input type="hidden" name="monev_pdf_name" class="monev-file-name" value="<?php echo esc_attr( $file_name ); ?>">
                        
                        <span class="monev-file-badge <?php echo $file_id ? 'has-file' : 'no-file'; ?>">
                            <span class="dashicons dashicons-media-document"></span>
                            <span class="monev-file-badge-text"><?php echo $file_id ? esc_html( $file_name ) : __( 'Tidak ada berkas PDF', 'monev-kurikulum' ); ?></span>
                        </span>
                        
                        <button type="button" class="button monev-upload-btn"><?php _e( 'Pilih Berkas PDF', 'monev-kurikulum' ); ?></button>
                        <button type="button" class="button monev-clear-btn" style="<?php echo $file_id ? '' : 'display:none;'; ?>">&times;</button>
                    </div>
                </div>

                <!-- Custom Link -->
                <div class="monev-admin-field">
                    <label for="monev_custom_link"><strong><?php _e( 'Tautan Unduhan Alternatif (Opsional):', 'monev-kurikulum' ); ?></strong></label>
                    <p class="description"><?php _e( 'Gunakan jika dokumen disimpan di Google Drive/Dropbox.', 'monev-kurikulum' ); ?></p>
                    <input type="url" name="monev_custom_link" id="monev_custom_link" class="regular-text" style="width: 100%; margin-top: 8px;" placeholder="https://" value="<?php echo esc_url( $custom_link ); ?>">
                </div>

                <!-- Tahun Akademik -->
                <div class="monev-admin-field">
                    <label for="monev_tahun_akademik"><strong><?php _e( 'Tahun Akademik:', 'monev-kurikulum' ); ?></strong></label>
                    <p class="description"><?php _e( 'Contoh: 2024/2025', 'monev-kurikulum' ); ?></p>
                    <input type="text" name="monev_tahun_akademik" id="monev_tahun_akademik" class="regular-text" style="width: 100%; margin-top: 8px;" placeholder="e.g. 2024/2025" value="<?php echo esc_attr( $tahun_akademik ); ?>">
                </div>

                <!-- Semester Select -->
                <div class="monev-admin-field">
                    <label for="monev_semester"><strong><?php _e( 'Semester:', 'monev-kurikulum' ); ?></strong></label>
                    <p class="description"><?php _e( 'Pilih periode semester laporan.', 'monev-kurikulum' ); ?></p>
                    <select name="monev_semester" id="monev_semester" style="width: 100%; margin-top: 8px; height: 35px; border-radius: 4px; border: 1px solid #cbd5e1;">
                        <option value="Ganjil" <?php selected( $semester, 'Ganjil' ); ?>><?php _e( 'Ganjil', 'monev-kurikulum' ); ?></option>
                        <option value="Genap" <?php selected( $semester, 'Genap' ); ?>><?php _e( 'Genap', 'monev-kurikulum' ); ?></option>
                        <option value="Lainnya" <?php selected( $semester, 'Lainnya' ); ?>><?php _e( 'Lainnya / Umum', 'monev-kurikulum' ); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * Save the Meta Box fields
     */
    public function save_monev_meta_data( $post_id ) {
        if ( ! isset( $_POST['monev_kurikulum_nonce'] ) || ! wp_verify_nonce( $_POST['monev_kurikulum_nonce'], 'save_monev_kurikulum_action' ) ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Save inputs
        if ( isset( $_POST['monev_pdf_id'] ) ) {
            update_post_meta( $post_id, '_monev_pdf_id', sanitize_text_field( $_POST['monev_pdf_id'] ) );
        }
        if ( isset( $_POST['monev_pdf_name'] ) ) {
            update_post_meta( $post_id, '_monev_pdf_name', sanitize_text_field( $_POST['monev_pdf_name'] ) );
        }
        if ( isset( $_POST['monev_custom_link'] ) ) {
            update_post_meta( $post_id, '_monev_custom_link', esc_url_raw( $_POST['monev_custom_link'] ) );
        }
        if ( isset( $_POST['monev_tahun_akademik'] ) ) {
            update_post_meta( $post_id, '_monev_tahun_akademik', sanitize_text_field( $_POST['monev_tahun_akademik'] ) );
        }
        if ( isset( $_POST['monev_semester'] ) ) {
            update_post_meta( $post_id, '_monev_semester', sanitize_text_field( $_POST['monev_semester'] ) );
        }
    }
}
