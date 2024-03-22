<?php
/**
 * Plugin Name: Simple Social Previews
 * Plugin URI: http://www.jonathanbriehl.com
 * Description: Adds meta tags to WordPress site header to create Twitter summary cards and Facebook previews.
 * Version: 1.0.3
 * Author: Jonathan Briehl
 * Author URI: http://www.jonathanbriehl.com
 * License: GPL2
 */

/**  Copyright 2020  Jonathan Briehl
 *
 *  This program is free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License, version 2, as
 *  published by the Free Software Foundation.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program; if not, write to the Free Software
 *  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if (version_compare(PHP_VERSION, '5.3', '<')) {
    add_action('admin_notices', 'jb_social_previews_requirements');
    add_action('admin_init', 'jb_social_previews_deactivate_self');
    return;
}

/**
 * Display requirements error message.
 */
function jb_social_previews_requirements()
{
    ?>
    <div class='error'>
        <p><?php esc_html_e('Plugin Name requires PHP 5.3 to function properly. Please upgrade PHP. The Plugin has been auto-deactivated.', 'jb_social_previews'); ?></p>
    </div>
    <?php
    if (isset($_GET['activate'])) {
        unset($_GET['activate']);
    }
}

/**
 * Auto Deactivate Plugin
 */
function jb_social_previews_deactivate_self()
{
    deactivate_plugins(plugin_basename(__FILE__));
}

/**
 * Adds Social Previews plugin to Settings menu as a sub-menu item
 */
function jb_social_previews_menu()
{
    add_options_page(
        __('Social Previews', 'jb_social_previews'),  // Page Title.
        __('Social Previews', 'jb_social_previews'),  // Menu Title.
        'manage_options',                    // Capability.
        'jb-social-previews',               // Menu Slug.
        'jb_social_previews_settings_page'    // Callback Function.
    );
}

add_action('admin_menu', 'jb_social_previews_menu');

/**
 * Register plugin options
 */
if (!function_exists('jb_social_previews_settings_init')) {
    function jb_social_previews_settings_init()
    {
        register_setting('jb_social_previews_settings', 'jb_social_previews_twitter_on');
        register_setting('jb_social_previews_settings', 'jb_social_previews_twitter_use_large');
        register_setting('jb_social_previews_settings', 'jb_social_previews_twitter_username');
        register_setting('jb_social_previews_settings', 'jb_social_previews_facebook_on');
        register_setting('jb_social_previews_settings', 'jb_social_previews_title_site_name');
        register_setting('jb_social_previews_settings', 'jb_social_previews_image_url');
    }

    add_action('admin_init', 'jb_social_previews_settings_init');
}

/**
 * Displays the page content for the Social Previews options
 */
