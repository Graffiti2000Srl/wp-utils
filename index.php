<?php

class G2K_WP_Utils
{
    public $removeAdminBar = true;

    public function __construct()
    {
        define('WP_THEME_PATH', get_template_directory());
        define('WP_THEME_URI', get_template_directory_uri());
    }

    public function init()
    {
        $this->_showHideThings();
    }

    protected function _showHideThings()
    {
        if ($this->removeAdminBar) {
            add_filter('show_admin_bar', '__return_false');
        }
    }
} 