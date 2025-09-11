<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Register the Revive To Sky Options Menu
 * @return void
 */
function revivetosky_options()
{
    $options_suffix = add_options_page('Revive To Sky', 'Revive To Sky', 'manage_options', 'revivetosky', 'revivetosky_options_page');
    add_action('load-' . $options_suffix, 'revivetosky_enqueue_on_option_page');
}
add_action('admin_menu', 'revivetosky_options');


/**
 * Initialise the settings for Revive to Sky
 *
 * @return void
 */
function revivetosky_settings_init()
{
    register_setting('revive_to_sky', 'revivetosky_settings', array(
        'sanitize_callback' => 'revivetosky_sanitize_settings',
    ));

    add_settings_section(
        'revivetosky_admin_section',
        __('Bluesky Connection Settings', 'revive-to-sky'),
        'revivetosky_admin_section_callback',
        'revive_to_sky'
    );

    add_settings_field(
        'revivetosky_bluesky_handle',
        __('Bluesky Handle', 'revive-to-sky'),
        'revivetosky_bluesky_handle_render',
        'revive_to_sky',
        'revivetosky_admin_section'
    );

    add_settings_field(
        'revivetosky_bluesky_app_password',
        __('App Password', 'revive-to-sky'),
        'revivetosky_bluesky_app_password_render',
        'revive_to_sky',
        'revivetosky_admin_section'
    );


    add_settings_section(
        'revivetosky_message_section',
        __('Bluesky Message Settings', 'revive-to-sky'),
        'revivetosky_message_settings_callback',
        'revive_to_sky'
    );


    add_settings_field(
        'revivetosky_message_every_settings',
        __('Message Every...', 'revive-to-sky'),
        'revivetosky_message_every_settings_render',
        'revive_to_sky',
        'revivetosky_message_section'
    );

    $categories = revivetosky_get_categories();

    if ($categories) {

        add_settings_field(
            'revivetosky_categories_to_include_settings',
            __('Categories to Include', 'revive-to-sky'),
            'revivetosky_categories_to_include_render',
            'revive_to_sky',
            'revivetosky_message_section'
        );

        add_settings_field(
            'revivetosky_categories_to_exclude_settings',
            __('Categories to Exclude', 'revive-to-sky'),
            'revivetosky_categories_to_exclude_render',
            'revive_to_sky',
            'revivetosky_message_section'
        );
    }

    $tags = revivetosky_get_tags();

    if ($tags) {

        add_settings_field(
            'revivetosky_tags_to_include_settings',
            __('Tags to Include', 'revive-to-sky'),
            'revivetosky_tags_to_include_render',
            'revive_to_sky',
            'revivetosky_message_section'
        );

        add_settings_field(
            'revivetosky_tags_to_exclude_settings',
            __('Tags to Exclude', 'revive-to-sky'),
            'revivetosky_tags_to_exclude_render',
            'revive_to_sky',
            'revivetosky_message_section'
        );
    }

    add_settings_field(
        'revivetosky_message_to_send',
        __('Message Template', 'revive-to-sky'),
        'revivetosky_message_templates_render',
        'revive_to_sky',
        'revivetosky_message_section'
    );
}
add_action('admin_init', 'revivetosky_settings_init');


/**
 * The admin settings section callback
 * 
 * @return void
 */
function revivetosky_admin_section_callback() {}


/**
 * The admin settings section callback
 * 
 * @return void
 */
function revivetosky_message_settings_callback() {}


/**
 * Render the Bluesky Handle option
 * 
 * @return void
 */
function revivetosky_bluesky_handle_render()
{

    $revivetosky_bluesky_handle = revivetosky_get_option('revivetosky_bluesky_handle');
?>
    <input type='text' name='revivetosky_settings[revivetosky_bluesky_handle]' value='<?php echo esc_attr($revivetosky_bluesky_handle); ?>'><br />
    <p class="description"><?php esc_html_e('Put your Bluesky Handle here.', 'revive-to-sky'); ?></p>
<?php
}


/**
 * Render the Bluesky App Password option
 * 
 * @return void
 */
function revivetosky_bluesky_app_password_render()
{

    $revivetosky_bluesky_app_password = revivetosky_get_option('revivetosky_bluesky_app_password');
?>
    <input type='text' name='revivetosky_settings[revivetosky_bluesky_app_password]' value='<?php echo esc_attr($revivetosky_bluesky_app_password); ?>' <br />
    <p class="description">
        <?php
        printf(
            wp_kses(
                /* translators: %s: URL to Bluesky app passwords page */
                __( 'Put your Bluesky App Password here. <strong>This is not your Bluesky Password!</strong> You can create a Bluesky App password <a href="%s" target="_blank">here</a>', 'revive-to-sky' ),
                array(
                    'strong' => array(),
                    'a'      => array(
                        'href'   => array(),
                        'target' => array(),
                    ),
                )
            ),
            'https://bsky.app/settings/app-passwords'
        );
        ?>
    </p>
<?php
}





/**
 * Render the Bluesky Handle option
 * 
 * @return void
 */