if (!function_exists('jb_social_previews_settings_page')) {
    function jb_social_previews_settings_page()
    {
        // Must check that the user has the required capability.
        if (!current_user_can('manage_options')) {
            wp_die(esc_html__('You do not have sufficient permissions to access this page.', 'jb_social_previews'));
        }

        // Enqueue jQuery.
        wp_enqueue_script('jquery');

        // This will enqueue the Media Uploader script.
        wp_enqueue_media();

        $plugin_url = plugin_dir_url(__FILE__);
        wp_enqueue_style('jb-social-previews', $plugin_url . 'style.css');

        // Get current settings
        $twitter_on = get_option('jb_social_previews_twitter_on', '');
        $twitter_use_large_image = get_option('jb_social_previews_twitter_use_large', '');
        $twitter_username = get_option('jb_social_previews_twitter_username', '');
        $facebook_on = get_option('jb_social_previews_facebook_on', '');
        $title_site_name = get_option('jb_social_previews_title_site_name', '');
        $img_url = get_option('jb_social_previews_image_url', '');
        ?>
        <div class="jb-social-previews-settings-container"> <!-- container -->
            <div class="jb-social-previews-content-container"> <!-- content -->

                <h1>
                    <?php esc_html_e('Simple Social Previews', 'jb_social_previews'); ?>
                </h1>

                <p>
                    The Simple Social Previews adds the appropriate meta data fields to your site header to enable
                    Facebook previews when sharing a link on Facebook. It also enables Twitter cards on any tweets
                    that include a link to your website. You can read more about Twitter cards
                    <a href="https://dev.twitter.com/cards/overview" target="_blank">here</a>.
                </p>

                <p>
                    As a bonus, you can override the default settings and turn Twitter cards on/off, select Twitter card
                    size, turn Facebook preview on/off, add/remove site title, and create custom titles.
                </p>

                <div class="jb-social-previews-info-alert">
                    <strong>Please Note</strong><br/>
                    A new field has been added on the user profile page. It is labeled "Twitter Username (for Twitter
                    Cards)." When a page or post is loaded, the plugin will look to see if the Twitter username is
                    filled in for the author of that page. If so, that user's username will be listed as the 'creator'
                    of the post. If a Twitter username is not provided for that user, the default site username is used.
                </div>

                <hr/>

                <form name="jb_social_previews_form" method="post" action="options.php">
                    <?php settings_fields('jb_social_previews_settings'); ?>

                    <h3><?php esc_html_e('Turn on Twitter Cards', 'jb_social_previews'); ?></h3>
                    <div>
                        <label>
                            <input type="checkbox" name="jb_social_previews_twitter_on"
                                   value="on" <?php checked($twitter_on, 'on'); ?>>
                            <?php esc_html_e(' Use Twitter cards on your site?', 'jb_social_previews'); ?>
                        </label>
                    </div>
                    <div>
                        <label>
                            <input type="checkbox" name="jb_social_previews_twitter_use_large"
                                   value="on" <?php checked($twitter_use_large_image, 'on'); ?>>
                            <?php esc_html_e(' Use large cards? By default, small cards are used.', 'jb_social_previews'); ?>
                        </label>
                    </div>
                    <hr/>

                    <h3><?php esc_html_e('Site Twitter username', 'jb_social_previews'); ?></h3>
                    <p>
                        @ <input type="text" name="jb_social_previews_twitter_username"
                                 value="<?php echo esc_attr($twitter_username); ?>" size="20">
                        <br/><small><?php esc_html_e(' Do not include the @ sign', 'jb_social_previews'); ?></small>
                    </p>
                    <hr/>

                    <h3><?php esc_html_e('Turn on Facebook Previews', 'jb_social_previews'); ?></h3>
                    <p>
                        <label>
                            <input type="checkbox" name="jb_social_previews_facebook_on"
                                   value="on" <?php checked($facebook_on, 'on'); ?>>
                            <?php esc_html_e(' Use Facebook previews on your site?', 'jb_social_previews'); ?>
                        </label>
                    </p>
                    <hr/>

                    <h3><?php esc_html_e('Default options', 'jb_social_previews'); ?></h3>
                    <p>
                        <label>
                            <input type="checkbox" name="jb_social_previews_title_site_name"
                                   value="on" <?php checked($title_site_name, 'on'); ?>>
                            <?php esc_html_e(' Use site title in shared link title? eg: ', 'jb_social_previews'); ?>
                            <i><?php esc_html_e('Post Title - ', 'jb_social_previews'); ?><?php echo get_bloginfo('title') ?></i>
                        </label>
                    </p>

                    <div class="image-preview">
                        <div class="image" id="share_image_preview">
                            <?php if (!filter_var($img_url, FILTER_VALIDATE_URL) === false) : ?>
                                <img src="<?php echo esc_url($img_url); ?>" style="width: 100%;"/>
                            <?php endif; ?>
                        </div>
                        <div>
                            <label for="image_url">Default Share Image</label><br/>
                            <input type="text" name="jb_social_previews_image_url" id="image_url" class="regular-text"
                                   value="<?php echo esc_url($img_url); ?>" size="20">
                            <?php submit_button(__('Upload Image', 'jb_social_previews'), 'secondary', 'upload-btn', false); ?>
                            <br/>
                            <small>
                                This is the image that will be used if your homepage is shared or if a post/page is
                                shared
                                that does not have a featured image.
                            </small>
                        </div>
                    </div>

                    <script type="text/javascript">
                        jQuery(document).ready(function ($) {
                            $('#upload-btn').click(function (e) {
                                e.preventDefault();
                                var image = wp.media({
                                    title: 'Upload Image',
                                    // mutiple: true if you want to upload multiple files at once
                                    multiple: false
                                }).open()
                                    .on('select', function (e) {
                                        // This will return the selected image from the Media Uploader, the result is an object
                                        var uploaded_image = image.state().get('selection').first();
                                        // We convert uploaded_image to a JSON object to make accessing it easier
                                        // Output to the console uploaded_image
                                        console.log(uploaded_image);
                                        var image_url = uploaded_image.toJSON().url;
                                        // Let's assign the url value to the input field
                                        $('#image_url').val(image_url);
                                        $('#share_image_preview').html('<img src="' + image_url + '" style="width: 100%;" />');
                                    });
                            });
                        });
                    </script>
                    <?php submit_button(__('Update Options', 'jb_social_previews')); ?>
                </form>

            </div><!-- end content -->
        </div> <!-- end container -->
        <?php
    } // End settings page.
}

