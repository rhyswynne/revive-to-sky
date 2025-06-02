<?php

/**
 * Register the Revive To Sky Options Menu
 * @return void
 */
function dr_rts_options()
{
    $options_suffix = add_options_page('Revive To Sky', 'Revive To Sky', 'manage_options', 'revivetosky', 'dt_rts_options_page');
    add_action('load-' . $options_suffix, 'dr_rts_enqueue_on_option_page');
}
add_action('admin_menu', 'dr_rts_options');


/**
 * Initialise the settings for Revive to Sky
 *
 * @return void
 */
function dr_rts_settings_init()
{
    register_setting('revive_to_sky', 'dr_rts_settings');

    add_settings_section(
        'dr_rts_admin_section',
        __('Bluesky Connection Settings', 'dr_rts'),
        'dr_rts_admin_section_callback',
        'revive_to_sky'
    );

    add_settings_field(
        'dr_rts_bluesky_handle',
        __('Bluesky Handle', 'preload_lcp'),
        'dr_rts_bluesky_handle_render',
        'revive_to_sky',
        'dr_rts_admin_section'
    );

    add_settings_field(
        'dr_rts_bluesky_app_password',
        __('App Password', 'preload_lcp'),
        'dr_rts_bluesky_app_password_render',
        'revive_to_sky',
        'dr_rts_admin_section'
    );


    add_settings_section(
        'dr_rts_message_section',
        __('Bluesky Message Settings', 'dr_rts'),
        'dr_rts_message_settings_callback',
        'revive_to_sky'
    );


    add_settings_field(
        'dr_rts_message_every_settings',
        __('Message Every...', 'preload_lcp'),
        'dr_rts_message_every_settings_render',
        'revive_to_sky',
        'dr_rts_message_section'
    );

    $categories = dr_rts_get_categories();

    if ($categories) {

        add_settings_field(
            'dr_rts_categories_to_include_settings',
            __('Categories to Include', 'preload_lcp'),
            'dr_rts_categories_to_include_render',
            'revive_to_sky',
            'dr_rts_message_section'
        );

        add_settings_field(
            'dr_rts_categories_to_exclude_settings',
            __('Categories to Exclude', 'preload_lcp'),
            'dr_rts_categories_to_exclude_render',
            'revive_to_sky',
            'dr_rts_message_section'
        );
    }

    $tags = dr_rts_get_tags();

    if ($tags) {

        add_settings_field(
            'dr_rts_tags_to_include_settings',
            __('Tags to Include', 'preload_lcp'),
            'dr_rts_tags_to_include_render',
            'revive_to_sky',
            'dr_rts_message_section'
        );

        add_settings_field(
            'dr_rts_tags_to_exclude_settings',
            __('Tags to Exclude', 'preload_lcp'),
            'dr_rts_tags_to_exclude_render',
            'revive_to_sky',
            'dr_rts_message_section'
        );
    }

    add_settings_field(
        'dr_rts_message_templates',
        __('Message Template', 'preload_lcp'),
        'dr_rts_message_templates_render',
        'revive_to_sky',
        'dr_rts_message_section'
    );
}
add_action('admin_init', 'dr_rts_settings_init');


/**
 * The admin settings section callback
 * 
 * @return void
 */
function dr_rts_admin_section_callback() {}


/**
 * The admin settings section callback
 * 
 * @return void
 */
function dr_rts_message_settings_callback() {}


/**
 * Render the Bluesky Handle option
 * 
 * @return void
 */
function dr_rts_bluesky_handle_render()
{

    $dr_rts_bluesky_handle = dr_rts_get_option('dr_rts_bluesky_handle');
?>
    <input type='text' name='dr_rts_settings[dr_rts_bluesky_handle]' value='<?php echo esc_attr($dr_rts_bluesky_handle); ?>'><br />
    <p class="description"><?php _e('Put your Bluesky Handle here.', 'dr_rts'); ?></p>
<?php
}


/**
 * Render the Bluesky App Password option
 * 
 * @return void
 */
