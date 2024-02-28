# PDF_Importer_Wordpress_Plugin

Plugin Name: PDF Importer

Description:
The PDF Importer plugin facilitates bulk importing of PDF files into WordPress, creating corresponding posts with attachments. It streamlines the process of importing PDFs stored in a specified directory on the server, creating posts based on the PDF filenames, and attaching the PDF files to the posts. Additionally, it allows users to specify the post type for the imported posts and the custom field to store the PDF attachments.

Features:

    Settings Page:
        The plugin provides a settings page accessible from the WordPress admin dashboard under the "PDF Importer Settings" menu.
        On this page, administrators can configure the following settings:
            Select the post type: Administrators can choose the post type for the imported posts from a dropdown menu. The available options include all registered public post types except "page" and "attachment."
            Specify the PDF directory path: Administrators can input the directory path where the PDF files are stored on the server. The default directory path is "/uploads/pdf/".
            Define the ACF field name: Administrators can specify the name of the Advanced Custom Fields (ACF) field where the PDF attachments will be stored. The default field name is "file".

    PDF Import Process:
        Upon submitting the settings form, the plugin initiates the PDF import process.
        It checks the specified directory for PDF files and iterates through each file.
        For each PDF file, the plugin checks if a corresponding post already exists based on the PDF filename.
        If no matching post is found, the plugin creates a new post with the title derived from the PDF filename.
        The PDF file is attached to the created post as an attachment, and its metadata is set accordingly.
        Optionally, the PDF attachment is set as the featured image of the post, and the specified ACF field is updated with the attachment details.

    Post Title Extraction:
        The plugin extracts the post title from the PDF filename to use when creating posts.
        It removes any underscores, hyphens, symbols, or non-word characters from the filename.
        Leading and trailing whitespaces are also trimmed to ensure a clean post title.

Usage:

    Navigate to the "PDF Importer Settings" page under the WordPress admin dashboard.
    Configure the desired settings:
        Select the post type for imported posts.
        Specify the directory path where PDF files are stored.
        Define the ACF field name to store PDF attachments.
    Click the "Upload PDFs and create new posts" button to initiate the PDF import process.
    The plugin will process each PDF file, creating corresponding posts and attaching the PDF files to them.
    Upon completion, a confirmation message will be displayed indicating that the posts have been successfully created.

Benefits:

    Streamlines the process of bulk importing PDF files into WordPress.
    Automates the creation of posts based on PDF filenames, saving time and effort.
    Provides flexibility by allowing administrators to customize settings such as post type and ACF field name.
    Ensures clean and formatted post titles by extracting them from PDF filenames and removing unnecessary characters.

Note: It's essential to ensure that the specified PDF directory path is accessible and contains the desired PDF files for import.

This detailed description provides users with a comprehensive understanding of the PDF Importer plugin's functionality, features, usage instructions, and benefits.