/** Add "Settings" link on the plugin page */
if (!function_exists('jb_social_preview_plugin_settings_link')) {
    function jb_social_preview_plugin_settings_link($links)
    {
        $settings_link = '<a href="options-general.php?page=jb-social-previews">Settings</a>';
        array_unshift($links, $settings_link);
        return $links;
    }

    $plugin = plugin_basename(__FILE__);
    add_filter("plugin_action_links_$plugin", 'jb_social_preview_plugin_settings_link');
}

/** Loads Twitter card and Facebook preview to header */
if (!function_exists('jb_social_preview_header')) {
    function jb_social_preview_header()
    {
        $jb_social_previews_twitter_on = get_option('jb_social_previews_twitter_on');
        $jb_social_previews_twitter_use_large = get_option('jb_social_previews_twitter_use_large');
        $jb_social_previews_twitter_username = get_option('jb_social_previews_twitter_username');
        $jb_social_previews_facebook_on = get_option('jb_social_previews_facebook_on');
        $jb_social_previews_title_site_name = get_option('jb_social_previews_title_site_name');
        $jb_social_previews_image_url = get_option('jb_social_previews_image_url');

        if (is_single()) {
            $post = get_post(get_the_ID());
            $post_twitter_preference = get_post_meta($post->ID, "_twitter_card_size", true);
            $post_facebook_preference = get_post_meta($post->ID, "_facebook_preview", true);
            $post_site_title_preference = get_post_meta($post->ID, "_site_title", true);
            $custom_post_title = get_post_meta($post->ID, "_custom_title", true);

            if ($post_twitter_preference == 'small') {
                $jb_social_previews_twitter_use_large = 'off';
            } elseif ($post_twitter_preference == 'large') {
                $jb_social_previews_twitter_use_large = 'on';
            } elseif ($post_twitter_preference == 'none') {
                $jb_social_previews_twitter_on = 'off';
            }

            if ($post_facebook_preference == 'yes') {
                $jb_social_previews_facebook_on = 'on';
            } elseif ($post_facebook_preference == 'no') {
                $jb_social_previews_facebook_on = 'off';
            }

            if ($post_site_title_preference == 'yes') {
                $jb_social_previews_title_site_name = 'on';
            } elseif ($post_site_title_preference == 'no') {
                $jb_social_previews_title_site_name = 'off';
            }
        }

        if (isset($custom_post_title) && strlen($custom_post_title) > 0) {
            $post_title = esc_html($custom_post_title);
        } else {
            if (is_front_page()) {
                $post_title = get_bloginfo('title');
            } else {
                $post_title = get_the_title();
            }
        }

        if ($jb_social_previews_title_site_name == 'on' && !is_front_page()) {
            $post_title .= ' - ' . get_bloginfo('title');
        }

        if ($jb_social_previews_twitter_on == 'on') {

            echo "\n";

            if ($jb_social_previews_twitter_use_large == 'on') {
                ?>
                <meta name="twitter:card" content="summary_large_image"><?php
                echo "\n";
            } else {
                ?>
                <meta name="twitter:card" content="summary"><?php
                echo "\n";
            }

            if (isset($jb_social_previews_twitter_username) && strlen($jb_social_previews_twitter_username) > 0) {
                ?>
                <meta name="twitter:site" content="@<?php echo esc_html($jb_social_previews_twitter_username); ?>"><?php
                echo "\n";
            }

            if (is_single() or is_page()) {
                $post = get_post(get_the_ID());
                $author_id = $post->post_author;
                if (strlen(get_the_author_meta('social_previews_twitter_username', $author_id)) > 0) {
                    ?>
                    <meta name="twitter:creator"
                          content="@<?php echo esc_html(get_the_author_meta('social_previews_twitter_username', $author_id)); ?>"><?php
                    echo "\n";
                } else {
                    if (isset($jb_social_previews_twitter_username) && strlen($jb_social_previews_twitter_username) > 0) {
                        ?>
                        <meta name="twitter:creator"
                              content="@<?php echo esc_html($jb_social_previews_twitter_username); ?>"><?php
                        echo "\n";
                    }
                }
            } else {
                if (isset($jb_social_previews_twitter_username) && strlen($jb_social_previews_twitter_username) > 0) {
                    ?>
                    <meta name="twitter:creator"
                          content="@<?php echo esc_html($jb_social_previews_twitter_username); ?>"><?php
                    echo "\n";
                }
            }

            ?>
            <meta name="twitter:title" content="<?php echo $post_title; ?>"><?php
            echo "\n";

            ?>
            <meta name="twitter:description" content="<?php if (is_front_page()) {
                echo esc_html(get_bloginfo('description'));
            } else {
                echo esc_html(strip_tags(jb_social_plugins_custom_excerpt(100, $post->ID, false)));
            } ?>"><?php
            echo "\n";

            if (has_post_thumbnail() && !is_front_page()) {
                ?>
                <meta name="twitter:image"
                      content="<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>"><?php
            } elseif (isset($jb_social_previews_image_url) && strlen($jb_social_previews_image_url) > 0) {
                ?>
                <meta name="twitter:image" content="<?php echo esc_html($jb_social_previews_image_url); ?>"><?php
            }
            echo "\n";
        }

        if ($jb_social_previews_facebook_on == 'on') {
            echo "\n";
            ?>
            <meta property="og:title" content="<?php echo $post_title; ?>" /><?php
            echo "\n";

            ?>
            <meta property="og:site_name" content="<?php bloginfo('title'); ?>"/><?php
            echo "\n";

            ?>
            <meta property="og:url"  content="<?php if (is_front_page()) {
                echo esc_url(site_url());
            } else {
                esc_url(the_permalink());
            } ?>" /><?php
            echo "\n";

            ?>
            <meta property="og:description"  content="<?php if (is_front_page()) {
                echo esc_html(get_bloginfo('description'));
            } else {
                echo esc_html(strip_tags(jb_social_plugins_custom_excerpt(100, $post->ID, false)));
            } ?>" /><?php
            echo "\n";

            if (has_post_thumbnail() && !is_front_page()) {
                ?>
                <meta property="og:image"
                      content="<?php echo wp_get_attachment_url(get_post_thumbnail_id($post->ID)); ?>" /><?php
                echo "\n";
            } elseif (isset($jb_social_previews_image_url) && strlen($jb_social_previews_image_url) > 0) {
                ?>
                <meta property="og:image" content="<?php echo esc_html($jb_social_previews_image_url); ?>" /><?php
                echo "\n";
            }
        }

    }

    add_action('wp_head', 'jb_social_preview_header', 25);
}

