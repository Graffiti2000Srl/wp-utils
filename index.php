<?php

class G2K_WP_Utils
{
    public function __construct()
    {
        define('WP_THEME_PATH', get_template_directory());
        define('WP_THEME_URI', get_template_directory_uri());
    }
} 