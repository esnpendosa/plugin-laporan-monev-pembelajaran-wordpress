<?php
/**
 * Class to register Custom Post Type and Meta Box for Monev Pembelajaran
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Monev_CPT {

    public function __construct() {
        add_action( 'init', [ $this, 'register_post_type' ] );
        add_action( 'add_meta_boxes', [ $this, 'add_monev_meta_box' ] );
        add_action( 'save_post', [ $this, 'save_monev_meta_data' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
    }

    /**
     * Register CPT: monev_pembelajaran
     */
    public function register_post_type() {
        $labels = [
            'name'                  => _x( 'Monev Pembelajaran', 'Post type general name', 'monev-pembelajaran' ),
            'singular_name'         => _x( 'Monev Pembelajaran', 'Post type singular name', 'monev-pembelajaran' ),
            'menu_name'             => _x( 'Monev Pembelajaran', 'Admin Menu text', 'monev-pembelajaran' ),
            'name_admin_bar'        => _x( 'Monev Pembelajaran', 'Add New on Toolbar', 'monev-pembelajaran' ),
            'add_new'               => __( 'Tambah Baru', 'monev-pembelajaran' ),
            'add_new_item'          => __( 'Tambah Monev Baru', 'monev-pembelajaran' ),
            'new_item'              => __( 'Monev Baru', 'monev-pembelajaran' ),
            'edit_item'             => __( 'Edit Monev', 'monev-pembelajaran' ),
            'view_item'             => __( 'Lihat Monev', 'monev-pembelajaran' ),
            'all_items'             => __( 'Semua Monev', 'monev-pembelajaran' ),
            'search_items'          => __( 'Cari Monev', 'monev-pembelajaran' ),
            'not_found'             => __( 'Tidak ada Monev ditemukan.', 'monev-pembelajaran' ),
            'not_found_in_trash'    => __( 'Tidak ada Monev di Sampah.', 'monev-pembelajaran' )
        ];

        $args = [
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => [ 'slug' => 'monev-pembelajaran' ],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => 20,
            'menu_icon'          => 'dashicons-welcome-learn-more',
            'supports'           => [ 'title' ], // We only need the title (Semester/TA)
        ];

        register_post_type( 'monev_pembelajaran', $args );
    }

    /**
     * Add Meta Box to monev_pembelajaran CPT
     */
    public function add_monev_meta_box() {
        add_meta_box(
            'monev_detail_meta_box',
            __( 'Detail File & Status Monev', 'monev-pembelajaran' ),
            [ $this, 'render_meta_box_content' ],
            'monev_pembelajaran',
            'normal',
            'high'
        );
    }

    /**
     * Enqueue Admin JS & CSS
     */
    public function enqueue_admin_assets( $hook ) {
        global $post;

        // Load only on monev_pembelajaran post editor screen and settings screen
        $is_settings_page = ( isset($_GET['page']) && $_GET['page'] === 'monev-settings' );
        $is_post_editor = ( $hook === 'post.php' || $hook === 'post-new.php' ) && isset( $post ) && $post->post_type === 'monev_pembelajaran';

        if ( $is_post_editor || $is_settings_page ) {
            wp_enqueue_media();

            // Admin CSS
            wp_enqueue_style(
                'monev-admin-style',
                MONEV_PEMBELAJARAN_URL . 'assets/css/admin.css',
                [],
                MONEV_PEMBELAJARAN_VERSION
            );

            // Admin JS
            wp_enqueue_script(
                'monev-admin-js',
                MONEV_PEMBELAJARAN_URL . 'assets/js/admin.js',
                [ 'jquery' ],
                MONEV_PEMBELAJARAN_VERSION,
                true
            );
        }
    }

    /**
     * Render the Meta Box HTML content
     */
    public function render_meta_box_content( $post ) {
        // Add Nonce field for security
        wp_nonce_field( 'save_monev_meta_action', 'monev_meta_nonce' );

        // Load template structure
        $template = get_option( 'monev_template_structure', [] );
        
        // Load saved values
        $saved_values = get_post_meta( $post->ID, '_monev_data_values', true );
        if ( ! is_array( $saved_values ) ) {
            $saved_values = [];
        }

        if ( empty( $template ) ) {
            echo '<div class="monev-admin-alert alert-warning">';
            printf(
                __( 'Template belum dibuat. Silakan atur template struktur terlebih dahulu di menu <a href="%s">Template Struktur</a>.', 'monev-pembelajaran' ),
                admin_url( 'edit.php?post_type=monev_pembelajaran&page=monev-settings' )
            );
            echo '</div>';
            return;
        }
        ?>
        <div class="monev-meta-box-container">
            <p class="monev-intro">
                <?php _e( 'Isi detail unduhan file atau status perkuliahan untuk masing-masing Fakultas dan Program Studi di bawah ini.', 'monev-pembelajaran' ); ?>
            </p>

            <table class="wp-list-table widefat fixed striped table-view-list monev-builder-table">
                <thead>
                    <tr>
                        <th class="column-title"><?php _e( 'Fakultas / Program Studi', 'monev-pembelajaran' ); ?></th>
                        <th class="column-file"><?php _e( 'File Upload (Media Library)', 'monev-pembelajaran' ); ?></th>
                        <th class="column-link"><?php _e( 'Link Kustom / URL Alternatif', 'monev-pembelajaran' ); ?></th>
                        <th class="column-text"><?php _e( 'Status Kustom (misal: "Perkuliahan Selesai")', 'monev-pembelajaran' ); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    foreach ( $template as $faculty ) : 
                        $fac_id = $faculty['id'];
                        $fac_val = isset( $saved_values[ $fac_id ] ) ? $saved_values[ $fac_id ] : [];
                        $fac_file_id = isset( $fac_val['file_id'] ) ? $fac_val['file_id'] : '';
                        $fac_file_name = isset( $fac_val['file_name'] ) ? $fac_val['file_name'] : '';
                        $fac_custom_link = isset( $fac_val['custom_link'] ) ? $fac_val['custom_link'] : '';
                        $fac_custom_text = isset( $fac_val['custom_text'] ) ? $fac_val['custom_text'] : '';
                    ?>
                        <!-- Faculty Row -->
                        <tr class="monev-row-faculty" data-id="<?php echo esc_attr( $fac_id ); ?>">
                            <td class="monev-cell-title">
                                <span class="dashicons dashicons-category"></span>
                                <strong><?php echo esc_html( $faculty['name'] ); ?></strong>
                            </td>
                            <td class="monev-cell-file">
                                <div class="monev-media-uploader-wrapper">
                                    <input type="hidden" name="monev_data[<?php echo esc_attr( $fac_id ); ?>][file_id]" class="monev-file-id" value="<?php echo esc_attr( $fac_file_id ); ?>">
                                    <input type="hidden" name="monev_data[<?php echo esc_attr( $fac_id ); ?>][file_name]" class="monev-file-name" value="<?php echo esc_attr( $fac_file_name ); ?>">
                                    
                                    <span class="monev-file-badge <?php echo $fac_file_id ? 'has-file' : 'no-file'; ?>">
                                        <span class="dashicons dashicons-media-document"></span>
                                        <span class="monev-file-badge-text"><?php echo $fac_file_id ? esc_html( $fac_file_name ) : __( 'Tidak ada file', 'monev-pembelajaran' ); ?></span>
                                    </span>
                                    
                                    <button type="button" class="button button-small monev-upload-btn"><?php _e( 'Pilih', 'monev-pembelajaran' ); ?></button>
                                    <button type="button" class="button button-small monev-clear-btn" style="<?php echo $fac_file_id ? '' : 'display:none;'; ?>">&times;</button>
                                </div>
                            </td>
                            <td class="monev-cell-link">
                                <input type="url" name="monev_data[<?php echo esc_attr( $fac_id ); ?>][custom_link]" class="regular-text" placeholder="https://" value="<?php echo esc_url( $fac_custom_link ); ?>">
                            </td>
                            <td class="monev-cell-text">
                                <input type="text" name="monev_data[<?php echo esc_attr( $fac_id ); ?>][custom_text]" class="regular-text" placeholder="<?php _e( 'Contoh: Unduh / Perkuliahan Selesai', 'monev-pembelajaran' ); ?>" value="<?php echo esc_attr( $fac_custom_text ); ?>">
                            </td>
                        </tr>

                        <!-- Study Programs Rows -->
                        <?php if ( ! empty( $faculty['items'] ) ) : ?>
                            <?php 
                            foreach ( $faculty['items'] as $prodi ) : 
                                $prodi_id = $prodi['id'];
                                $prodi_val = isset( $saved_values[ $prodi_id ] ) ? $saved_values[ $prodi_id ] : [];
                                $prodi_file_id = isset( $prodi_val['file_id'] ) ? $prodi_val['file_id'] : '';
                                $prodi_file_name = isset( $prodi_val['file_name'] ) ? $prodi_val['file_name'] : '';
                                $prodi_custom_link = isset( $prodi_val['custom_link'] ) ? $prodi_val['custom_link'] : '';
                                $prodi_custom_text = isset( $prodi_val['custom_text'] ) ? $prodi_val['custom_text'] : '';
                            ?>
                                <tr class="monev-row-prodi" data-id="<?php echo esc_attr( $prodi_id ); ?>">
                                    <td class="monev-cell-title">
                                        <span class="monev-indent-line"></span>
                                        <span class="dashicons dashicons-arrow-right-alt2"></span>
                                        <span><?php echo esc_html( $prodi['name'] ); ?></span>
                                    </td>
                                    <td class="monev-cell-file">
                                        <div class="monev-media-uploader-wrapper">
                                            <input type="hidden" name="monev_data[<?php echo esc_attr( $prodi_id ); ?>][file_id]" class="monev-file-id" value="<?php echo esc_attr( $prodi_file_id ); ?>">
                                            <input type="hidden" name="monev_data[<?php echo esc_attr( $prodi_id ); ?>][file_name]" class="monev-file-name" value="<?php echo esc_attr( $prodi_file_name ); ?>">
                                            
                                            <span class="monev-file-badge <?php echo $prodi_file_id ? 'has-file' : 'no-file'; ?>">
                                                <span class="dashicons dashicons-media-document"></span>
                                                <span class="monev-file-badge-text"><?php echo $prodi_file_id ? esc_html( $prodi_file_name ) : __( 'Tidak ada file', 'monev-pembelajaran' ); ?></span>
                                            </span>
                                            
                                            <button type="button" class="button button-small monev-upload-btn"><?php _e( 'Pilih', 'monev-pembelajaran' ); ?></button>
                                            <button type="button" class="button button-small monev-clear-btn" style="<?php echo $prodi_file_id ? '' : 'display:none;'; ?>">&times;</button>
                                        </div>
                                    </td>
                                    <td class="monev-cell-link">
                                        <input type="url" name="monev_data[<?php echo esc_attr( $prodi_id ); ?>][custom_link]" class="regular-text" placeholder="https://" value="<?php echo esc_url( $prodi_custom_link ); ?>">
                                    </td>
                                    <td class="monev-cell-text">
                                        <input type="text" name="monev_data[<?php echo esc_attr( $prodi_id ); ?>][custom_text]" class="regular-text" placeholder="<?php _e( 'Contoh: Unduh / Perkuliahan Selesai', 'monev-pembelajaran' ); ?>" value="<?php echo esc_attr( $prodi_custom_text ); ?>">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>

                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
    }

    /**
     * Save the Meta Box fields
     */
    public function save_monev_meta_data( $post_id ) {
        // Nonce check
        if ( ! isset( $_POST['monev_meta_nonce'] ) || ! wp_verify_nonce( $_POST['monev_meta_nonce'], 'save_monev_meta_action' ) ) {
            return;
        }

        // Autosave check
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Permissions check
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }

        // Check if our CPT data is submitted
        if ( isset( $_POST['monev_data'] ) && is_array( $_POST['monev_data'] ) ) {
            $sanitized_data = [];

            foreach ( $_POST['monev_data'] as $key => $values ) {
                $sanitized_key = sanitize_key( $key );
                
                $sanitized_data[ $sanitized_key ] = [
                    'file_id'     => isset( $values['file_id'] ) ? sanitize_text_field( $values['file_id'] ) : '',
                    'file_name'   => isset( $values['file_name'] ) ? sanitize_text_field( $values['file_name'] ) : '',
                    'custom_link' => isset( $values['custom_link'] ) ? esc_url_raw( $values['custom_link'] ) : '',
                    'custom_text' => isset( $values['custom_text'] ) ? sanitize_text_field( $values['custom_text'] ) : ''
                ];
            }

            update_post_meta( $post_id, '_monev_data_values', $sanitized_data );
        }
    }
}
