<?php

class G2K_WP_Utils
{
    public $removeAdminBar = false;
    public $removeComments = false;
    public $removePosts    = false;

    public $debugRouting = false;

    public function __construct()
    {
        define('WP_THEME_PATH', get_template_directory());
        define('WP_THEME_URI', get_template_directory_uri());
    }

    public function init()
    {
        $this->_showHideThings();
        $this->_fixThings();

        $this->_debugThings();
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

        if ($this->removePosts) {
            add_action('admin_menu', 'wputils_post_remove');
            function wputils_post_remove () {
                remove_menu_page('edit.php');
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

    protected function _debugThings()
    {
        if ($this->debugRouting) {
            function debug_404_rewrite_dump (&$wp) {
                global $wp_rewrite;

                echo '<pre>';
                echo '<h2>rewrite rules</h2>';
                echo var_export($wp_rewrite->wp_rewrite_rules(), true);

                echo '<h2>permalink structure</h2>';
                echo var_export($wp_rewrite->permalink_structure, true);

                echo '<h2>page permastruct</h2>';
                echo var_export($wp_rewrite->get_page_permastruct(), true);

                echo '<h2>matched rule and query</h2>';
                echo var_export($wp->matched_rule, true);

                echo '<h2>matched query</h2>';
                echo var_export($wp->matched_query, true);

                echo '<h2>request</h2>';
                echo var_export($wp->request, true);

                global $wp_the_query;
                echo '<h2>the query</h2>';
                echo var_export($wp_the_query, true);
            }
            add_action('parse_request', 'debug_404_rewrite_dump');

            function debug_404_template_redirect() {
                global $wp_filter;
                echo '<h2>template redirect filters</h2>';
                echo var_export($wp_filter[current_filter()], true);
            }
            add_action('template_redirect', 'debug_404_template_redirect', 99999);

            function debug_404_template_dump( $template ) {
                echo '<h2>template file selected</h2>';
                echo var_export($template, true);

                exit();
            }
            add_filter ('template_include', 'debug_404_template_dump');
        }
    }

    public static function get_the_excerpt($post_id, $excerpt_length = 35, $line_breaks = true)
    {
        $the_post = get_post($post_id);
        $the_excerpt = $the_post->post_excerpt ?: $the_post->post_content;

        $the_excerpt = apply_filters('the_excerpt', $the_excerpt);
        $the_excerpt = $line_breaks ?
            strip_tags(strip_shortcodes($the_excerpt), '<p><br>') :
            strip_tags(strip_shortcodes($the_excerpt));

        $words = explode(' ', $the_excerpt, $excerpt_length + 1);
        if(count($words) > $excerpt_length) {
            array_pop($words);
            array_push($words, '…');
            $the_excerpt = implode(' ', $words);
            $the_excerpt = $line_breaks ? $the_excerpt . '</p>' : $the_excerpt;
        }

        $the_excerpt = trim($the_excerpt);

        return $the_excerpt;
    }
} 
