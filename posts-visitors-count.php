<?php
/*
* Plugin Name: Posts Visitors Counter
*/

add_action('wp_head', function(){
    $postId = get_the_ID(); 
    echo '<link rel="postsVisitorsCount" href="'.get_rest_url( null, 'posts-visitors-count/v1/add/'.$postId ).'" />';
});

function postsVisitorsCount_load_plugin_scripts() {
    if(is_single()){
        wp_enqueue_script('postsVisitorsCount', plugin_dir_url( __FILE__ ) . '/main.js', array('jquery'), "", true);
    }
}
add_action( 'wp_enqueue_scripts', 'postsVisitorsCount_load_plugin_scripts' );

function postsVisitorsCount_addNew($data){
    $meta_key = 'postsVisitorsCount_meta';
    $post_id = $data['id'];
    if(get_post_type($post_id) === 'post'){
        $count = get_post_meta($post_id, $meta_key, true);
        if ($count === '') {
            add_post_meta($post_id, $meta_key, 1);
        } else {
            $count = intval($count);
            update_post_meta($post_id, $meta_key, $count + 1);
        }
    }
}
add_action( 'rest_api_init', function () {
  register_rest_route( 'posts-visitors-count/v1', '/add/(?P<id>\d+)', array(
    'methods' => 'GET',
    'callback' => 'postsVisitorsCount_addNew',
  ) );
});

add_action('plugins_loaded', 'postsVisitorsCount_run');
function postsVisitorsCount_run(){
    if(current_user_can( 'edit_posts' )){
        require_once __DIR__ . '/views-column.php';
        //require_once __DIR__ . '/views-dashboard-widget.php';
    }
}

add_action('admin_menu', 'postsVisitorsCount_top_posts_page');
function postsVisitorsCount_top_posts_page(){
    if(current_user_can( 'edit_posts' )){
        $title = get_locale() === 'ar' ? 'مشاهدات المقالات' : 'Posts Visits';
        $menu_title  = $title;
        add_menu_page(
            $title,     // page title
            $menu_title,     // menu title
            'reports-view',   // capability
            'posts-visitors-count',     // menu slug
            'postsVisitorsCount_content', // callback function
            'dashicons-media-spreadsheet' // menu icon 
        );
    }
}
function postsVisitorsCount_content(){
    require_once __DIR__ . '/views-content.php';
}


function postsVisitorsCount_addCap(){
    $role = get_role( 'administrator' );
    $role->add_cap( 'reports-view' );

}
add_action( 'init', 'postsVisitorsCount_addCap' );


?>
