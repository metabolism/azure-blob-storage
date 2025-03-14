<?php

class Azure_Blob_Image_Editor_Imagick extends WP_Image_Editor_Imagick
{
    public function save( $destfilename = null, $mime_type = null ) {

        $saved = parent::save( $destfilename, $mime_type );

        if( !is_wp_error( $saved ) )
            do_action('wp_save_file', $saved['path'],  $saved['mime-type']);

        return $saved;
    }

    public function make_subsize( $size_data ) {

        $saved = parent::make_subsize( $size_data );

        $dir = pathinfo( $this->file, PATHINFO_DIRNAME );

        if( !is_wp_error( $saved ) )
            do_action('wp_save_file', $dir.'/'.$saved['file'],  $saved['mime-type']);

        return $saved;
    }
}