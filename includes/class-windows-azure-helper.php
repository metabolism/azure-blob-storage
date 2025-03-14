<?php

/**
 * Microsoft Azure Storage helper class.
 *
 * Version: 4.0.0
 *
 * Author: Microsoft Open Technologies, Inc.
 *
 * Author URI: http://www.microsoft.com/
 *
 * License: New BSD License (BSD)
 *
 * Copyright (c) Microsoft Open Technologies, Inc.
 * All rights reserved.
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided that the following conditions are met:
 * Redistributions of source code must retain the above copyright notice, this list
 * of conditions and the following disclaimer.
 * Redistributions in binary form must reproduce the above copyright notice, this
 * list of conditions  and the following disclaimer in the documentation and/or
 * other materials provided with the distribution.
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
 *
 * PHP Version 5
 *
 * @category  WordPress_Plugin
 * @package   Windows_Azure_Storage_For_WordPress
 * @author    Microsoft Open Technologies, Inc. <msopentech@microsoft.com>
 * @copyright Microsoft Open Technologies, Inc.
 * @license   New BSD license, (http://www.opensource.org/licenses/bsd-license.php)
 * @link      http://www.microsoft.com
 * @since     4.0.0
 */
class Windows_Azure_Helper {

	/**
	 * Emulator hostname.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const EMULATOR_BLOB_URI = '127.0.0.1:10000';

	/**
	 * Blob hostname.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const BLOB_BASE_DNS_NAME = 'blob.core.windows.net';

	/**
	 * Developer storage account name.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const DEV_STORE_NAME = 'devstoreaccount1';

	/**
	 * Developer storage account key.
	 *
	 * @since 4.0.0
	 *
	 * @const string
	 */
	const DEV_STORE_KEY = 'Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==';

	/**
	 * Return account name.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Account name.
	 */
	static public function get_account_name() {
		return defined( 'MICROSOFT_AZURE_ACCOUNT_NAME' )
			? MICROSOFT_AZURE_ACCOUNT_NAME
			: get_option( 'azure_storage_account_name' );
	}
	/**
	 * Return account name.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Account name.
	 */
	static public function get_container_name() {
		return defined( 'MICROSOFT_AZURE_CONTAINER_NAME' )
			? MICROSOFT_AZURE_CONTAINER_NAME
			: get_option( 'azure_storage_container_name' );
	}

	/**
	 * Return account key.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Account key.
	 */
	static public function get_account_key() {
		return defined( 'MICROSOFT_AZURE_ACCOUNT_KEY' )
			? MICROSOFT_AZURE_ACCOUNT_KEY
			: get_option( 'azure_storage_account_primary_access_key' );
	}

	/**
	 * Return override container name.
	 *
	 * @since 4.5.0
	 *
	 * @return mixed|void Account key.
	 */
	static public function get_azure_storage_override_container_path() {
		return defined( 'MICROSOFT_AZURE_OVERRIDE_CONTAINER_PATH' )
			? MICROSOFT_AZURE_OVERRIDE_CONTAINER_PATH
			: get_option( 'azure_storage_override_container_path' );
	}
 
	/**
	 * Return CNAME url.
	 *
	 * @since 4.0.0
	 *
	 * @return string CNAME value.
	 */
	static public function get_cname() {
		return untrailingslashit( strtolower( defined( 'MICROSOFT_AZURE_CNAME' ) ? MICROSOFT_AZURE_CNAME : get_option( 'cname' ) ) );
	}

	/**
	 * Returns a flag which determines whether or not to use this for default upload.
	 *
	 * @since 4.2.0
	 *
	 * @static
	 * @access public
	 * @return boolean
	 */
	static public function get_use_for_default_upload() {
		$use_for_default_upload = defined( 'MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD' )
			? MICROSOFT_AZURE_USE_FOR_DEFAULT_UPLOAD
			: get_option( 'azure_storage_use_for_default_upload' );

		return filter_var( $use_for_default_upload, FILTER_VALIDATE_BOOLEAN );
	}

	/**
	 * Return HTTP proxy setting.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy host.
	 */
	static public function get_http_proxy_host() {
		return get_option( 'http_proxy_host' );
	}

	/**
	 * Return HTTP proxy port.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy port.
	 */
	static public function get_http_proxy_port() {
		return get_option( 'http_proxy_port' );
	}

	/**
	 * Return HTTP proxy username.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy username.
	 */
	static public function get_http_proxy_username() {
		return get_option( 'http_proxy_username' );
	}

	/**
	 * Return HTTP proxy password.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Proxy password.
	 */
	static public function get_http_proxy_password() {
		return get_option( 'http_proxy_password' );
	}

