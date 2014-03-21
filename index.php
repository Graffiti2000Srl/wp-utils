<?php

class G2K_WP_Utils
{
    public $removeAdminBar = false;
    public $removeComments = false;

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

        if ($this->removeComments) {
            # Removes comments from admin menu
            add_action('admin_menu', 'my_remove_admin_menus');
            function my_remove_admin_menus () {
                remove_menu_page('edit-comments.php');
            }

            # Removes comments from post and pages
            add_action('init', 'remove_comment_support', 100);
            function remove_comment_support() {
                remove_post_type_support('post', 'comments');
                remove_post_type_support('page', 'comments');
            }

            # Removes comments from admin bar
            add_action('wp_before_admin_bar_render', 'mytheme_admin_bar_render');
            function mytheme_admin_bar_render() {
                global $wp_admin_bar;

                $wp_admin_bar->remove_menu('comments');
            }
        }
    }
} 