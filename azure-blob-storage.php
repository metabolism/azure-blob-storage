<?php
/**
 * Plugin Name:       Azure Blob Storage for WordPress
 * Description:       Use the Microsoft Azure Storage service to host your website's media files.
 * Version:           1.0
 * Requires at least: 6.4
 * Requires PHP:      8.0
 * Author:            Metabolism, Microsoft Open Technologies
 * License:           BSD 2-Clause
 * License URI:       http://www.opensource.org/licenses/bsd-license.php
 * Text Domain:       azure-blob-storage
 */

/*
 * Copyright (c) 2009-2016, Microsoft Open Technologies, Inc.
 * Copyright (c) 2016, 10up
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 *
 * * Redistributions of source code must retain the above copyright notice, this list
 *   of conditions and the following disclaimer.
 *
 * * Redistributions in binary form must reproduce the above copyright notice, this
 *   list of conditions  and the following disclaimer in the documentation and/or
 *   other materials provided with the distribution.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
 * ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A  PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
 * ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS
 * OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)  HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
 * NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN
 * IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 */

/*
 * 'Microsoft Azure SDK for PHP v0.4.0' and its dependencies are included
 * in the library directory. If another version of the SDK is installed
 * and USESDKINSTALLEDGLOBALLY is defined, that version will be used instead.
 * 'Microsoft Azure SDK for PHP' provides access to the Microsoft Azure
 * Blob Storage service that this plugin enables for WordPress.
 * See https://github.com/windowsazure/azure-sdk-for-php/ for updates to the SDK.
 */

define( 'AZURE_BLOB_STORAGE_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
define( 'AZURE_BLOB_STORAGE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'AZURE_BLOB_STORAGE_PLUGIN_VERSION', '1.0' );

/**
 * @param $source_file
 * @return mixed
 */
function azure_blob_storage_upload_file($source_file)
{
    if( !file_exists($source_file) )
        return $source_file;

    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');
    $mime_type    = wp_check_filetype($source_file)['type'];

    try {

        \Windows_Azure_Helper::put_media_to_blob_storage('', $replace_file, $source_file, $mime_type);

    } catch ( Exception $e ) {

        $queue = get_option('azure_blob_storage_upload_queue');

        if( !is_array($queue) )
            $queue = [];

        $queue[] = [$source_file, $mime_type, $e->getMessage()];

        update_option('azure_blob_storage_upload_queue', $queue);
    }

    return $source_file;
}

function azure_blob_storage_delete_file($source_file)
{
    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');

    try {

        \Windows_Azure_Helper::delete_blob('', $replace_file);

    } catch ( Exception $e ) {

        $queue = get_option('azure_blob_storage_delete_queue');

        if( !is_array($queue) )
            $queue = [];

        $queue[] = [$source_file, $e->getMessage()];

        update_option('azure_blob_storage_delete_queue', $queue);
    }

    return $source_file;
}

add_action( 'init', function () {

    require_once ABSPATH . '/wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
    require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'vendor/autoload.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-windows-azure-helper.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-windows-azure-rest-api-client.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-windows-azure-storage-util.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-windows-azure-file-contents-provider.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-windows-azure-filesystem-access-provider.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-windows-azure-wp-filesystem-direct.php';

    require_once ABSPATH . WPINC . '/class-wp-image-editor.php';
    require_once ABSPATH . WPINC . '/class-wp-image-editor-gd.php';
    require_once ABSPATH . WPINC . '/class-wp-image-editor-imagick.php';

    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-azure-blob-image-editor-gd.php';
    require_once AZURE_BLOB_STORAGE_PLUGIN_PATH . 'includes/class-azure-blob-image-editor-imagick.php';

    add_filter('wp_image_editors', function ($implementations) {

        return array_merge(['Azure_Blob_Image_Editor_GD', 'Azure_Blob_Image_Editor_Imagick'], $implementations);

    }, 10);

   add_filter('wp_get_attachment_url', function ($url){

       if( !defined('MICROSOFT_AZURE_ACCOUNT_URL') )
           return $url;

        $upload_dir = wp_upload_dir();
        return str_replace($upload_dir['baseurl'], MICROSOFT_AZURE_ACCOUNT_URL, $url);
    });

    add_action( 'wp_save_file', 'azure_blob_storage_upload_file', 10, 2 );
    add_filter( 'wp_delete_file', 'azure_blob_storage_delete_file', 10, 2 );
});