/**
 * Update User Profile Links
 */
if (!function_exists('jb_social_previews_modify_contact_methods')) {
    function jb_social_previews_modify_contact_methods($profile_fields)
    {
        // Add new fields
        $profile_fields['social_previews_twitter_username'] = 'Twitter Username<br /><small>(for Social Previews)</small>';

        return $profile_fields;
    }

    add_filter('user_contactmethods', 'jb_social_previews_modify_contact_methods');
}

/** UPLOAD ENGINE **/
if (!function_exists('load_wp_media_files')) {
    function load_wp_media_files()
    {
        wp_enqueue_media();
    }

    add_action('admin_enqueue_scripts', 'load_wp_media_files');
}

/**
 * Register meta box(es).
 */
if (!function_exists('jb_social_previews_register_meta_boxes')) {
    function jb_social_previews_register_meta_boxes()
    {
        $post_types = array('post', 'page');
        add_meta_box('meta-box-id', esc_html__('Social Preview Options', 'jb_social_previews'), 'jb_social_previews_callback', $post_types, 'side', 'low');
    }

    add_action('add_meta_boxes', 'jb_social_previews_register_meta_boxes');
}

/**
 * Meta box display callback for posts and pages.
 *
 * @param WP_Post $post Current post object.
 */
function jb_social_previews_callback($post)
{
    global $post;

    // Noncename needed to verify where the data originated
    echo '<input type="hidden" name="socialpreviewmeta_noncename" id="socialpreviewmeta_noncename" value="' .
        wp_create_nonce(plugin_basename(__FILE__)) . '" />';

    // Get the location data if its already been entered
    $twitter_card_size = get_post_meta($post->ID, '_twitter_card_size', true);
    $facebook_preview = get_post_meta($post->ID, '_facebook_preview', true);
    $site_title = get_post_meta($post->ID, '_site_title', true);
    $custom_title = get_post_meta($post->ID, '_custom_title', true);

    // Echo out the field
    ?>
    <div style="max-width: 216px;">
        <label for="_twitter_card_size" style="margin-bottom: 4px; display: inline-block;"><strong>Twitter Card
                Size</strong></label>
        <select name="_twitter_card_size" id="_twitter_card_size" class="widefat">
            <option value="default" <?php selected($twitter_card_size, 'default'); ?>>Default</option>
            <option value="small" <?php selected($twitter_card_size, 'small'); ?>>Small</option>
            <option value="large" <?php selected($twitter_card_size, 'large'); ?>>Large</option>
            <option value="none" <?php selected($twitter_card_size, 'none'); ?>>Do not use card</option>
        </select>

        <br/><br/>

        <label for="_facebook_preview" style="margin-bottom: 4px; display: inline-block;"><strong>Facebook
                Preview</strong></label>
        <select name="_facebook_preview" id="_facebook_preview" class="widefat">
            <option value="default" <?php selected($facebook_preview, 'default'); ?>>Default</option>
            <option value="yes" <?php selected($facebook_preview, 'yes'); ?>>Yes</option>
            <option value="no" <?php selected($facebook_preview, 'no'); ?>>No</option>
        </select>

        <br/><br/>

        <label for="_site_title" style="margin-bottom: 4px; display: inline-block;"><strong>Use Site Title on Shared
                Link</strong></label>
        <select name="_site_title" id="_site_title" class="widefat">
            <option value="default" <?php selected($site_title, 'default'); ?>>Default</option>
            <option value="yes" <?php selected($site_title, 'yes'); ?>>Yes</option>
            <option value="no" <?php selected($site_title, 'no'); ?>>No</option>
        </select>

        <br/><br/>
        <label for="_custom_title" style="margin-bottom: 4px; display: inline-block;"><strong>Custom
                Title</strong></label>
        <input type="text" name="_custom_title" id="_custom_title" value="<?php echo $custom_title; ?>"
               class="widefat"/>
    </div>
    <?php
    // echo '<input type="text" name="_twitter_card_size" value="' . $twitter_card_size  . '" class="widefat" />';
}

