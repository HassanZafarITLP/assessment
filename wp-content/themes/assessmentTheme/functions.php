<?php

function add_normalize_CSS() {
   wp_enqueue_style( 'normalize-styles', "https://cdnjs.cloudflare.com/ajax/libs/normalize/7.0.0/normalize.min.css");
}
add_action('wp_enqueue_scripts', 'add_normalize_CSS');

function add_scripts_method(){
wp_enqueue_script( 'jquery');
}
add_action( 'wp_enqueue_scripts', 'add_scripts_method' );

function add_widget_support() {
               register_sidebar( array(
                               'name'          => 'Sidebar',
                               'id'            => 'sidebar',
                               'before_widget' => '<div>',
                               'after_widget'  => '</div>',
                               'before_title'  => '<h2>',
                               'after_title'   => '</h2>',
               ) );
}
add_action( 'widgets_init', 'add_widget_support' );

function add_Main_Nav() {
  register_nav_menu('header-menu',__( 'Header Menu' ));
}

add_action( 'init', 'add_Main_Nav' );

function is_userip_address_a_match() {
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $ip_parts = explode('.', $_SERVER['REMOTE_ADDR']);
        if ($ip_parts[0] === '77' && $ip_parts[1] === '29') {
            return true;
        }
    }
    return false;
}

function redirect_user_basedon_ip() {
    if (is_userip_address_a_match()) {
        wp_redirect('https://ikonicsolution.com/');
        exit;
    }
}
add_action('template_redirect', 'redirect_user_basedon_ip');


// Register the Projects custom post type
function register_projects_post_type() {
    $labels = array(
        'name'               => 'Projects',
        'singular_name'      => 'Project',
        'menu_name'          => 'Projects',
        'name_admin_bar'     => 'Projects',
        'add_new'            => 'Add New',
        'add_new_item'       => 'Add New Project',
        'edit_item'          => 'Edit Project',
        'new_item'           => 'New Project',
        'view_item'          => 'View Project',
        'all_items'          => 'All Projects',
        'search_items'       => 'Search Projects',
        'not_found'          => 'No projects found',
        'not_found_in_trash' => 'No projects found in Trash',
        'parent_item_colon'  => '',
        'featured_image'     => 'Project Cover Image',
        'set_featured_image' => 'Set cover image',
        'remove_featured_image' => 'Remove cover image',
        'use_featured_image' => 'Use as cover image',
        'archives'           => 'Project archives',
        'insert_into_item'   => 'Insert into project',
        'uploaded_to_this_item' => 'Uploaded to this project',
        'filter_items_list'  => 'Filter projects list',
        'items_list_navigation' => 'Projects list navigation',
        'items_list'         => 'Projects list',
    );

    $args = array(
        'labels'              => $labels,
        'public'              => true,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'query_var'           => true,
        'rewrite'             => array( 'slug' => 'projects' ),
        'capability_type'     => 'post',
        'has_archive'         => true,
        'hierarchical'        => false,
        'menu_position'       => null,
        'supports'            => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' ),
        'menu_icon'           => 'dashicons-portfolio', 
    );

    register_post_type( 'projects', $args );
}
add_action( 'init', 'register_projects_post_type' );

// Register the Project Type taxonomy
function register_project_type_taxonomy() {
    $labels = array(
        'name'                       => 'Project Types',
        'singular_name'              => 'Project Type',
        'menu_name'                  => 'Project Types',
        'all_items'                  => 'All Project Types',
        'edit_item'                  => 'Edit Project Type',
        'view_item'                  => 'View Project Type',
        'update_item'                => 'Update Project Type',
        'add_new_item'               => 'Add New Project Type',
        'new_item_name'              => 'New Project Type Name',
        'parent_item'                => 'Parent Project Type',
        'parent_item_colon'          => 'Parent Project Type:',
        'search_items'               => 'Search Project Types',
        'popular_items'              => 'Popular Project Types',
        'separate_items_with_commas' => 'Separate project types with commas',
        'add_or_remove_items'        => 'Add or remove project types',
        'choose_from_most_used'      => 'Choose from the most used project types',
        'not_found'                  => 'No project types found',
    );

    $args = array(
        'labels'                     => $labels,
        'hierarchical'               => true,
        'public'                     => true,
        'show_ui'                    => true,
        'show_admin_column'          => true,
        'show_in_nav_menus'          => true,
        'show_tagcloud'              => true,
    );

    register_taxonomy( 'project_type', 'projects', $args );
}
add_action( 'init', 'register_project_type_taxonomy' );

// Register the AJAX endpoint
function register_ajax_endpoint() {
    add_action( 'wp_ajax_projects_ajax_endpoint', 'projects_ajax_handler' );
    add_action( 'wp_ajax_nopriv_projects_ajax_endpoint', 'projects_ajax_handler' );
}
add_action( 'wp_loaded', 'register_ajax_endpoint' );


function projects_ajax_handler() {

    $response = array(
        'success' => true,
        'data'    => array(),
    );

    if ( is_user_logged_in() ) {
        $posts_per_page = 6;
    } else {
        $posts_per_page = 3;
    }

    $args = array(
        'post_type'      => 'projects',
        'posts_per_page' => $posts_per_page,
        'orderby'        => 'date',
        'order'          => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $post_id    = get_the_ID();
            $post_title = get_the_title();
            $post_link  = get_permalink();
            $post_data = array(
                'id'    => $post_id,
                'title' => $post_title,
                'link'  => $post_link,
            );
            $response['data'][] = $post_data;
        }
    } else {
        $response['success'] = false;
    }

    wp_reset_postdata();

    $json_response = json_encode( $response );

    header( 'Content-Type: application/json' );
    echo $json_response;

    wp_die();
}

function hs_give_me_coffee() {
    $response = wp_remote_get('https://coffee.alexflipnote.dev/random.json');
    
    if (is_wp_error($response)) {
        return 'Failed to fetch coffee. Please try again later.';
    }
    
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body);
    
    if (!$data) {
        return 'Failed to decode API response.';
    }
    $coffee_link = "<a href=".$data->file.">Your Coffee is here!</a>";
    return $coffee_link;
}

add_shortcode( 'Hassan_get_coffee', 'hs_give_me_coffee' );


function show_kanye_quotes() {
    $quotes = array();

    for ($i = 0; $i < 5; $i++) {
        $response = wp_remote_get('https://api.kanye.rest/quote/random');

        if (is_wp_error($response)) {
            return 'Failed to fetch quotes. Please try again later.';
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body);

        if (!$data) {
            return 'Failed to decode API response.';
        }

        $quote = $data->quote;

        $quotes[] = $quote;
    }

    $output = '<ul>';
    foreach ($quotes as $quote) {
        $output .= '<li>' . $quote . '</li>';
    }
    $output .= '</ul>';

    return $output;
}

add_shortcode( 'hassan_get_quotes', 'show_kanye_quotes' );