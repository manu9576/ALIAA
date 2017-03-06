<?php

@ini_set( 'upload_max_size' , '64M' );
@ini_set( 'post_max_size', '64M');
@ini_set( 'max_execution_time', '300' );

add_action( 'wp_enqueue_scripts', 'my_theme_enqueue_styles' );
// bloque l'affichage de la version de WP - rend plus difficile le pirate...
remove_action('wp_head', 'wp_generator');

// masque les erreurs de connexion
add_filter('login_errors',create_function('$a', "return null;"));

function my_theme_enqueue_styles() {
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );

}
?>
