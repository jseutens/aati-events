<?php
/*
Plugin Name: AATI Events
Plugin URI: https://github.com/jseutens/aati-events
Description: A custom plugin to create a new post type 'aati_events' and add additional fields.
Version: 1.1
Author: Johan Seutens
Author URI: https://www.aati.be
Text Domain: aati-events
Domain Path: /languages/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/
// Check if the ABSPATH constant is defined
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Define constants used throughout the plugin
define( 'AATIEVENTS_PLUGIN_DIR', untrailingslashit( plugin_dir_path( __FILE__ ) ) );
define( 'AATIEVENTS_PLUGIN_URL', untrailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'AATIEVENTS_PLUGIN_FNAME', plugin_basename( __FILE__ ) );
define( 'AATIEVENTS_PLUGIN_DIRNAME', plugin_basename( dirname( __FILE__ ) ) );
define( 'AATIEVENTS_VERSION', '1.0.0' );
define( 'AATIEVENTS_TEXTDOMAIN', 'aati-events');
// load languages
	function aatievents_load_textdomain() {
		load_plugin_textdomain(AATIEVENTS_TEXTDOMAIN,false, AATIEVENTS_PLUGIN_DIRNAME. '/languages');
	}
	add_action( 'plugins_loaded', 'AATIEVENTS_load_textdomain');
	

// Activation hook
register_activation_hook( __FILE__, 'aatievents_activate' );
function aatievents_activate() {
  // Activation code here
}

// Deactivation hook
register_deactivation_hook( __FILE__, 'aatievents_deactivate' );
function aatievents_deactivate() {
  // Deactivation code here
}

// Register the uninstall hook
register_uninstall_hook(__FILE__, 'aatievents_uninstall');
function aatievents_uninstall()
{
    require_once plugin_dir_path(__FILE__) . 'uninstall.php';
}

//

function aati_events_register_post_type() {
	$slug = get_option('aati_events_slug', 'event');
    $labels = array(
        'name'               => __('Events', 'aati-events'),
        'singular_name'      => __('Event', 'aati-events'),
        'add_new'            => __('Add New', 'aati-events'),
        'add_new_item'       => __('Add New Event', 'aati-events'),
        'edit_item'          => __('Edit Event', 'aati-events'),
        'new_item'           => __('New Event', 'aati-events'),
        'all_items'          => __('All Events', 'aati-events'),
        'view_item'          => __('View Event', 'aati-events'),
        'search_items'       => __('Search Events', 'aati-events'),
        'not_found'          => __('No events found', 'aati-events'),
        'not_found_in_trash' => __('No events found in Trash', 'aati-events'),
        'menu_name'          => __('Events', 'aati-events')
    );

    $args = array(
        'labels'             => $labels,
        'public'             => true,
        'publicly_queryable' => true,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'query_var'          => true,
        'rewrite' => array('slug' => $slug),
		'capability_type'    => 'post',
        'has_archive'        => true,
        'hierarchical'       => false,
        'menu_position'      => null,
        'supports'           => array('title','editor','excerpt','custom-fields','revisions','thumbnail','author','page-attributes',)
    );

    register_post_type('aati_event', $args);
}
add_action('init', 'aati_events_register_post_type',20);


function aati_events_info_page() {
    add_submenu_page(
        'edit.php?post_type=aati_event',
        __('Information Page', 'aati-events'),
        __('Information Page', 'aati-events'),
        'manage_options',
        'aati-events-info-page',
        'aati_events_info_page_content'
    );
}
add_action('admin_menu', 'aati_events_info_page');

function aati_events_info_page_content() {
    if (!current_user_can('manage_options')) {
        return;
    }
    ?>
    <div class="wrap">
        <h1><?php _e('AATI Events Information Page', 'aati-events'); ?></h1>
        <h2><?php _e('These are the Event dynamic data field name', 'aati-events'); ?></h2>
        <p><?php _e('The title of the event', 'aati-events'); ?></p>
        <code>{post_title}</code>
        <p><?php _e('The content of the event', 'aati-events'); ?></p>
        <code>{post_content}</code>
        <p><?php _e('The featured image of the event', 'aati-events'); ?></p>
        <code>{featured_image}</code>
        <p><?php _e('Link to the event.', 'aati-events'); ?></p>
        <code>{cf__aati_event_link}</code>
        <p><?php _e('Start day of the event.', 'aati-events'); ?></p>
        <code>{cf__aati_event_start_date}</code>
        <p><?php _e('End date of the event.', 'aati-events'); ?></p>
        <code>{cf__aati_event_end_date}</code>
        <p><?php _e('The address of the event, in one line', 'aati-events'); ?></p>
        <code>{cf__aati_event_address}</code>
        <p><?php _e('The address of the event, with breaklines', 'aati-events'); ?></p>
        <code>{cf__aati_event_address_formatted}</code>
        <p><?php _e('To link to the calendar link so people can add the event to their calendar.', 'aati-events'); ?></p>
        <code>{cf__aati_event_ics_url}</code>
        <p><?php _e('To display the date according to the setting in WordPress.', 'aati-events'); ?></p>
        <code>{echo:aati_events_format_date({cf__aati_event_start_date})}</code>
        <p><?php _e('To display the dates together in one row but without repeating the same year or month.', 'aati-events'); ?></p>
        <code>{echo:aati_events_format_event_date({cf__aati_event_start_date},{cf__aati_event_end_date})}</code>
	    <p><?php _e('To use in the META filter Query loop : META KEY / META VALUE .', 'aati-events'); ?></p>	
        <code>_aati_event_start_date_timestamp</code> 
		<code>{echo:aati_event_today}</code><?php //echo aati_event_today(); ?>
		
	    <p><?php _e('Event slug.', 'aati-events'); ?></p>
        <code>{cf__aati_event_slug}</code>	
		
    </div>
    <?php
}

function aati_event_today() {
	    $aati_events_current_date = date("Y-m-d H:i:s");
		$aati_event_timestamp = strtotime($aati_events_current_date);
	return $aati_event_timestamp;
}


function aati_events_add_meta_boxes() {
    $post_types = array('aati_event'); // Add 'post' to the array of post types
    foreach ($post_types as $post_type) {
        add_meta_box(
            'aati_events_meta_box',
            __('Event Details', 'aati-events'),
            'aati_events_meta_box_callback',
            $post_type,
            'normal',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'aati_events_add_meta_boxes');


function aati_events_meta_box_callback($post) {
    wp_nonce_field('aati_events_save_meta_box_data', 'aati_events_meta_box_nonce');

    // Get the current values of the custom fields
    $link = get_post_meta($post->ID, '_aati_event_link', true);
    $start_date = get_post_meta($post->ID, '_aati_event_start_date', true);
    $end_date = get_post_meta($post->ID, '_aati_event_end_date', true);
    $address = get_post_meta($post->ID, '_aati_event_address', true);


	echo '<table>';
	
    echo '<tr><td><label for="aati_event_link">' . __('Link (more info or buy tickets):', 'aati-events') . '</label></td>';
    echo '<td><input type="text" id="aati_event_link" name="aati_event_link" value="' . esc_attr($link) . '" size="50" /></td></tr>';

    echo '<tr><td><label for="aati_event_start_date">' . __('Start Date:', 'aati-events') . '</label></td>';
    echo '<td><input type="date" id="aati_event_start_date" name="aati_event_start_date" value="' . esc_attr($start_date) . '" /></td></tr>';

    echo '<tr><td><label for="aati_event_end_date">' . __('End Date:', 'aati-events') . '</label></td>';
    echo '<td><input type="date" id="aati_event_end_date" name="aati_event_end_date" value="' . esc_attr($end_date) . '" /></td></tr>';

    echo '<tr><td><label for="aati_event_address">' . __('Address:', 'aati-events') . '</label></td>';
    echo '<td><textarea id="aati_event_address" name="aati_event_address" rows="4" cols="50">' . esc_textarea($address) . '</textarea></td></tr>';

 	echo '<tr><td><label for="aati_event_ics">' . __('Calendar:', 'aati-events') . '</label></td>';	
    // After your existing code, retrieve the .ics URL from post meta and display the link:
    $ics_url = get_post_meta($post->ID, '_aati_event_ics_url', true);
    if (!empty($ics_url)) {
        echo '<td><a href="' . esc_url($ics_url) . '" download>Add to calendar</a></td></tr>';
    } else {
		echo '<td>' . __('Calendar No Link:', 'aati-events') . '</td></tr>';
	}
    echo '</table>';
}

function aati_events_save_meta_box_data($post_id) {
    if (!isset($_POST['aati_events_meta_box_nonce'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['aati_events_meta_box_nonce'], 'aati_events_save_meta_box_data')) {
        return;
    }

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $post = get_post($post_id); // Get the post object

    $link = sanitize_text_field($_POST['aati_event_link']);
    $start_date = sanitize_text_field($_POST['aati_event_start_date']);
    $end_date = sanitize_text_field($_POST['aati_event_end_date']);
    $address = sanitize_textarea_field($_POST['aati_event_address']);
    $formatted_address = nl2br($address);
// in WP format for comparing 
    $start_wp_date = aati_events_format_date($start_date);
    $end_wp_date = aati_events_format_date($end_date);
// in timestamp for comparing in queries	
	$start_date_timestamp = strtotime($start_date);
	$end_date_timestamp = strtotime($end_date);
	// Check if slug has changed, if so delete old .ics file
    $old_slug = get_post_meta($post_id, '_aati_event_slug', true);
    if ($old_slug !== $post->post_name) {
        if (file_exists(plugin_dir_path( __FILE__ ) . "events/{$old_slug}.ics")) {
            unlink(plugin_dir_path( __FILE__ ) . "events/{$old_slug}.ics");
        }
    }

    // Generate new .ics file and save path in post meta
    if (!empty($start_date) && !empty($end_date)) {
        $ics_data = generate_ics_data($start_date, $end_date, $_POST['post_title'], $_POST['post_content'], $address);
        $file_path = plugin_dir_path( __FILE__ ) . "events/{$post->post_name}.ics";

        file_put_contents($file_path, $ics_data);
        update_post_meta($post_id, '_aati_event_ics_url', plugin_dir_url( __FILE__ ) . "events/{$post->post_name}.ics");
    }

    update_post_meta($post_id, '_aati_event_link', $link);
    update_post_meta($post_id, '_aati_event_wp_start_date', $start_wp_date);
    update_post_meta($post_id, '_aati_event_start_date', $start_date);
	update_post_meta($post_id, '_aati_event_wp_end_date', $end_wp_date);
    update_post_meta($post_id, '_aati_event_start_date_timestamp', $start_date_timestamp);
    update_post_meta($post_id, '_aati_event_end_date_timestamp', $end_date_timestamp);	
    update_post_meta($post_id, '_aati_event_end_date', $end_date);	
    update_post_meta($post_id, '_aati_event_address', $address);
    update_post_meta($post_id, '_aati_event_address_formatted', $formatted_address);
    // Save new slug in post meta
    update_post_meta($post_id, '_aati_event_slug', $post->post_name);
}

add_action('save_post', 'aati_events_save_meta_box_data');

function aati_events_add_to_calendar_link($post_id) {
    $start_date = get_post_meta($post_id, '_aati_event_start_date', true);
    $end_date = get_post_meta($post_id, '_aati_event_end_date', true);
    $title = get_the_title($post_id);
    $details = wp_strip_all_tags(get_post_field('post_content', $post_id));

    $base_url = "https://www.google.com/calendar/render?action=TEMPLATE";

    $url = $base_url . "&text=" . urlencode($title) . "&dates=" . date('Ymd\THis', strtotime($start_date)) . "/" . date('Ymd\THis', strtotime($end_date)) . "&details=" . urlencode($details);

    return $url;
}

// this adds the custom fields in a non bricks way to the post content  so currently diabled but i keep the code
function aati_events_add_custom_fields_to_content($content) {
    if (get_post_type() === 'aati_event') {
        $link = get_post_meta(get_the_ID(), '_aati_event_link', true);
        $start_date = get_post_meta(get_the_ID(), '_aati_event_start_date', true);
        $end_date = get_post_meta(get_the_ID(), '_aati_event_end_date', true);

        // Get WordPress date format
        $date_format = get_option('date_format');

        // Format the start and end dates using the WordPress date format
		$formatted_start_date = aati_events_format_date($start_date);
		$formatted_end_date = aati_events_format_date($end_date);

        $custom_fields = '<p><strong>' . __('Link:', 'aati-events') . '</strong> <a href="' . esc_url($link) . '">' . esc_url($link) . '</a></p>';
        $custom_fields .= '<p><strong>' . __('Start Date:', 'aati-events') . '</strong> ' . esc_html($formatted_start_date) . '</p>';
        $custom_fields .= '<p><strong>' . __('End Date:', 'aati-events') . '</strong> ' . esc_html($formatted_end_date) . '</p>';

        $content .= $custom_fields;
    }

    return $content;
}
// disabled
//add_filter('the_content', 'aati_events_add_custom_fields_to_content');

function aati_events_format_date($date) {
    $date_format = get_option('date_format');
    $formatted_date = date_i18n($date_format, strtotime($date));
    return $formatted_date;
}
// make sure we can change the slug to use for the events / exhibitions 
function aati_events_settings_page() {
    add_submenu_page(
        'edit.php?post_type=aati_event',
        __('AATI Events Settings', 'aati-events'),
        __('Settings', 'aati-events'),
        'manage_options',
        'aati-events-settings',
        'aati_events_settings_page_content'
    );
}
add_action('admin_menu', 'aati_events_settings_page');

function aati_events_format_event_date($start_date, $end_date) {
    // Create DateTime objects from the start and end dates
    $start_date_obj = new DateTime($start_date);
    $end_date_obj = new DateTime($end_date);

    $formatted_date = '';

    if ($start_date_obj->format('Y') == $end_date_obj->format('Y')) {
        // Same year
        if ($start_date_obj->format('m') == $end_date_obj->format('m')) {
            // Same month
            if ($start_date_obj->format('d') == $end_date_obj->format('d')) {
                // Same day
                $formatted_date = $start_date_obj->format('j F Y');
            } else {
                // Different day
                $formatted_date = $start_date_obj->format('j') . ' - ' . $end_date_obj->format('j F Y');
            }
        } else {
            // Different month
            $formatted_date = $start_date_obj->format('j F') . ' - ' . $end_date_obj->format('j F Y');
        }
    } else {
        // Different year
        $formatted_date = $start_date_obj->format('j F Y') . ' - ' . $end_date_obj->format('j F Y');
    }

    return $formatted_date;
}


function aati_events_settings_page_content() {
    if (!current_user_can('manage_options')) {
        return;
    }

    if (isset($_POST['aati_events_slug']) && wp_verify_nonce($_POST['aati_events_nonce'], 'aati_events_save_slug')) {
        update_option('aati_events_slug', sanitize_text_field($_POST['aati_events_slug']));
    }

    $slug = get_option('aati_events_slug', 'event');
    ?>
    <div class="wrap">
        <h1><?php _e('AATI Events Settings', 'aati-events'); ?></h1>
        <form method="post">
            <?php wp_nonce_field('aati_events_save_slug', 'aati_events_nonce'); ?>
            <table class="form-table">
                <tr valign="top">
                    <th scope="row"><?php _e('Events Slug', 'event_slug'); ?></th>
                    <td><input type="text" name="aati_events_slug" value="<?php echo esc_attr($slug); ?>" /></td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

function aati_events_admin_scripts($hook) {
    $current_screen = get_current_screen();

    if ($current_screen->id === 'aati_event') {
        wp_enqueue_script('aati_events_admin', AATIEVENTS_PLUGIN_URL . '/assets/js/admin.js', array('jquery'), '1.0', true);
    }
}
add_action('admin_enqueue_scripts', 'aati_events_admin_scripts');

function generate_ics_file($start_date, $end_date, $summary, $description, $location, $event_id) {
    $ics_data = generate_ics_data($start_date, $end_date, $summary, $description, $location);
    $file_path = plugin_dir_path( __FILE__ ) . "events/event_{$event_id}.ics";

    // Ensure the events directory exists
    if (!is_dir(plugin_dir_path( __FILE__ ) . "events")) {
        mkdir(plugin_dir_path( __FILE__ ) . "events");
    }

    file_put_contents($file_path, $ics_data);

    // Return the URL to the .ics file
    return plugin_dir_url( __FILE__ ) . "events/event_{$event_id}.ics";
}

function generate_ics_data($start_date, $end_date, $summary, $description, $location) {
	$description = str_replace("\n", "\\n ", $description);
    $location = str_replace("\n", "\\n ", $location);
    $ics_data = "BEGIN:VCALENDAR\n";
    $ics_data .= "VERSION:2.0\n";
    $ics_data .= "PRODID:-//hacksw/handcal//NONSGML v1.0//EN\n";
    $ics_data .= "BEGIN:VEVENT\n";

    $start_date_ics = date('Ymd\THis\Z', strtotime($start_date));
    $end_date_ics = $end_date ? date('Ymd\THis\Z', strtotime($end_date)) : $start_date_ics;
    
    $ics_data .= "DTSTART:$start_date_ics\n";
    $ics_data .= "DTEND:$end_date_ics\n";
    $ics_data .= "SUMMARY:$summary\n";
    $ics_data .= "DESCRIPTION:$description\n";
    $ics_data .= "LOCATION:$location\n";
    $ics_data .= "END:VEVENT\n";
    $ics_data .= "END:VCALENDAR\n";

    return $ics_data;
}

// Add the 'Duplicate' link in the row actions on the admin page
function aati_events_duplicate_link($actions, $post) {
    if ($post->post_type=='event' && current_user_can('edit_posts')) {
        $actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_event&post=' . $post->ID, basename(__FILE__), 'duplicate_nonce') . '" title="Duplicate this event" rel="permalink">Duplicate</a>';
    }
    return $actions;
}
add_filter('post_row_actions', 'aati_events_duplicate_link', 10, 2);
// Handle the duplicate action
function aati_events_duplicate_event() {
    // Check if the 'duplicate_event' action is set
    if (!isset($_REQUEST['action']) || 'duplicate_event' != $_REQUEST['action']) {
        return;
    }

    // Check if a post is specified
    if (!(isset($_GET['post']) || isset($_POST['post']))) {
        wp_die('No event to duplicate has been supplied!');
    }

    // Nonce verification
    if (!isset($_GET['duplicate_nonce']) || !wp_verify_nonce($_GET['duplicate_nonce'], basename(__FILE__))) {
        return;
    }

    $post_id = (isset($_GET['post']) ? absint($_GET['post']) : absint($_POST['post']));
    $post = get_post($post_id);

    // Create a duplicate post
    $new_post_id = wp_insert_post(
        array(
            'post_title'    => $post->post_title . ' (Copy)',
            'post_type'     => $post->post_type,
            'post_status'   => 'draft',
            'post_content'  => $post->post_content,
        )
    );

    // Get the post meta
    $post_meta = get_post_meta($post_id);

    // Copy the post meta to the new post
    foreach ($post_meta as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($new_post_id, $key, $value);
        }
    }

    // Redirect to the new post edit page
    wp_redirect(admin_url('post.php?action=edit&post=' . $new_post_id));
    exit();
}
add_action('admin_init', 'aati_events_duplicate_event');






/* Add new query type controls to query options */
add_filter( 'bricks/setup/control_options', 'aati_events_setup_query_controls');
function aati_events_setup_query_controls( $control_options ) {

    /* Adding new options in the dropdown */
    $control_options['queryTypes']['aati_events_current'] = esc_html__( 'Current Events' );
	$control_options['queryTypes']['aati_events_future'] = esc_html__( 'Future Events' );

    return $control_options;

};