function revivetosky_message_every_settings_render()
{

    $revivetosky_message_every_settings = revivetosky_get_option('revivetosky_message_every_settings');

    if (is_array($revivetosky_message_every_settings)) {

        $rate_number    =  array_key_exists('number', $revivetosky_message_every_settings) ? $revivetosky_message_every_settings['number'] : false;
        $rate_timeframe =  array_key_exists('timeframe', $revivetosky_message_every_settings) ? $revivetosky_message_every_settings['timeframe'] : false;
    } else {
        $rate_number = $rate_timeframe = false;
    }
?>

    <input type='number' name='revivetosky_settings[revivetosky_message_every_settings][number]' value='<?php echo esc_attr($rate_number); ?>' min='1' step="1">
    <select name='revivetosky_settings[revivetosky_message_every_settings][timeframe]'>
        <option value="hours" <?php selected($rate_timeframe, 'hours'); ?>><?php esc_html_e('Hours', 'revive-to-sky'); ?></option>
        <option value="days" <?php selected($rate_timeframe, 'days'); ?>><?php esc_html_e('Days', 'revive-to-sky'); ?></option>
        <option value="weeks" <?php selected($rate_timeframe, 'weeks'); ?>><?php esc_html_e('Weeks', 'revive-to-sky'); ?></option>
    </select>
    <p><?php esc_html_e('How often a message is sent to Bluesky.', 'revive-to-sky'); ?></p>
    <?php
}


/**
 * Render the categories to include on the options page.
 *
 * @return void
 */
