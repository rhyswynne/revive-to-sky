<?php

/**
 * Wrapper function to get one option field
 *
 * @param  string $option_field  The option to return
 * @return mixed                 The option we've returned, or false
 */
function dr_rts_get_option($option_field)
{
    $all_options = get_option('dr_rts_settings');

    if (is_array($all_options)) {
        if (array_key_exists($option_field, $all_options)) {
            $option = $all_options[$option_field];
        } else {
            $option = false;
        }
    } else {
        $option = false;
    }

    return $option;
}


/**
 * Get all Categories in an array.
 * 
 * Wrapper function, returns false if empty
 *
 * @return mixed    array if we have 1 category, false if not.
 */
function dr_rts_get_categories()
{
    $categories = get_categories(array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    if (empty($categories)) {
        return false;
    }

    return $categories;
}

/**
 * Get all Tags in an array.
 * 
 * Wrapper function, returns false if empty
 *
 * @return mixed    array if we have 1 category, false if not.
 */
function dr_rts_get_tags()
{
    $categories = get_tags(array(
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC'
    ));

    if (empty($categories)) {
        return false;
    }

    return $categories;
}

/**
 * Function to build the Newsletter box
 * 
 * Here so it isn't in the way. Also allows more than one of my plugins to be loaded on the same blog without any issues.
 */
if (!function_exists('dwinrhys_print_newsletter_box')) {
    function dwinrhys_print_newsletter_box()
    {
?>
        <div id="mlb2-21162946" class="ml-form-embedContainer ml-subscribe-form ml-subscribe-form-21162946">
            <div class="ml-form-align-center ">
                <div class="ml-form-embedWrapper embedForm">
                    <div class="ml-form-embedBody ml-form-embedBodyDefault row-form">

                        <div class="ml-form-embedContent" style=" ">

                            <h2><?php esc_html_e('Get Notified for Updates', 'revive-to-sky'); ?></h2>
                            <p><?php esc_html_e('Sign up below to receive updates to this plugin, as well as a monthly newsletter on SEO and WordPress news. Subscription is free and you can unsubscribe at any time.', 'revive-to-sky'); ?></p>

                        </div>

                        <form class="ml-block-form" action="https://assets.mailerlite.com/jsonp/609353/forms/142144868274144703/subscribe" data-code="" method="post" target="_blank">
                            <div class="ml-form-formContent">



                                <div class="ml-form-fieldRow ">
                                    <div class="ml-field-group ml-field-name">




                                        <!-- input -->
                                        <input aria-label="name" type="text" class="form-control" data-inputmask="" name="fields[name]" placeholder="Name" autocomplete="given-name">
                                        <!-- /input -->

                                        <!-- textarea -->

                                        <!-- /textarea -->

                                        <!-- select -->

                                        <!-- /select -->

                                        <!-- checkboxes -->

                                        <!-- /checkboxes -->

                                        <!-- radio -->

                                        <!-- /radio -->

                                        <!-- countries -->

                                        <!-- /countries -->





                                    </div>
                                </div>
                                <div class="ml-form-fieldRow ml-last-item">
                                    <div class="ml-field-group ml-field-email ml-validate-email ml-validate-required">




                                        <!-- input -->
                                        <input aria-label="email" aria-required="true" type="email" class="form-control" data-inputmask="" name="fields[email]" placeholder="Email" autocomplete="email">
                                        <!-- /input -->

                                        <!-- textarea -->

                                        <!-- /textarea -->

                                        <!-- select -->

                                        <!-- /select -->

                                        <!-- checkboxes -->

                                        <!-- /checkboxes -->

                                        <!-- radio -->

                                        <!-- /radio -->

                                        <!-- countries -->

                                        <!-- /countries -->





                                    </div>
                                </div>

                            </div>



                            <!-- Privacy policy -->

                            <!-- /Privacy policy -->

                            <input type="hidden" name="ml-submit" value="1">

                            <div class="ml-form-embedSubmit">

                                <button type="submit" class="primary dr_button dr_button_primary"><?php esc_html_e('Subscribe', 'revive-to-sky'); ?></button>

                                <button disabled="disabled" style="display: none;" type="button dr_button dr_button_disabled" class="loading">
                                    <div class="ml-form-embedSubmitLoad"></div>
                                    <span class="sr-only"><?php esc_html_e('Loading...', 'revive-to-sky'); ?></span>
                                </button>
                            </div>


                            <input type="hidden" name="anticsrf" value="true">
                        </form>
                    </div>

                    <div class="ml-form-successBody row-success" style="display: none">

                        <div class="ml-form-successContent">

                            <h2><?php esc_html_e('Thank you!', 'revive-to-sky'); ?></h2>

                            <p><?php esc_html_e('Thank you for your interest! To confirm, I have sent an email to you. Please click on that to subscribe!', 'revive-to-sky'); ?></p>


                        </div>

                    </div>
                </div>
            </div>
        </div>





        <?php /* <script>
            function ml_webform_success_21162946() {
                var $ = ml_jQuery || jQuery;
                $('.ml-subscribe-form-21162946 .row-success').show();
                $('.ml-subscribe-form-21162946 .row-form').hide();
            }
        </script>


        
        <script>
            fetch("https://assets.mailerlite.com/jsonp/609353/forms/142144868274144703/takel")
        </script> */ ?>
<?php
    }
}

/**
 * Get the post to post to Bluesky
 *
 * @param  $numberofposts  Optional - number of posts to return. I used the -1 for debugging.
 * @return mixed 
 */
function dr_rts_get_post_to_post_to_bluesky( $numberofposts = 1 )
{
    $included_categories = dr_rts_get_option('dr_rts_categories_to_include_settings');
    $excluded_categories = dr_rts_get_option('dr_rts_categories_to_exclude_settings');
    $included_tags       = dr_rts_get_option('dr_rts_tags_to_include_settings');
    $excluded_tags       = dr_rts_get_option('dr_rts_tags_to_exclude_settings');

    $post_args = array(
        'post_type'      => 'post',
        'orderby'        => 'rand',
        'posts_per_page' => $numberofposts,
        'ignore_sticky_posts' => 1
    );

    if ($included_categories) {
        $post_args['category__in'] = $included_categories;
       
    }

    if ($excluded_categories) {
        $post_args['category__not_in'] = $excluded_categories;
    }

    if ($included_tags) {
        $post_args['tag__in'] = $included_tags;
    }

    if ($excluded_tags) {
        $post_args['tag__not_in'] = $excluded_tags;
    }

    $post_to_skeet = new WP_Query($post_args);

    if ($post_to_skeet->have_posts()) {
        $post_to_skeet->the_post();
        return get_post();
    } else {
        return false;
    }
}


/**
 * Get the URLs in the post
 *
 * @param  string $text    The Skeet we are using
 * @return array  $urldata An array of all URLs
 */
function dt_rts_get_urls($text)
{

    $regex = '/(https?:\/\/[^\s]+)/';
    preg_match_all($regex, $text, $matches, PREG_OFFSET_CAPTURE);

    $urlData = array();

    foreach ($matches[0] as $match) {
        $url = $match[0];
        $start = $match[1];
        $end = $start + strlen($url);

        $urlData[] = array(
            'start' => $start,
            'end' => $end,
            'url' => $url,
        );
    }

    return $urlData;
}


function dt_rts_create_link_card($post_id, $access_token)
{
    // The required fields for every embed card
    $card = [
        "uri" => get_the_permalink($post_id),
        "title" => get_the_title($post_id),
        "description" => get_the_excerpt($post_id),
    ];

    $embed = array();
    $image = false;

    if (has_post_thumbnail($post_id)) {
        $image_id = get_post_thumbnail_id($post_id);
        $image = dr_bts_upload_media_to_bluesky($image_id, $access_token);
    }

    if ($image) {
        $embed = [
            'embed' => [
                '$type' => 'app.bsky.embed.external',
                'external' => [
                    'uri' => $card['uri'],
                    'title' => $card['title'],
                    'description' => $card['description'],
                    'thumb' => $image,
                ],
            ],
        ];
    } else {
        $embed = [
            'embed' => [
                '$type' => 'app.bsky.embed.external',
                'external' => [
                    'uri' => $card['uri'],
                    'title' => $card['title'],
                    'description' => $card['description']
                ],
            ],
        ];
    }

    return $embed;
}
