<!DOCTYPE html>
<html <?php language_attributes(); ?>
 <head>
   <title><?php bloginfo('name'); ?> &raquo; <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
   <meta charset="<?php bloginfo( 'charset' ); ?>">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>">
   <?php wp_head(); ?>
 </head>
 <body <?php body_class(); ?>>
 <script>
    jQuery(document).ready(function(jQuery) {
    // Please use below ajax_url to get logs on live server
    // var ajax_url = "wp-admin/admin-ajax.php";
    var ajax_url = "http://localhost/assessment/wp-admin/admin-ajax.php"; 
        jQuery.ajax({
            url: ajax_url,
            type: 'GET',
            dataType: 'jsonp',
            data: {
                action: 'projects_ajax_endpoint',
            },
            success: function(response) {
                if (response.success) {
                    var projects = response.data;
                    console.log("projects found.");
                    console.log(response); 
                    
                } else {
                    console.log('No projects found.');
                }
            },
            error: function(error) {
                console.log(error);
            }
        });
    });

 </script>
   <header class="my-logo">
   <h1><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php bloginfo('name'); ?></a></h1>
 </header>
 <?php wp_nav_menu( array( 'header-menu' => 'header-menu' ) ); ?>