function dr_rts_bluesky_app_password_render()
{

    $dr_rts_bluesky_app_password = dr_rts_get_option('dr_rts_bluesky_app_password');
?>
    <input type='text' name='dr_rts_settings[dr_rts_bluesky_app_password]' value='<?php echo esc_attr($dr_rts_bluesky_app_password); ?>' <br />
    <p class="description"><?php _e('Put your Bluesky App Password here. <strong>This is not your Bluesky Password!</strong> You can create a Bluesky App password <a href="https://bsky.app/settings/app-passwords" target="_blank">here</a>', 'dr_rts'); ?></p>
<?php
}





/**
 * Render the Bluesky Handle option
 * 
 * @return void
 */
function dr_rts_message_every_settings_render()
{

    $dr_rts_message_every_settings = dr_rts_get_option('dr_rts_message_every_settings');

    if (is_array($dr_rts_message_every_settings)) {

        $rate_number    =  array_key_exists('number', $dr_rts_message_every_settings) ? $dr_rts_message_every_settings['number'] : false;
        $rate_timeframe =  array_key_exists('timeframe', $dr_rts_message_every_settings) ? $dr_rts_message_every_settings['timeframe'] : false;
    } else {
        $rate_number = $rate_timeframe = false;
    }
?>

    <input type='number' name='dr_rts_settings[dr_rts_message_every_settings][number]' value='<?php echo esc_attr($rate_number); ?>' min='1' step="1">
    <select name='dr_rts_settings[dr_rts_message_every_settings][timeframe]'>
        <option value="hours" <?php selected($rate_timeframe, 'hours'); ?>><?php _e('Hours', 'dr_rts'); ?></option>
        <option value="days" <?php selected($rate_timeframe, 'days'); ?>><?php _e('Days', 'dr_rts'); ?></option>
        <option value="weeks" <?php selected($rate_timeframe, 'weeks'); ?>><?php _e('Weeks', 'dr_rts'); ?></option>
    </select>
    <p><?php _e('How often a message is sent to Bluesky.', 'dr_rts'); ?></p>
    <?php
}


/**
 * Render the categories to include on the options page.
 *
 * @return void
 */
