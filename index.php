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
        $this->_fixThings();
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

    protected function _fixThings()
    {
        function my_wp_title ($title, $sep) {
            global $paged, $page;

            if (is_feed()) {
                return $title;
            }

            # Add the site name.
            $title .= get_bloginfo( 'name' );

            # Add the site description for the home/front page.
            $site_description = get_bloginfo('description', 'display');
            if ($site_description and (is_home() || is_front_page())) {
                $title .= ' ' . $sep . ' ' . $site_description;
            }

            # Add a page number if necessary.
            if ($paged >= 2 or $page >= 2) {
                $title .= ' ' . $sep . ' ' . sprintf(__('Page %s'), max($paged, $page));
            }

            return $title;
        }
        add_filter('wp_title', 'my_wp_title', 10, 2);
    }
} 