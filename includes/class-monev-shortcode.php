<?php
/**
 * Shortcode class to handle frontend rendering of Monev Pembelajaran
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Monev_Shortcode {

    public function __construct() {
        add_shortcode( 'monev_pembelajaran', [ $this, 'render_shortcode' ] );
    }

    /**
     * Render the shortcode [monev_pembelajaran]
     */
    public function render_shortcode( $atts ) {
        // Enqueue frontend assets
        wp_enqueue_style(
            'monev-frontend-style',
            MONEV_PEMBELAJARAN_URL . 'assets/css/frontend.css',
            [],
            MONEV_PEMBELAJARAN_VERSION
        );

        // Fetch all published monev posts (latest semesters first)
        $query = new WP_Query([
            'post_type'      => 'monev_pembelajaran',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC'
        ]);

        if ( ! $query->have_posts() ) {
            return '<div class="monev-no-data">' . esc_html__( 'Belum ada data Monev Pembelajaran yang diposting.', 'monev-pembelajaran' ) . '</div>';
        }

        // Get template structure
        $template = get_option( 'monev_template_structure', [] );
        if ( empty( $template ) ) {
            return '<div class="monev-no-data">' . esc_html__( 'Template struktur belum dikonfigurasi oleh Administrator.', 'monev-pembelajaran' ) . '</div>';
        }

        ob_start();
        ?>
        <div class="monev-frontend-container">
            <?php 
            while ( $query->have_posts() ) : 
                $query->the_post();
                $post_id = get_the_ID();
                $semester_title = get_the_title();
                $saved_values = get_post_meta( $post_id, '_monev_data_values', true );
                if ( ! is_array( $saved_values ) ) {
                    $saved_values = [];
                }
            ?>
                <!-- Semester Section Table -->
                <div class="monev-semester-block">
                    <div class="monev-table-responsive">
                        <table class="monev-table">
                            <thead>
                                <tr>
                                    <th class="monev-th-title"><?php echo esc_html( $semester_title ); ?></th>
                                    <th class="monev-th-file"><?php _e( 'File', 'monev-pembelajaran' ); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                foreach ( $template as $faculty ) : 
                                    $fac_id = $faculty['id'];
                                    $fac_data = isset( $saved_values[ $fac_id ] ) ? $saved_values[ $fac_id ] : [];
                                    $fac_file_id = isset( $fac_data['file_id'] ) ? $fac_data['file_id'] : '';
                                    $fac_custom_link = isset( $fac_data['custom_link'] ) ? $fac_data['custom_link'] : '';
                                    $fac_custom_text = isset( $fac_data['custom_text'] ) ? $fac_data['custom_text'] : '';

                                    // Resolve Link for Faculty
                                    $fac_link = '';
                                    if ( ! empty( $fac_file_id ) ) {
                                        $fac_link = wp_get_attachment_url( $fac_file_id );
                                    } elseif ( ! empty( $fac_custom_link ) ) {
                                        $fac_link = $fac_custom_link;
                                    }

                                    // Check if row has any content to display in File column
                                    $has_fac_action = ! empty( $fac_link ) || ! empty( $fac_custom_text );
                                ?>
                                    <!-- Faculty Row -->
                                    <tr class="monev-tr-faculty">
                                        <td class="monev-td-title">
                                            <strong><?php echo esc_html( $faculty['name'] ); ?></strong>
                                        </td>
                                        <td class="monev-td-action">
                                            <?php if ( ! empty( $fac_link ) ) : ?>
                                                <a href="<?php echo esc_url( $fac_link ); ?>" class="monev-btn-download" target="_blank" rel="noopener noreferrer">
                                                    <span class="monev-icon-download"></span> <?php _e( 'Unduh', 'monev-pembelajaran' ); ?>
                                                </a>
                                            <?php elseif ( ! empty( $fac_custom_text ) ) : ?>
                                                <span class="monev-badge-status"><?php echo esc_html( $fac_custom_text ); ?></span>
                                            <?php else : ?>
                                                <span class="monev-empty-dash">&mdash;</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>

                                    <!-- Study Programs Under Faculty -->
                                    <?php if ( ! empty( $faculty['items'] ) ) : ?>
                                        <?php 
                                        foreach ( $faculty['items'] as $prodi ) : 
                                            $prodi_id = $prodi['id'];
                                            $prodi_data = isset( $saved_values[ $prodi_id ] ) ? $saved_values[ $prodi_id ] : [];
                                            $prodi_file_id = isset( $prodi_data['file_id'] ) ? $prodi_data['file_id'] : '';
                                            $prodi_custom_link = isset( $prodi_data['custom_link'] ) ? $prodi_data['custom_link'] : '';
                                            $prodi_custom_text = isset( $prodi_data['custom_text'] ) ? $prodi_data['custom_text'] : '';

                                            // Resolve Link for Prodi
                                            $prodi_link = '';
                                            if ( ! empty( $prodi_file_id ) ) {
                                                $prodi_link = wp_get_attachment_url( $prodi_file_id );
                                            } elseif ( ! empty( $prodi_custom_link ) ) {
                                                $prodi_link = $prodi_custom_link;
                                            }
                                        ?>
                                            <tr class="monev-tr-prodi">
                                                <td class="monev-td-title">
                                                    <span class="monev-prodi-indent"></span>
                                                    <span><?php echo esc_html( $prodi['name'] ); ?></span>
                                                </td>
                                                <td class="monev-td-action">
                                                    <?php if ( ! empty( $prodi_link ) ) : ?>
                                                        <a href="<?php echo esc_url( $prodi_link ); ?>" class="monev-btn-download" target="_blank" rel="noopener noreferrer">
                                                            <span class="monev-icon-download"></span> <?php _e( 'Unduh', 'monev-pembelajaran' ); ?>
                                                        </a>
                                                    <?php elseif ( ! empty( $prodi_custom_text ) ) : ?>
                                                        <span class="monev-badge-status"><?php echo esc_html( $prodi_custom_text ); ?></span>
                                                    <?php else : ?>
                                                        <span class="monev-empty-dash">&mdash;</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>

                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php 
            endwhile; 
            wp_reset_postdata();
            ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
