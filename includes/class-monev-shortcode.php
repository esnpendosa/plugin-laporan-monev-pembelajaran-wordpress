<?php
/**
 * Shortcode class to handle frontend rendering of Monev Kurikulum as article cards
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Monev_Shortcode {

    public function __construct() {
        add_shortcode( 'monev_kurikulum', [ $this, 'render_shortcode' ] );
    }

    /**
     * Render the shortcode [monev_kurikulum]
     */
    public function render_shortcode( $atts ) {
        // Enqueue frontend assets
        wp_enqueue_style(
            'monev-frontend-style',
            MONEV_KURIKULUM_URL . 'assets/css/frontend.css',
            [],
            MONEV_KURIKULUM_VERSION
        );

        // Capture filter inputs from GET query parameters
        $selected_fakultas = isset( $_GET['monev_fakultas'] ) ? sanitize_text_field( $_GET['monev_fakultas'] ) : '';
        $selected_tahun = isset( $_GET['monev_tahun'] ) ? sanitize_text_field( $_GET['monev_tahun'] ) : '';

        // Build WP_Query arguments
        $args = [
            'post_type'      => 'monev_kurikulum',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC'
        ];

        // Tax Query (Fakultas Category Filter)
        if ( ! empty( $selected_fakultas ) ) {
            $args['tax_query'] = [
                [
                    'taxonomy' => 'fakultas_kurikulum',
                    'field'    => 'slug',
                    'terms'    => $selected_fakultas,
                ]
            ];
        }

        // Meta Query (Tahun Akademik Filter)
        if ( ! empty( $selected_tahun ) ) {
            $args['meta_query'] = [
                [
                    'key'     => '_monev_tahun_akademik',
                    'value'   => $selected_tahun,
                    'compare' => '='
                ]
            ];
        }

        $query = new WP_Query( $args );

        // Fetch distinct values for "Tahun Akademik" to populate filter dropdown
        global $wpdb;
        $tahun_options = $wpdb->get_col("
            SELECT DISTINCT meta_value 
            FROM $wpdb->postmeta 
            WHERE meta_key = '_monev_tahun_akademik' AND meta_value != ''
            ORDER BY meta_value DESC
        ");

        // Fetch all terms in custom taxonomy "fakultas_kurikulum"
        $fakultas_terms = get_terms([
            'taxonomy'   => 'fakultas_kurikulum',
            'hide_empty' => false
        ]);

        ob_start();
        ?>
        <div class="monev-kurikulum-wrapper">
            
            <!-- Elegant Blue Filter Bar -->
            <div class="monev-filter-bar">
                <form method="get" class="monev-filter-form">
                    <?php
                    // Maintain current page query parameter if exists (important for static page routing)
                    if ( is_page() ) {
                        global $post;
                        echo '<input type="hidden" name="page_id" value="' . esc_attr($post->ID) . '">';
                    }
                    ?>
                    <div class="monev-filter-fields">
                        <!-- Fakultas Dropdown -->
                        <div class="monev-filter-group">
                            <label for="monev_fakultas"><?php _e( 'Fakultas / Program Studi', 'monev-kurikulum' ); ?></label>
                            <select name="monev_fakultas" id="monev_fakultas">
                                <option value=""><?php _e( 'Semua Fakultas/Prodi', 'monev-kurikulum' ); ?></option>
                                <?php foreach ( $fakultas_terms as $term ) : ?>
                                    <option value="<?php echo esc_attr( $term->slug ); ?>" <?php selected( $selected_fakultas, $term->slug ); ?>>
                                        <?php echo esc_html( $term->name ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tahun Akademik Dropdown -->
                        <div class="monev-filter-group">
                            <label for="monev_tahun"><?php _e( 'Tahun Akademik', 'monev-kurikulum' ); ?></label>
                            <select name="monev_tahun" id="monev_tahun">
                                <option value=""><?php _e( 'Semua Tahun Akademik', 'monev-kurikulum' ); ?></option>
                                <?php foreach ( $tahun_options as $tahun ) : ?>
                                    <option value="<?php echo esc_attr( $tahun ); ?>" <?php selected( $selected_tahun, $tahun ); ?>>
                                        <?php echo esc_html( $tahun ); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="monev-filter-actions">
                        <button type="submit" class="monev-btn-filter"><?php _e( 'Filter', 'monev-kurikulum' ); ?></button>
                        <?php if ( ! empty( $selected_fakultas ) || ! empty( $selected_tahun ) ) : ?>
                            <a href="<?php echo esc_url( get_permalink() ); ?>" class="monev-btn-reset"><?php _e( 'Reset', 'monev-kurikulum' ); ?></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- 3-Column Article Card Grid -->
            <?php if ( $query->have_posts() ) : ?>
                <div class="monev-article-grid">
                    <?php 
                    while ( $query->have_posts() ) : 
                        $query->the_post();
                        $post_id = get_the_ID();
                        
                        // Meta fields
                        $file_id = get_post_meta( $post_id, '_monev_pdf_id', true );
                        $custom_link = get_post_meta( $post_id, '_monev_custom_link', true );
                        $tahun_akademik = get_post_meta( $post_id, '_monev_tahun_akademik', true );
                        $semester = get_post_meta( $post_id, '_monev_semester', true );

                        // Get Taxonomy Terms
                        $terms = get_the_terms( $post_id, 'fakultas_kurikulum' );
                        $term_names = [];
                        if ( is_array( $terms ) ) {
                            foreach ( $terms as $term ) {
                                $term_names[] = $term->name;
                            }
                        }

                        // Resolve PDF Download link
                        $pdf_link = '';
                        if ( ! empty( $file_id ) ) {
                            $pdf_link = wp_get_attachment_url( $file_id );
                        } elseif ( ! empty( $custom_link ) ) {
                            $pdf_link = $custom_link;
                        }

                        // Excerpt/Snippet
                        $excerpt = wp_trim_words( get_the_content(), 16, '...' );
                        if ( empty( $excerpt ) ) {
                            $excerpt = __( 'Tidak ada deskripsi laporan tambahan untuk materi Monev Kurikulum ini.', 'monev-kurikulum' );
                        }

                        // Thumbnail
                        $thumb_url = get_the_post_thumbnail_url( $post_id, 'medium_large' );
                        ?>
                        <article class="monev-article-card">
                            <!-- Card Header Image -->
                            <div class="monev-card-image-wrapper">
                                <?php if ( ! empty( $thumb_url ) ) : ?>
                                    <img src="<?php echo esc_url( $thumb_url ); ?>" alt="<?php the_title_attribute(); ?>" class="monev-card-img">
                                <?php else : ?>
                                    <!-- Premium Blue Gradient Fallback Graphic -->
                                    <div class="monev-card-fallback-cover">
                                        <div class="monev-fallback-shapes"></div>
                                        <span class="monev-fallback-pdf-icon"></span>
                                        <span class="monev-fallback-label"><?php _e( 'Monev Kurikulum', 'monev-kurikulum' ); ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Semester Badge -->
                                <?php if ( ! empty( $semester ) ) : ?>
                                    <span class="monev-card-badge-semester <?php echo esc_attr( strtolower( $semester ) ); ?>">
                                        <?php echo esc_html( $semester ); ?>
                                    </span>
                                <?php endif; ?>
                            </div>

                            <!-- Card Body -->
                            <div class="monev-card-body">
                                <!-- Categories and Meta tags -->
                                <div class="monev-card-meta">
                                    <?php if ( ! empty( $term_names ) ) : ?>
                                        <span class="monev-meta-category" title="<?php echo esc_attr( implode( ', ', $term_names ) ); ?>">
                                            <?php echo esc_html( $term_names[0] ); ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ( ! empty( $tahun_akademik ) ) : ?>
                                        <span class="monev-meta-year">
                                            <span class="dashicons dashicons-calendar"></span> TA. <?php echo esc_html( $tahun_akademik ); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Title -->
                                <h3 class="monev-card-title">
                                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                                </h3>

                                <!-- Description Excerpt -->
                                <p class="monev-card-excerpt"><?php echo esc_html( $excerpt ); ?></p>
                            </div>

                            <!-- Card Footer -->
                            <div class="monev-card-footer">
                                <?php if ( ! empty( $pdf_link ) ) : ?>
                                    <a href="<?php echo esc_url( $pdf_link ); ?>" class="monev-btn-download-pdf" target="_blank" rel="noopener noreferrer">
                                        <span class="monev-icon-pdf"></span> <?php _e( 'Unduh Laporan PDF', 'monev-kurikulum' ); ?>
                                    </a>
                                <?php else : ?>
                                    <span class="monev-no-file-btn"><?php _e( 'Berkas Belum Tersedia', 'monev-kurikulum' ); ?></span>
                                <?php endif; ?>
                                
                                <a href="<?php the_permalink(); ?>" class="monev-btn-read-more" title="<?php _e( 'Baca Selengkapnya', 'monev-kurikulum' ); ?>">
                                    <span class="dashicons dashicons-arrow-right-alt"></span>
                                </a>
                            </div>
                        </article>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <div class="monev-no-data">
                    <span class="dashicons dashicons-warning" style="font-size: 32px; width: 32px; height: 32px; color: #94a3b8; margin-bottom: 12px;"></span>
                    <p><?php _e( 'Tidak ada laporan Monev Kurikulum yang ditemukan cocok dengan kriteria filter.', 'monev-kurikulum' ); ?></p>
                </div>
            <?php endif; ?>

        </div>
        <?php
        wp_reset_postdata();
        return ob_get_clean();
    }
}
