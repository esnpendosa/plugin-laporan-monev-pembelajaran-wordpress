/**
 * Monev Pembelajaran Admin Scripts
 * Integrates WordPress Media Uploader and uploader UI states
 */
jQuery(document).ready(function($) {
    var monev_media_frame;

    // Use event delegation so it works even if rows are dynamically rendered
    $(document).on('click', '.monev-upload-btn', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $wrapper = $button.closest('.monev-media-uploader-wrapper');

        // Create the media frame if it doesn't exist
        monev_media_frame = wp.media({
            title: 'Pilih atau Unggah File Monev',
            button: {
                text: 'Gunakan File Ini'
            },
            multiple: false
        });

        // Store current active wrapper in the frame object
        monev_media_frame.activeWrapper = $wrapper;

        // Callback when a file is selected
        monev_media_frame.on('select', function() {
            var attachment = monev_media_frame.state().get('selection').first().toJSON();
            var $activeWrapper = monev_media_frame.activeWrapper;

            // Update hidden inputs
            $activeWrapper.find('.monev-file-id').val(attachment.id);
            $activeWrapper.find('.monev-file-name').val(attachment.filename);

            // Update badge UI
            var $badge = $activeWrapper.find('.monev-file-badge');
            $badge.removeClass('no-file').addClass('has-file');
            $badge.find('.monev-file-badge-text').text(attachment.filename);

            // Show clear button
            $activeWrapper.find('.monev-clear-btn').show();
        });

        // Open the modal
        monev_media_frame.open();
    });

    // Clear file selection
    $(document).on('click', '.monev-clear-btn', function(e) {
        e.preventDefault();
        
        var $button = $(this);
        var $wrapper = $button.closest('.monev-media-uploader-wrapper');

        // Reset inputs
        $wrapper.find('.monev-file-id').val('');
        $wrapper.find('.monev-file-name').val('');

        // Reset badge UI
        var $badge = $wrapper.find('.monev-file-badge');
        $badge.removeClass('has-file').addClass('no-file');
        $badge.find('.monev-file-badge-text').text('Tidak ada file');

        // Hide clear button
        $button.hide();
    });
});