function revivetosky_categories_to_include_render()
{
    $selected_categories = revivetosky_get_option('revivetosky_ategories_to_include_settings');

    $categories = revivetosky_get_categories();

    echo '<div class="dr-checkbox-list">';

    foreach ($categories as $category) {
        $is_checked = is_array($selected_categories) && in_array($category->term_id, $selected_categories) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="revivetosky_settings[revivetosky_categories_to_include_settings][]"
                value="<?php echo esc_attr($category->term_id); ?>"
                <?php echo esc_attr( $is_checked ); ?>>
            <?php echo esc_html($category->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php esc_html_e('Select which post categories for which to pull blog posts to post to Bluesky.', 'revive-to-sky'); ?></p>
    <?php
}


/**
 * Render the categories to exclude on the options page.
 *
 * @return void
 */
function revivetosky_categories_to_exclude_render()
{
    $selected_categories = revivetosky_get_option('revivetosky_categories_to_exclude_settings');

    $categories = revivetosky_get_categories();

    if (empty($categories)) {
        esc_html_e('No categories found.', 'revive-to-sky');
        return;
    }

    echo '<div class="dr-checkbox-list">';

    foreach ($categories as $category) {
        $is_checked = is_array($selected_categories) && in_array($category->term_id, $selected_categories) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="revivetosky_settings[revivetosky_categories_to_exclude_settings][]"
                value="<?php echo esc_attr($category->term_id); ?>"
                <?php echo esc_attr( $is_checked ); ?>>
            <?php echo esc_html($category->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php esc_html_e('Select which post categories for which to exclude from posts to Bluesky.', 'revive-to-sky'); ?></p>
    <?php
}


/**
 * Render the tags to include on the options page.
 *
 * @return void
 */
function revivetosky_tags_to_include_render()
{
    $selected_tags = revivetosky_get_option('revivetosky_tags_to_include_settings');

    $tags = revivetosky_get_tags();

    echo '<div class="dr-checkbox-list">';

    foreach ($tags as $tag) {
        $is_checked = is_array($selected_tags) && in_array($tag->term_id, $selected_tags) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="revivetosky_settings[revivetosky_tags_to_include_settings][]"
                value="<?php echo esc_attr($tag->term_id); ?>"
                <?php echo esc_attr( $is_checked ); ?>>
            <?php echo esc_html($tag->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php esc_html_e('Select which post tags for which to pull blog posts to post to Bluesky.', 'revive-to-sky'); ?></p>
    <?php
}


/**
 * Render the tags to exclude on the options page.
 *
 * @return void
 */
function revivetosky_tags_to_exclude_render()
{
    $selected_tags = revivetosky_get_option('revivetosky_tags_to_exclude_settings');

    $tags = revivetosky_get_tags();

    echo '<div class="dr-checkbox-list">';

    foreach ($tags as $tag) {
        $is_checked = is_array($selected_tags) && in_array($tag->term_id, $selected_tags) ? 'checked' : '';
    ?>
        <label>
            <input type="checkbox"
                name="revivetosky_settings[revivetosky_tags_to_exclude_settings][]"
                value="<?php echo esc_attr($tag->term_id); ?>"
                <?php echo esc_attr( $is_checked ); ?>>
            <?php echo esc_html($tag->name); ?>
        </label><br>
    <?php
    }

    echo '</div>';
    ?>
    <p class="description"><?php esc_html_e('Select which post categories for which to exclude from posts to Bluesky.', 'revive-to-sky'); ?></p>
<?php
}


/**
 * Render the message template option.
 *
 * @return void
 */
function revivetosky_message_templates_render()
{
    $revivetosky_message_to_send = revivetosky_get_option('revivetosky_message_to_send') ? revivetosky_get_option('revivetosky_message_to_send') : __('From the Archives: %%POSTTITLE%% - %%POSTURL%%', 'revive-to-sky');
?>
    <textarea
        name='revivetosky_settings[revivetosky_message_to_send]'
        maxlength="300"
        rows="4"
        cols="50"
        class="large-text"><?php echo esc_textarea($revivetosky_message_to_send); ?></textarea>
    <p class="description">
        <?php esc_html_e('Enter your message template. Use %%POSTTITLE%% for the post title and %%POSTURL%% for the post URL.', 'revive-to-sky'); ?>
    </p>
<?php
}


/**
 * Create and add the Revive to Sky Options Page
 * 
 * @return void
 */
function revivetosky_options_page()
{

    $current_user = wp_get_current_user();

?>
    <div class="dr_admin_wrap">
        <h1><?php esc_html_e('Revive to Sky Options', 'revive-to-sky'); ?></h1>

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
                    <h2><?php esc_html_e('Custom WordPress Development for the masses', 'revive-to-sky'); ?></h2>
                    <p><img src="https://gravatar.com/avatar/13b432f781f24140731c6fe815e6d831?s=70&d=mm" alt="<?php esc_html_e('Rhys Wynne', 'revive-to-sky'); ?>" class="dr_avatar" />
                        <?php esc_html_e("Hello! Dwi'n Rhys (I am Rhys in Welsh), and I am a WordPress developer from the United Kingdom with over 15+ years of Commercial WordPress experience. I love working with WordPress and helping businesses grow online. Let's talk and see what I can do for you!", "revive-to-sky"); ?>
                    </p>
                    <p><a href="https://dwinrhys.com/custom-wordpress-development/?utm_source=plugin-options&utm_medium=wordpress&utm_campaign=revive-to-sky" target="_blank" class="dr_button dr_button_primary"><?php esc_html_e("Let's Talk!", "revive-to-sky"); ?></a></p>
                </div>

                <div class="dr_box">
                    <?php revivetosky_print_newsletter_box('revive_to_sky'); ?>
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
function revivetosky_delete_last_posted_option($old_value, $value)
{

    if (!is_array($value) || !array_key_exists('number', $value) || !array_key_exists('timeframe', $value)) {
        return;
    }

    if (!array_key_exists('number', $old_value) || !array_key_exists('timeframe', $old_value)) {
        return;
    }

    if ($value['number'] === $old_value['number'] && $value['timeframe'] === $old_value['timeframe']) {
        return;
    }

    // If the settings have changed, delete the last post posted option
    delete_option('revivetosky_last_post_posted');
}
add_action('update_option_revivetosky_message_every_settings', 'revivetosky_delete_last_posted_option', 10, 2);



/**
 * Sanitize the plugin settings
 *
 * @param array $input The settings input array.
 * @return array Sanitized settings array
 */
function revivetosky_sanitize_settings($input) {
    $sanitized = array();

    // Bluesky Handle - sanitize as text
    if (isset($input['revivetosky_bluesky_handle'])) {
        $sanitized['revivetosky_bluesky_handle'] = sanitize_text_field($input['revivetosky_bluesky_handle']);
    }

    // App Password - sanitize as text
    if (isset($input['revivetosky_bluesky_app_password'])) {
        $sanitized['revivetosky_bluesky_app_password'] = sanitize_text_field($input['revivetosky_bluesky_app_password']);
    }

    // Message Every Settings - sanitize number and timeframe
    if (isset($input['revivetosky_message_every_settings'])) {
        $sanitized['revivetosky_message_every_settings'] = array(
            'number' => absint($input['revivetosky_message_every_settings']['number']),
            'timeframe' => sanitize_text_field($input['revivetosky_message_every_settings']['timeframe'])
        );
    }

    // Categories to Include - sanitize array of IDs
    if (isset($input['revivetosky_categories_to_include_settings'])) {
        $sanitized['revivetosky_categories_to_include_settings'] = array_map('absint', (array) $input['revivetosky_categories_to_include_settings']);
    }

    // Categories to Exclude - sanitize array of IDs
    if (isset($input['revivetosky_categories_to_exclude_settings'])) {
        $sanitized['revivetosky_categories_to_exclude_settings'] = array_map('absint', (array) $input['revivetosky_categories_to_exclude_settings']);
    }

    // Tags to Include - sanitize array of IDs
    if (isset($input['revivetosky_tags_to_include_settings'])) {
        $sanitized['revivetosky_tags_to_include_settings'] = array_map('absint', (array) $input['revivetosky_tags_to_include_settings']);
    }

    // Tags to Exclude - sanitize array of IDs
    if (isset($input['revivetosky_tags_to_exclude_settings'])) {
        $sanitized['revivetosky_tags_to_exclude_settings'] = array_map('absint', (array) $input['revivetosky_tags_to_exclude_settings']);
    }

    // Message Template - sanitize textarea
    if (isset($input['revivetosky_message_to_send'])) {
        $sanitized['revivetosky_message_to_send'] = sanitize_textarea_field($input['revivetosky_message_to_send']);
    }

    return $sanitized;
}
