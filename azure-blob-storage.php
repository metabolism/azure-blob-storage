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

global $azure_blob_storage_last_error;

/**
 * @param $source_file
 * @return void
 */
function azure_blob_storage_set_metadata($source_file)
{
    global $azure_blob_storage_list;

    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');

    $azure_blob_storage_list[md5($replace_file)] = 1;
    file_put_contents(AZURE_BLOB_STORAGE_LIST_FILE, json_encode($azure_blob_storage_list), LOCK_EX);
}

/**
 * @param $source_file
 * @return void
 */
function azure_blob_storage_remove_metadata($source_file)
{
    global $azure_blob_storage_list;

    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');

    unset($azure_blob_storage_list[md5($replace_file)]);
    file_put_contents(AZURE_BLOB_STORAGE_LIST_FILE, json_encode($azure_blob_storage_list), LOCK_EX);
}

/**
 * @param $source_file
 * @return bool
 */
function azure_blob_storage_get_metadata($source_file)
{
    global $azure_blob_storage_list;

    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');

    return isset($azure_blob_storage_list[md5($replace_file)]);
}

/**
 * @param $source_file
 * @param bool $retry_later
 * @return bool
 */
function azure_blob_storage_upload_file($source_file, $retry_later=true)
{
    global $azure_blob_storage_last_error;

    if( !file_exists($source_file) ){

        $azure_blob_storage_last_error = 'File does not exist';
        return true;
    }

    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');
    $mime_type    = wp_check_filetype($source_file)['type'];

    try {

        \Windows_Azure_Helper::put_media_to_blob_storage('', $replace_file, $source_file, $mime_type);
        azure_blob_storage_set_metadata($source_file);

        return true;

    } catch ( Exception $e ) {

        if( $retry_later ){

            $queue = get_option('azure_blob_storage_upload_queue');

            if( !is_array($queue) )
                $queue = [];

            $queue[] = [$source_file, $mime_type, $e->getMessage()];

            update_option('azure_blob_storage_upload_queue', $queue);
        }

        $azure_blob_storage_last_error = $e->getMessage();
    }

    return false;
}

/**
 * @param $source_file
 * @return mixed
 */
function azure_blob_storage_delete_file($source_file)
{
    $upload_dir   = wp_upload_dir();
    $replace_file = ltrim(str_replace( $upload_dir['basedir'], '', $source_file ), '/');

    try {

        \Windows_Azure_Helper::delete_blob('', $replace_file);

        azure_blob_storage_remove_metadata($source_file);

        return $source_file;

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

    global $azure_blob_storage_list;

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

    $upload_dir = wp_upload_dir();
    define( 'AZURE_BLOB_STORAGE_LIST_FILE', $upload_dir['basedir'] . '/.azure-blob-storage-list' );

    $azure_blob_storage_list = json_decode(file_get_contents(AZURE_BLOB_STORAGE_LIST_FILE), true);

    if( !is_array($azure_blob_storage_list) )
        $azure_blob_storage_list = [];

    add_filter('wp_image_editors', function ($implementations) {

        return array_merge(['Azure_Blob_Image_Editor_GD', 'Azure_Blob_Image_Editor_Imagick'], $implementations);

    }, 10);

    add_filter('wp_get_attachment_url', function ($url){

        if( !defined('MICROSOFT_AZURE_ACCOUNT_URL') )
            return $url;

        $upload_dir = wp_upload_dir();
        $source_file = str_replace($upload_dir['baseurl'], '', $url);

        if( azure_blob_storage_get_metadata($source_file) )
            return str_replace($upload_dir['baseurl'], MICROSOFT_AZURE_ACCOUNT_URL, $url);

        return $url;
    });

    add_action( 'wp_save_file', 'azure_blob_storage_upload_file', 10, 1 );
    add_filter( 'wp_delete_file', 'azure_blob_storage_delete_file', 10, 1 );
});

add_action( 'admin_notices', function (){

    $queue = get_option('azure_blob_storage_upload_queue');

    if( is_array($queue) )
        echo '<div class="error"><p>Azure blob : '.count($queue).' file(s) waiting to upload</p></div>';
});

add_action('sync_upload_queue_to_azure', function () {

    $queue = get_option('azure_blob_storage_upload_queue');

    if( is_array($queue) ){

        $queue = array_values($queue);
        $nextFile = $queue[0];

        if( azure_blob_storage_upload_file($nextFile, false) ){

            unset($queue[0]);
            $queue = array_values($queue);

            if( empty($queue) )
                delete_option('azure_blob_storage_upload_queue');
            else
                update_option('azure_blob_storage_upload_queue', $queue);
        }
    }
});

add_action('wp_ajax_sync_to_azure', function () {

    $queue = get_option('azure_blob_storage_sync_queue');

    if( empty($queue) ){

        $upload_dir = wp_get_upload_dir();
        $queue = list_files($upload_dir['basedir'], 100, ['.gitkeep', '.azure-blob-storage-list']);

        update_option('azure_blob_storage_sync_queue', $queue);
    }

    $queue = array_values($queue);
    $nextFile = $queue[0];

    if( azure_blob_storage_upload_file($nextFile, false) ){

        unset($queue[0]);

        $queue = array_values($queue);

        if( empty($queue) )
            delete_option('azure_blob_storage_sync_queue');
        else
            update_option('azure_blob_storage_sync_queue', $queue);

        wp_send_json(['message' => 'Upload completed successfully!', 'count' => count($queue)]);
    }
    else{

        global $azure_blob_storage_last_error;

        wp_send_json(['message' => 'Upload failed!', 'file'=>$nextFile, 'error'=>$azure_blob_storage_last_error, 'count' => count($queue)]);
    }
});

add_action( 'admin_init', function() {

    if( isset($_GET['clear-azure-queue']) ){

        delete_option('azure_blob_storage_sync_queue');

        wp_redirect(admin_url('options-media.php'));
        exit;
    }

    add_settings_field('upload_to_azure', __('Azure Blob Storage', 'azure-blob-storage'), function () {

        $queue = get_option('azure_blob_storage_sync_queue');

        if( empty($queue) ){

            $upload_dir = wp_get_upload_dir();
            $queue = list_files($upload_dir['basedir'], 100, ['.gitkeep', '.azure-blob-storage-list']);
        }

        ?>
        <a class="button button-primary" id="sync-to-azure">Upload <?php echo count($queue); ?> file(s)</a>
        <a class="button button-secondary" href="<?php echo admin_url('options-media.php')?>?clear-azure-queue">Clear queue</a>
        <script>

            function azureUploadNextFile($el) {

                fetch(ajaxurl, {
                    method: "POST",
                    headers: {"Content-Type": "application/x-www-form-urlencoded"},
                    body: "action=sync_to_azure"
                }).then(function(response) {

                    return response.json();

                }).then(data => {

                    $el.innerHTML = "Uploading... "+data.count+" file(s) left";

                    if( data.count )
                        azureUploadNextFile($el);
                    else
                        $el.innerHTML = "Upload complete";

                }).catch(error => {

                    alert(error);
                });
            }

            document.getElementById("sync-to-azure").addEventListener("click", function() {

                this.disabled = true;
                this.innerHTML = "Uploading...";

                azureUploadNextFile(this)
            })
        </script>
        <?php

    }, 'media');
});