/**
 * Save meta box content.
 */
function jb_social_previews_save_meta_box($post_id)
{
    global $post;

    if (!isset($post) or !isset($_POST['socialpreviewmeta_noncename']))
    {
        return null;
    }

    // verify this came from our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (!wp_verify_nonce($_POST['socialpreviewmeta_noncename'], plugin_basename(__FILE__))) {
        return $post->ID;
    }

    // Is the user allowed to edit the post or page?
    if (!current_user_can('edit_post', $post->ID))
        return $post->ID;

    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.

    $social_previews_meta['_twitter_card_size'] = htmlspecialchars(stripslashes($_POST['_twitter_card_size']));
    $social_previews_meta['_facebook_preview'] = htmlspecialchars(stripslashes($_POST['_facebook_preview']));
    $social_previews_meta['_site_title'] = htmlspecialchars(stripslashes($_POST['_site_title']));
    $social_previews_meta['_custom_title'] = htmlspecialchars(stripslashes($_POST['_custom_title']));

    // Add values of $events_meta as custom fields

    foreach ($social_previews_meta as $key => $value) { // Cycle through the $events_meta array!
        if ($post->post_type == 'revision') return; // Don't store custom data twice
        $value = implode(',', (array)$value); // If $value is an array, make it a CSV (unlikely)
        if (get_post_meta($post->ID, $key, FALSE)) { // If the custom field already has a value
            update_post_meta($post->ID, $key, $value);
        } else { // If the custom field doesn't have a value
            add_post_meta($post->ID, $key, $value);
        }
        if (!$value) delete_post_meta($post->ID, $key); // Delete if blank
    }
}