/* Run new query if option selected */
add_filter( 'bricks/query/run', 'aati_events_maybe_run_new_queries', 10, 2);
function aati_events_maybe_run_new_queries( $results, $query_obj ) {
    
    if ( $query_obj->object_type === 'aati_events_current' ) {
        $results = run_current_query($query_obj);
    }
    
    if ( $query_obj->object_type === 'aati_events_future' ) {
        $results = run_future_query($query_obj);
    }
    
    return $results;
}


/* Setup post data for posts */
add_filter( 'bricks/query/loop_object', 'aati_events_setup_post_data', 10, 3);
function aati_events_setup_post_data( $loop_object, $loop_key, $query_obj ) {
    
    /* setup post data if using any of our custom queries */
    if ( $query_obj->object_type === 'aati_events_current' || $query_obj->object_type === 'aati_events_future' ) {
		
       global $post;
       $post = get_post( $loop_object );
       setup_postdata( $post );
		
    }
    
    return $loop_object;

};


/* first WP Query arguments */
function run_current_query() {
    $aati_events_current_date = current_time('Y-m-d');

    $args = [
        'post_type' => 'aati_event',
        'meta_query' => [
            'relation' => 'AND',
            [
                'key' => '_aati_event_start_date',
                'value' => $aati_events_current_date,
                'compare' => '<=',
                'type' => 'DATE',
            ],
            [
                'key' => '_aati_event_end_date',
                'value' => $aati_events_current_date,
                'compare' => '>=',
                'type' => 'DATE',
            ],
        ],
        'orderby' => $query_obj->settings['orderby'],
        'posts_per_page' => $query_obj->settings['posts_per_page'],
    ];

    $posts_query = new WP_Query($args);

    return $posts_query->posts;
}