	/**
	 * Return storage default container.
	 *
	 * @since 4.0.0
	 *
	 * @return mixed|void Default container name.
	 */
	static public function get_default_container() {
		return defined( 'MICROSOFT_AZURE_CONTAINER' )
			? MICROSOFT_AZURE_CONTAINER
			: get_option( 'default_azure_storage_account_container_name' );
	}

	/**
	 * Set storage default container.
	 *
	 * @param string $container Default container.
	 *
	 * @since 4.0.0
	 *
	 * @return void
	 */
	static public function set_default_container( $container ) {
		return update_option( 'default_azure_storage_account_container_name', $container );
	}

	/**
	 * Whether to delete local file after uploading it to Azure Storage or not.
	 *
	 * @since 4.0.0
	 *
	 * @return bool True if delete, false to keep file.
	 */
	static public function delete_local_file() {
		$option_value = (int) get_option( 'azure_storage_keep_local_file', 0 );

		return ( 0 === $option_value );
	}

	/**
	 * Return browse cache TTL.
	 *
	 * @since 4.0.0
	 *
	 * @return int Cache TTL.
	 */
	static public function get_cache_ttl() {
		return (int) get_option( 'azure_browse_cache_results', 15 );
	}

	/**
	 * Returns cache-control.
	 *
	 * @since 4.1.0
	 *
	 * @return int Cache-control.
	 */
	static public function get_cache_control() {
		return defined( 'MICROSOFT_AZURE_CACHE_CONTROL' )
			? MICROSOFT_AZURE_CACHE_CONTROL
			: get_option( 'azure_cache_control', 600 );
	}

	/**
	 * Return container ACL.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return string|WP_Error API call result.
	 */
	static public function get_container_acl( $container_name, $account_name = '', $account_key = '' ) {

		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		return $rest_api_client->get_container_acl( $container_name );
	}

	/**
	 * Return API credentials.
	 *
	 * @since 4.0.0
	 *
	 * @param string $account_name Account name.
	 * @param string $account_key  Account key.
	 *
	 * @return array Account credentials array.
	 */
	static public function get_api_credentials( $account_name, $account_key ) {
		if ( '' === $account_name ) {
			$account_name = self::get_account_name();
		}

		if ( '' === $account_key ) {
			$account_key = self::get_account_key();
		}

		return array( $account_name, $account_key );
	}

	/**
	 * Return containers list.
	 *
	 * @since 4.0.0
	 *
	 * @param string $account_name Account name.
	 * @param string $account_key  Account key.
	 * @param bool   $refresh      Whether new API request should be made instead of using cached list.
	 *
	 * @return WP_Error|Windows_Azure_List_Containers_Response Containers iterator class or WP_Error on failure.
	 */
	static public function list_containers( $account_name = '', $account_key = '', $refresh = false ) {
		static $containers_list;

		if ( null === $containers_list || $refresh ) {
			list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
			$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

			$containers_list = $rest_api_client->list_containers();
		}

		return $containers_list;
	}

	/**
	 * Create new container.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return string|WP_Error Container name or WP_Error on failure.
	 */
	static public function create_container( $container_name, $account_name = '', $account_key = '' ) {
		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		return $rest_api_client->create_container( $container_name );
	}

	/**
	 * Delete blob.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $file_path      Blob path.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return bool|WP_Error True on success or WP_Error on failure.
	 */
	static public function delete_blob( $container_name, $file_path, $account_name = '', $account_key = '' ) {

        list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );

        if( empty($container_name) )
            $container_name = self::get_container_name();

		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		return $rest_api_client->delete_blob( $container_name, $file_path );
	}

	/**
	 * Return blobs list from given container.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container    Container name.
	 * @param string $account_name Account name.
	 * @param string $account_key  Account key.
	 * @param bool   $refresh      Whether new API request should be made instead of using cached list.
	 *
	 * @return WP_Error|Windows_Azure_List_Blobs_Response Blobs iterator class or WP_Error on failure.
	 */
	static public function list_blobs( $container, $account_name = '', $account_key = '', $refresh = false ) {
		static $blobs_list;

		$containers_list = array();
		if ( null === $blobs_list || $refresh ) {
			list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
			$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

			$containers_list = $rest_api_client->list_blobs( $container );
		}

		return $containers_list;
	}

	/**
	 * Return blob properties.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $blob_name      Blob name.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return array|WP_Error API call result.
	 */
	static public function get_blob_properties( $container_name, $blob_name, $account_name = '', $account_key = '' ) {

		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		return $rest_api_client->get_blob_properties( $container_name, $blob_name );
	}

	/**
	 * Put uploaded file into Azure storage.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $blob_name      Blob name.
	 * @param string $local_path     Local path.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return bool|string|WP_Error False or WP_Error on failure URI on success.
	 */
	static public function put_uploaded_file_to_blob_storage( $container_name, $blob_name, $local_path, $account_name = '', $account_key = '' ) {
		if ( ! file_exists( $local_path ) ) {
			return new \WP_Error( -1, sprintf( __( 'Uploaded file %s does not exist.', 'windows-azure-storage' ) ), $blob_name );
		}
		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		$remote_path = self::get_unique_blob_name( $container_name, $blob_name );

		$finfo     = finfo_open( FILEINFO_MIME_TYPE );
		$mime_type = finfo_file( $finfo, $local_path );
		finfo_close( $finfo );

		$result = $rest_api_client->put_blob( $container_name, $local_path, $remote_path, true, $mime_type );
		if ( ! $result || is_wp_error( $result ) ) {
			return $result;
		}

		$rest_api_client->put_blob_properties( $container_name, $remote_path, array(
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_BLOB_CONTENT_TYPE => $mime_type,
			Windows_Azure_Rest_Api_Client::API_HEADER_CACHE_CONTROL        => sprintf( "max-age=%d, must-revalidate", Windows_Azure_Helper::get_cache_control() ),
		) );

		return $result;
	}

	/**
	 * Wrapper for REST API client sanitize file names. Supports only single file.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $blob_name      Blob name.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return string Sanitized file name.
	 */
	static public function get_unique_blob_name( $container_name, $blob_name, $account_name = '', $account_key = '' ) {
		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client      = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );
		$file_info            = array(
			$blob_name => array(
				$blob_name => $blob_name,
			),
		);
		$sanitize_blobs_names = $rest_api_client->sanitize_blobs_names( $container_name, $file_info );
		// Go very optimistic.
		if ( is_wp_error( $sanitize_blobs_names ) ) {
			$info        = pathinfo( $blob_name );
			$unique_name = uniqid( '-', false ) . '-' . uniqid( '-', false ) . $info['basename'];
			if ( array_key_exists( 'extension', $info ) && ! empty( $info['extension'] ) ) {
				$unique_name .= '.' . $info['extension'];
			}

			return $unique_name;
		}

		return $sanitize_blobs_names[ $blob_name ][ $blob_name ];
	}

	/**
	 * Put media file into Azure storage.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name Container name.
	 * @param string $blob_name      Blob name.
	 * @param string $local_path     Local path.
	 * @param string $mime_type      Mime type.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 *
	 * @return bool|string|WP_Error False or WP_Error on failure URI on success.
	 */
	static public function put_media_to_blob_storage( $container_name, $blob_name, $local_path, $mime_type, $account_name = '', $account_key = '' ) {
		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

        if( empty($container_name) )
            $container_name = self::get_container_name();

		$result = $rest_api_client->put_blob( $container_name, $local_path, $blob_name, false, $mime_type );
		if ( ! $result || is_wp_error( $result ) ) {
			return $result;
		}

		$cache_control = Windows_Azure_Helper::get_cache_control();
		if ( is_numeric( $cache_control ) ) {
			$cache_control = sprintf( "max-age=%d, must-revalidate", $cache_control );
		}

		$rest_api_client->put_blob_properties( $container_name, $blob_name, array(
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_BLOB_CONTENT_TYPE  => $mime_type,
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_BLOB_CACHE_CONTROL => apply_filters( 'windows_azure_blob_cache_control', $cache_control ),
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_ACCESS_TIER        => apply_filters( 'windows_azure_blob_access_tier', 'Hot' ),
		) );

		return $result;
	}

	/**
	 * Copy media file into same Container.
	 *
	 * @since 4.0.0
	 *
	 * @param string $container_name   Container name.
	 * @param string $destination_path Destination Path.
	 * @param string $source_path      Local path.
	 * @param string $account_name   Account name.
	 * @param string $account_key    Account key.
	 * @param int    $cache          Max-age cache
	 *
	 * @return bool|string|WP_Error False or WP_Error on failure URI on success.
	 */
	static public function copy_media_to_blob_storage( $container_name, $destination_path, $source_path, $mime_type, $account_name = '', $account_key = '', $cache = null ) {
		list( $account_name, $account_key ) = self::get_api_credentials( $account_name, $account_key );
		$rest_api_client = new Windows_Azure_Rest_Api_Client( $account_name, $account_key );

		$result = $rest_api_client->copy_blob( $container_name, $destination_path, $source_path );
		if ( ! $result || is_wp_error( $result ) ) {
			return $result;
		}

		$cache_control = ( empty( $cache ) ) ? Windows_Azure_Helper::get_cache_control() : $cache;
		if ( is_numeric( $cache_control ) ) {
			$cache_control = sprintf( "max-age=%d, must-revalidate", $cache_control );
		}

		$rest_api_client->put_blob_properties( $container_name, $source_path, array(
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_BLOB_CONTENT_TYPE  => $mime_type,
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_BLOB_CACHE_CONTROL => apply_filters( 'windows_azure_blob_cache_control', $cache_control ),
			Windows_Azure_Rest_Api_Client::API_HEADER_MS_ACCESS_TIER        => apply_filters( 'windows_azure_blob_access_tier', 'Hot' ),
		) );

		return $result;
	}

	/**
	 * Return API hostname.
	 *
	 * @since 4.0.0
	 *
	 * @return string API hostname.
	 */
	static public function get_hostname() {
		$storage_account_name = self::get_account_name();
		if ( self::DEV_STORE_NAME === $storage_account_name ) {
			// Use development storage.
			$host_name = self::EMULATOR_BLOB_URI;
		} else {
			// Use cloud storage.
			$host_name = self::BLOB_BASE_DNS_NAME;
		}

		// Remove http/https from the beginning.
		if ( 0 === strpos( $host_name, 'http' ) ) {
			/** @var $parts array */
			$parts     = parse_url( $host_name );
			$host_name = $parts['host'];
			if ( ! empty( $parts['port'] ) ) {
				$host_name = $host_name . ':' . $parts['port'];
			}
		}

		return $host_name;
	}

	/**
	 * Return full blob URL.
	 *
	 * @param string $blob_name Blob name.
	 *
	 * @since 4.0.0
	 *
	 * @return string Full blob URL.
	 */
	static public function get_full_blob_url( $blob_name ) {
		return sprintf( '%1$s/%2$s',
			untrailingslashit( Windows_Azure_Storage_Util::get_storage_url_base( true ) ),
			$blob_name
		);
	}

	/**
	 * Unlink file using $wp_filesystem.
	 *
	 * @since 4.0.0
	 *
	 * @param string $relative_path Relative path under uploads directory.
	 *
	 * @return bool
	 */
	static public function unlink_file( $relative_path ) {
		/** @var $wp_filesystem \WP_Filesystem_Base */
		global $wp_filesystem;

		$result = false;

		if ( WP_Filesystem() ) {
			$upload_dir = self::wp_upload_dir();
			$filename   = $upload_dir['uploads'] . DIRECTORY_SEPARATOR . $relative_path;
			$result     = $wp_filesystem->delete( $filename, false, 'f' );
		}

		return apply_filters( 'windows_azure_storage_unlink_file', $result, $relative_path );
	}

	/**
	 * Check if file exits using $wp_filesystem.
	 *
	 * @since 4.0.0
	 *
	 * @param string $relative_path Relative path under uploads directory.
	 *
	 * @return bool
	 */
	static public function file_exists( $relative_path ) {
		/** @var $wp_filesystem \WP_Filesystem_Base */
		global $wp_filesystem;

		$exist = false;

		/**
		 * Make sure function is available to use, avoid Fatal on image cropping.
		 */
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		if ( WP_Filesystem() ) {
			$upload_dir = self::wp_upload_dir();
			$filename   = $upload_dir['uploads'] . DIRECTORY_SEPARATOR . $relative_path;
			$exist = $wp_filesystem->exists( $filename, false, 'f' );
		}

		return apply_filters( 'windows_azure_storage_file_exist', $exist, $relative_path );
	}

	/**
	 * Returns an array containing the current upload directory's path and url.
	 *
	 * @since 4.2.0
	 *
	 * @static
	 * @access public
	 *
	 * @param string $time Optional. Time formatted in 'yyyy/mm'. Default null.
	 *
	 * @return array
	 */
	static public function wp_upload_dir( $time = null ) {
		static $wp_upload_dir = array();

		$blog_id = get_current_blog_id();
		if ( empty( $wp_upload_dir[ $blog_id ] ) || ! is_null( $time ) ) {
			$dir = $upload_path = trim( get_option( 'upload_path' ) );
			if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
				$dir = WP_CONTENT_DIR . '/uploads';
			} elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
				// $dir is absolute, $upload_path is (maybe) relative to ABSPATH
				$dir = path_join( ABSPATH, $upload_path );
			}

			$dir = rtrim( $dir, DIRECTORY_SEPARATOR );

			$wp_upload_dir[ $blog_id ] = call_user_func_array( 'wp_upload_dir', array( $time ) );
			$wp_upload_dir[ $blog_id ]['reldir'] = substr( $wp_upload_dir[ $blog_id ]['basedir'], strlen( $dir ) );
			$wp_upload_dir[ $blog_id ]['uploads'] = $dir;
		}

		return $wp_upload_dir[ $blog_id ];
	}

	/**
	 * Return formatted string for given blob.
	 *
	 * @param \MicrosoftAzure\Storage\Blob\Models\BlobProperties $blob_properties
	 *
	 * @return string
	 *
	 * @since 4.4.0
	 */
	public static function get_formatted_date_for_blob( $blob_properties ) {
		return sprintf( '%s %s', date_i18n( 'D, j M Y H:i:s',  $blob_properties->getLastModified()->getTimestamp() ), $blob_properties->getLastModified()->getTimezone()->getName() );
	}
}