add_action('save_post', 'jb_social_previews_save_meta_box');

/**
 * Load plugin textdomain.
 */
function jb_social_previews_load_textdomain()
{
    load_plugin_textdomain('jb_social_previews', false, plugin_basename(__DIR__) . DIRECTORY_SEPARATOR . 'languages');
}

add_action('plugins_loaded', 'jb_social_previews_load_textdomain');

/**
 * Custom Excerpt Length
 */
if (!function_exists('jb_social_plugins_custom_excerpt')) {
    function jb_social_plugins_custom_excerpt($excerpt_length = 55, $id = false, $echo = true)
    {

        $text = '';

        if ($id) {
            $the_post = &get_post($my_id = $id);
            $text = ($the_post->post_excerpt) ? $the_post->post_excerpt : $the_post->post_content;
        } else {
            global $post;
            $text = ($post->post_excerpt) ? $post->post_excerpt : get_the_content('');
        }

        $text = strip_shortcodes($text);
        $text = apply_filters('the_content', $text);
        $text = str_replace(']]>', ']]&gt;', $text);
        // $text = strip_tags($text);
        $text = preg_replace('/<[^>]*>/', '', $text);

        $excerpt_more = ' ' . '  ...';
        $words = preg_split("/[\n\r\t ]+/", $text, $excerpt_length + 1, PREG_SPLIT_NO_EMPTY);
        if (count($words) > $excerpt_length) {
            array_pop($words);
            $text = implode(' ', $words);
            $text = $text . $excerpt_more;
        } else {
            $text = implode(' ', $words);
        }
        if ($echo) {
            echo apply_filters('the_content', $text);
        } else {
            return $text;
        }
    }
}

/* Check For Plugin Updates - host hashed for privacy */
require plugin_dir_path(__FILE__) . '/plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
    'https://code.jonathanbriehl.com/wordpress-plugins/jb-social-previews/update.php?domain=' . md5($_SERVER['HTTP_HOST']),
    __FILE__, //Full path to the main plugin file or functions.php.
    'jb-social-previews'
);