/* second WP Query arguments */
function run_future_query() {
    $aati_events_current_date = current_time('Y-m-d');

    $args = [
        'post_type' => 'aati_event',
        'meta_query' => [
            [
                'key' => '_aati_event_start_date',
                'value' => $aati_events_current_date,
                'compare' => '>',
                'type' => 'DATE',
            ],
        ],
        'orderby' => $query_obj->settings['orderby'],
        'posts_per_page' => $query_obj->settings['posts_per_page'],
    ];

    $posts_query = new WP_Query($args);

    return $posts_query->posts;
}





add_filter( 'bricks/builder/query_control_fields', 'aati_events_add_query_control_fields' );
function aati_events_add_query_control_fields( $fields ) {
  // Posts Per Page
  $fields['posts_per_page'] = [
    'type'        => 'number',
    'label'       => esc_html__( 'Posts Per Page', 'aati_events' ),
    'description' => esc_html__( 'Number of posts to show per page', 'aati_events' ),
    'responsive'  => false,
    'min'         => 1,
    'max'         => 100,
    'default'     => 10,
    'conditions'  => [
      'object_type' => [
        'in' => [
          'aati_events_current',
		  'aati_events_future',
        ],
      ],
    ],
  ];
  
  // Order By
  $fields['orderby'] = [
    'type'        => 'select',
    'label'       => esc_html__( 'Order By', 'aati_events' ),
    'description' => esc_html__( 'Order posts by', 'aati_events' ),
    'responsive'  => false,
    'default'     => 'date',
    'options'     => [
      'none'          => esc_html__( 'No order', 'aati_events' ),
      'ID'            => esc_html__( 'Post ID', 'aati_events' ),
      'author'        => esc_html__( 'Author', 'aati_events' ),
      'title'         => esc_html__( 'Title', 'aati_events' ),
      'date'          => esc_html__( 'Published date', 'aati_events' ),
      'modified'      => esc_html__( 'Modified date', 'aati_events' ),
      'rand'          => esc_html__( 'Random', 'aati_events' ),
    ],
    'conditions'  => [
      'object_type' => [
        'in' => [
          'aati_events_current',
		  'aati_events_future',
        ],
      ],
    ],
  ];

  return $fields;
}



