<?php
error_reporting( E_ALL );
ini_set( 'display_errors', 1 );

class Settings {

    /** If the site is at the root, set this to "/". Otherwise,
        Put the full path to get from root to the website"
    **/
    const path_from_root = "/personal/";

    const site_title = "Glen Nelson";

    /** Database connection details. Do not use the defaults.
     **/
    const user_name = 'gral_cms';
    const password = 'password';
    const database = 'gral_cms';

    /** MEDIUMBLOB can hold up to 16 MB (16,777,216 bytes).
        Put how many bytes you want as the maximum size here.
     **/
    const max_file_size = 2097152;
}
?>