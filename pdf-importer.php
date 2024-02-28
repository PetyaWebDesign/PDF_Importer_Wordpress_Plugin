<?php
/*
Plugin Name: PDF Importer
Description: Bulk import PDF files and create posts with attachments.
Version: 1.0
Author: Petya Petkova {dev} blondie;
*/

class PDF_Importer {
    // Constructor
    public function __construct() {
        add_action('admin_menu', array($this, 'pdf_importer_settings_menu'));
    }

    // Add settings menu to select post type
    public function pdf_importer_settings_menu() {
        add_options_page('PDF Importer Settings', 'PDF Importer', 'manage_options', 'pdf_importer_settings', array($this, 'pdf_importer_settings_page'));
    }

    // Settings page content
    public function pdf_importer_settings_page() {
        // Check if user has permission
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        // Check if form was submitted
        if (isset($_POST['pdf_importer_save_settings'])) {
            // Run PDF import process
            $message = $this->pdf_importer_process();
        }

        // Get current post type option value
        $post_type = get_option('pdf_importer_post_type', 'post');
        // Get current directory path option value
        $directory_path = get_option('pdf_importer_directory_path', '/uploads/pdf/');
        // Get current ACF field name option value
        $acf_field_name = get_option('pdf_importer_acf_field_name', 'file');
        ?>
        <div class="wrap">
            <h2>PDF Importer Settings</h2>
            <p>Select the post type you want to add your PDFs as new posts to. Keep in mind that you need to have an ACF field for this post type.</p>
            <form method="post" action="">
                <input type="hidden" name="pdf_importer_save_settings" value="1">
                <?php settings_fields('pdf_importer_settings_group'); ?>
                <table class="form-table">
                    <tr valign="top">
                        <th scope="row">Select Post Type:</th>
                        <td>
                            <select name="pdf_importer_post_type">
                                <?php
                                // Get all registered post types excluding 'page' and 'attachment'
                                $post_types = get_post_types(array('public' => true), 'objects');
                                foreach ($post_types as $pt) {
                                    if ($pt->name != 'page' && $pt->name != 'attachment') {
                                        echo '<option value="' . $pt->name . '"' . selected($pt->name, $post_type, false) . '>' . $pt->labels->singular_name . '</option>';
                                    }
                                }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">PDF Directory Path:</th>
                        <td>
                            <input type="text" name="pdf_importer_directory_path" value="<?php echo esc_attr($directory_path); ?>" style="width: 300px;">
                        </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">ACF Field Name:</th>
                        <td>
                            <input type="text" name="pdf_importer_acf_field_name" value="<?php echo esc_attr($acf_field_name); ?>" style="width: 300px;">
                        </td>
                    </tr>
                </table>
                <?php submit_button('Upload PDFs and create new posts'); ?>
            </form>
            <?php if (isset($message)) : ?>
                <p><?php echo $message; ?></p>
            <?php endif; ?>
        </div>
        <?php
    }

    // PDF import process
    public function pdf_importer_process() {
        // Get directory path from settings
        $directory_path = isset($_POST['pdf_importer_directory_path']) ? sanitize_text_field($_POST['pdf_importer_directory_path']) : '/uploads/pdf/';
        // Get ACF field name from settings
        $acf_field_name = isset($_POST['pdf_importer_acf_field_name']) ? sanitize_text_field($_POST['pdf_importer_acf_field_name']) : 'file';

        // Directory containing PDF files to import
        $directory = WP_CONTENT_DIR . $directory_path;

        // Get selected post type from settings
        $post_type = isset($_POST['pdf_importer_post_type']) ? sanitize_text_field($_POST['pdf_importer_post_type']) : 'post';

        $message = '';

        // Check if the directory exists
        if (file_exists($directory)) {
            // Get list of PDF files in the directory
            $files = glob($directory . '*.pdf');

            // Loop through each PDF file
            foreach ($files as $file) {
                // Check if post already exists for this PDF
                $existing_post_id = $this->pdf_importer_get_post_id_by_pdf($file);
                if (!$existing_post_id) {
                    // Create a new post
                    $post_id = wp_insert_post(array(
                        'post_title'    => $this->get_post_title_from_filename($file), // Set the file name as post title
                        'post_type'     => $post_type, // Selected post type
                        'post_status'   => 'publish'
                    ));

                    // If the post is successfully created
                    if ($post_id) {
                        // Get the absolute path to the PDF file
                        $file_path = $file;

                        // Add the PDF file as an attachment to the post
                        $attachment = array(
                            'post_title'     => basename($file_path),
                            'post_content'   => '',
                            'post_status'    => 'inherit',
                            'post_mime_type' => 'application/pdf' // Set MIME type for PDF
                        );
                        $attach_id = wp_insert_attachment($attachment, $file_path, $post_id);

                        // Set the attached PDF file as the featured image of the post (optional)
                        set_post_thumbnail($post_id, $attach_id);

                        // Update custom field with PDF file
                        if ($attach_id) {
                            update_field($acf_field_name, ['ID' => $attach_id], $post_id); // Set ACF field name dynamically
                        }
                    }
                }
            }

            // Set message
            $message = "All done, your posts are created.";
        } else {
            $message = "Directory not found!";
        }

        return $message;
    }

    // Function to get post ID by PDF file
    public function pdf_importer_get_post_id_by_pdf($pdf_file) {
        global $wpdb;
        $attachment = $wpdb->get_col($wpdb->prepare("SELECT ID FROM $wpdb->posts WHERE guid='%s';", $pdf_file));
        if (!empty($attachment)) {
            return $attachment[0];
        }
        return 0;
    }

    // Function to extract post title from filename
    private function get_post_title_from_filename($filename) {
        // Extract filename without extension
        $filename = pathinfo($filename, PATHINFO_FILENAME);
        // Remove underscores, hyphens, symbols, and any other non-word characters
        $filename = preg_replace('/[^\p{L}\p{N}\s]+/u', '', $filename);
        // Remove leading and trailing whitespaces
        $filename = trim($filename);
        // Return the modified filename as post title
        return $filename;
    }
}

// Initialize the PDF Importer class
$pdf_importer = new PDF_Importer();
?>