function aati_add_export_page() {
    add_submenu_page(
        'edit.php?post_type=aati_event', // Parent slug
        'Export Events', // Page title
        'Export Events', // Menu title
        'manage_options', // Capability
        'aati_export_events', // Menu slug
        'aati_export_events_page' // Function
    );
}
add_action('admin_menu', 'aati_add_export_page');

function aati_export_events() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Get the events
    $events = get_posts(array(
        'post_type' => 'aati_event',
        'posts_per_page' => -1,
    ));

    // Prepare the CSV file
    $filename = plugin_dir_path(__FILE__) . 'aati-events.csv';
    $file = fopen($filename, 'w');

    // Write the header
    $headers = array('ID', 'Title', 'Content');
    $meta_keys = aati_get_meta_keys('aati_event');
    $headers = array_merge($headers, $meta_keys);
    fputcsv($file, $headers);

    // Write the events
    foreach ($events as $event) {
        $id = $event->ID;
        $title = get_the_title($id);
        $content = $event->post_content;
        $row = array($id, $title, $content);

        foreach ($meta_keys as $key) {
            $row[] = get_post_meta($id, $key, true);
        }

        fputcsv($file, $row);
    }

    // Close the file
    fclose($file);

    // Return the path to the file
    return $filename;
}

function aati_export_events_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if the export button has been pressed
    if (isset($_POST['aati_export'])) {
        $filename = aati_export_events();
        $filename = str_replace(plugin_dir_path(__FILE__), '', $filename);
        $message = 'Events exported successfully. <a href="' . plugins_url($filename, __FILE__) . '">Download CSV</a>';
    }

    // Render the export page
    echo '<div class="wrap">';
    echo '<h1>Export Events</h1>';
    if (isset($message)) {
        echo '<p>' . $message . '</p>';
    }
    echo '<form method="post">';
    echo '<input type="submit" name="aati_export" class="button button-primary" value="Export Events">';
    echo '</form>';
    echo '</div>';
}