function dr_rts_categories_to_include_render()
{
    $selected_categories = dr_rts_get_option('dr_rts_categories_to_include_settings');

    $categories = dr_rts_get_categories();

    echo '<div class="dr-checkbox-list">';

    foreach ($categories as $category) {
        $is_checked = is_array($selected_categories) && in_array($category->term_id, $selected_categories) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="dr_rts_settings[dr_rts_categories_to_include_settings][]"
                value="<?php echo esc_attr($category->term_id); ?>"
                <?php echo $is_checked; ?>>
            <?php echo esc_html($category->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php _e('Select which post categories for which to pull blog posts to post to Bluesky.', 'dr_rts'); ?></p>
    <?php
}


/**
 * Render the categories to exclude on the options page.
 *
 * @return void
 */
function dr_rts_categories_to_exclude_render()
{
    $selected_categories = dr_rts_get_option('dr_rts_categories_to_exclude_settings');

    $categories = dr_rts_get_categories();

    if (empty($categories)) {
        _e('No categories found.', 'dr_rts');
        return;
    }

    echo '<div class="dr-checkbox-list">';

    foreach ($categories as $category) {
        $is_checked = is_array($selected_categories) && in_array($category->term_id, $selected_categories) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="dr_rts_settings[dr_rts_categories_to_exclude_settings][]"
                value="<?php echo esc_attr($category->term_id); ?>"
                <?php echo $is_checked; ?>>
            <?php echo esc_html($category->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php _e('Select which post categories for which to exclude from posts to Bluesky.', 'dr_rts'); ?></p>
<?php
}


/**
 * Render the tags to include on the options page.
 *
 * @return void
 */
function dr_rts_tags_to_include_render()
{
    $selected_tags = dr_rts_get_option('dr_rts_tags_to_include_settings');

    $tags = dr_rts_get_tags();

    echo '<div class="dr-checkbox-list">';

    foreach ($tags as $tag) {
        $is_checked = is_array($selected_tags) && in_array($tag->term_id, $selected_tags) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="dr_rts_settings[dr_rts_tags_to_include_settings][]"
                value="<?php echo esc_attr($tag->term_id); ?>"
                <?php echo $is_checked; ?>>
            <?php echo esc_html($tag->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php _e('Select which post tags for which to pull blog posts to post to Bluesky.', 'dr_rts'); ?></p>
    <?php
}


/**
 * Render the tags to exclude on the options page.
 *
 * @return void
 */
function dr_rts_tags_to_exclude_render()
{
    $selected_tags = dr_rts_get_option('dr_rts_tags_to_exclude_settings');

    $tags = dr_rts_get_tags();

    echo '<div class="dr-checkbox-list">';

    foreach ($tags as $tag) {
        $is_checked = is_array($selected_tags) && in_array($tag->term_id, $selected_tags) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="dr_rts_settings[dr_rts_tags_to_exclude_settings][]"
                value="<?php echo esc_attr($tag->term_id); ?>"
                <?php echo $is_checked; ?>>
            <?php echo esc_html($tag->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php _e('Select which post categories for which to exclude from posts to Bluesky.', 'dr_rts'); ?></p>
    <?php
}


/**
 * Render the message template option.
 *
 * @return void
 */
function dr_rts_message_templates_render() {
    $dr_rts_message_to_send = dr_rts_get_option('dr_rts_message_to_send') ? dr_rts_get_option('dr_rts_message_to_send') : __( 'From the Archives: %%POSTTITLE%% - %%POSTURL%%' , 'dr_rts');

    ?>
    <textarea 
        name='dr_rts_settings[dr_rts_message_to_send]' 
        maxlength="300" 
        rows="4" 
        cols="50"
        class="large-text"
    ><?php echo esc_textarea($dr_rts_message_to_send); ?></textarea>
    <p class="description">
        <?php _e('Enter your message template. Use %%POSTTITLE%% for the post title and %%POSTURL%% for the post URL.', 'dr_rts'); ?>
    </p>
    <?php
}


/**
 * Create and add the Revive to Sky Options Page
 * 
 * @return void
 */
function dt_rts_options_page()
{

    $current_user = wp_get_current_user();

?>
    <div class="dr_admin_wrap">
        <h1><?php _e('Preload LCP Image Options', 'preload_lcp_image'); ?></h1>

        <div class="dr_admin_main_wrap">
            <div class="dr_admin_wrap_left">

                <form method="post" action="options.php" id="options">

                    <?php

                    settings_fields('revive_to_sky');
                    do_settings_sections('revive_to_sky');
                    submit_button();

                    ?>

                </form>
            </div>
            <div class="dr_admin_wrap_right">
                <div class="dr_box dr_box_highlighted">
                    <h2><?php _e('Is Your WordPress Site Slow?', 'preload_lcp'); ?></h2>
                    <p><img src="https://gravatar.com/avatar/13b432f781f24140731c6fe815e6d831?s=70&d=mm" alt="<?php _e('Rhys Wynne', 'preload_cp'); ?>" class="dr_avatar" />
                        <?php _e("Hello! Dwi'n Rhys (I am Rhys in Welsh), and I am an experienced WordPress developer from the United Kingdom, specialising in WordPress perfomance automation, as well as API integration, maintenance and custom code projects. Let's talk and see what I can do for you!", "preload_lcp"); ?>
                    </p>
                    <p><a href="https://dwinrhys.com/wordpress-speed-optimisation/?utm_source=plugin-options&utm_medium=wordpress&utm_campaign=preload-lcp-image" target="_blank" class="dr_button dr_button_primary"><?php _e("Get your site optimised!", "preload_lcp"); ?></a></p>
                </div>

                <div class="dr_box">
                    <?php dwinrhys_print_newsletter_box('revive_to_sky'); ?>
                </div>
            </div>
        </div>
    </div>

<?php

}

/**
 * Delete the last posted option value when the settings are updated.
 * 
 * @todo Maybe make start from that point.
 *
 * @param  array $old_value     The old value of the option.
 * @param  array $value         The new value of the option.
 * @return void
 */
function dr_rts_delete_last_posted_option( $old_value, $value ) {

    if ( !is_array($value) || !array_key_exists('number', $value) || !array_key_exists('timeframe', $value) ) {
        return;
    }

    if ( !array_key_exists('number', $old_value) || !array_key_exists('timeframe', $old_value) ) {
        return;
    }

    if ( $value['number'] === $old_value['number'] && $value['timeframe'] === $old_value['timeframe'] ) {
        return;
    }

    // If the settings have changed, delete the last post posted option
    delete_option('dr_rts_last_post_posted');
    
} add_action('update_option_dr_rts_message_every_settings', 'dr_rts_delete_last_posted_option', 10, 2);