function aati_get_meta_keys($post_type) {
    global $wpdb;

    $query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key) 
        FROM $wpdb->posts 
        LEFT JOIN $wpdb->postmeta 
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id 
        WHERE $wpdb->posts.post_type = '%s'
    ";

    $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));

    return $meta_keys;
}



function aati_add_import_page() {
    add_submenu_page(
        'edit.php?post_type=aati_event',
        'Import Events',
        'Import Events',
        'manage_options',
        'aati_import_events',
        'aati_import_events_page'
    );
}
add_action('admin_menu', 'aati_add_import_page');

function aati_import_events() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }

    // Check if a file was uploaded
    if (!isset($_FILES['csv_file'])) {
        return 'No file was uploaded.';
    }

    // Check if the file is a CSV file
    if (pathinfo($_FILES['csv_file']['name'], PATHINFO_EXTENSION) !== 'csv') {
        return 'The uploaded file is not a CSV file.';
    }

    // Open the file
    $file = fopen($_FILES['csv_file']['tmp_name'], 'r');

    // Get the header
    $headers = fgetcsv($file);

    // Loop through the lines
    while (($line = fgetcsv($file)) !== false) {
        // Prepare the post data
        $post_data = array(
            'post_type' => 'aati_event',
            'post_title' => sanitize_text_field($line[array_search('Title', $headers)]),
            'post_content' => sanitize_text_field($line[array_search('Content', $headers)]),
            'post_status' => 'publish',
        );

        // Insert the post
        $post_id = wp_insert_post($post_data);

        // Add the meta data
        foreach ($headers as $index => $key) {
            if (substr($key, 0, 1) === '_') {  // Check if the header starts with an underscore, indicating it's a custom field
                update_post_meta($post_id, $key, sanitize_text_field($line[$index]));
            }
        }
    }

    // Close the file
    fclose($file);

    // Return a success message
    return 'Events imported successfully.';
}


function aati_import_events_page() {
    // Check user capability
    if (!current_user_can('manage_options')) {
        return;
    }

    // Check if the import button has been pressed
    $message = '';
    if (isset($_POST['aati_import'])) {
        $message = aati_import_events();
    }

    // Render the import page
    echo '<div class="wrap">';
    echo '<h1>Import Events</h1>';
    if (!empty($message)) {
        echo '<p>' . $message . '</p>';
    }
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="csv_file">';
    echo '<input type="submit" name="aati_import" class="button button-primary" value="Import Events">';
    echo '</form>';
    echo '</div>';
}
