<?php 
require get_theme_file_path('/additional/search-route.php');
function university_custom_rest() {
    register_rest_field( 'post','authorName', array(
        'get_callback' => function() {
            return get_author_name();}
    ));
    register_rest_field( 'note','noteCount', array(
        'get_callback' => function() {
            return count_user_posts( get_current_user_id(),'note');}
    ));
}
add_action( 'rest_api_init','university_custom_rest');

function page_banner ( $args = null) {
    if (!$args['title']) {
        $args['title'] = get_the_title();
    }
    if (!$args['subtitle']) {
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if (!$args['photo']) {
        if (get_field('page_banner_background_image')) {
            $args['photo'] = get_field('page_banner_background_image')['sizes']['page_banner'];

        } else {
            $args['photo'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
?>
<div class="page-banner">
<div class="page-banner__bg-image" style="background-image: url(<?php echo $args['photo']?>);"></div>
<div class="page-banner__content container container--narrow">
  <h1 class="page-banner__title"><?php echo $args['title']?></h1>
  <div class="page-banner__intro">
    <p><?php echo $args['subtitle']?></p>
  </div>
</div>  
</div>
<?php } ?>


<?php
function university_files() {
    wp_enqueue_script('main-university-js',get_template_directory().'/js/scripts-bundled.js',null,1.0,true);//phpcs:ignore
    wp_enqueue_script('universityJS',get_template_directory_uri().'/js/scripts.js',array(),'1.0');//phpcs:ignore
    wp_enqueue_style('custon-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');//phpcs:ignore
    wp_enqueue_style('font-awsome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');//phpcs:ignore
    wp_enqueue_style('university_main_styles',get_stylesheet_uri());//phpcs:ignore
    wp_localize_script( 'main-university-js', 'universityData', array(
        'root_url' => get_site_url(),
        'nonce'=>wp_create_nonce('wp_rest')
    ));
}
add_action('wp_enqueue_scripts','university_files');

function university_features() {
    register_nav_menu( 'footerMenuOne', 'Footer Menu One' );
    register_nav_menu( 'footerMenuTwo', 'Footer Menu Two' );
    register_nav_menu( 'headerMenuLocation', 'Header Menu Location' );
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size( 'professorLandscape', 400,260, true );
    add_image_size( 'professorPortrait',480, 650, true);
    add_image_size('page_banner',15000,350,true);
}
add_action('after_setup_theme','university_features');

function university_adjust_queries( $query) {
    if (!is_admin() && is_post_type_archive( 'program' ) && $query->is_main_query()) {
        $query->set('orderby','title');
        $query->set('order','ASC');
        $query->set('posts_per_page',-1);
    }
    if (!is_admin() && is_post_type_archive( 'events' ) && $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key','event_date');
        $query->set('orderby','meta_value_num');
        $query->set('order','ASC');
        $query->set('meta_query',array(
                'key'=>'event_date',
                'compare'=>'>=',
                'value'=> $today,
                'type'=>'numeric'
        ));
    }
}

add_action('pre_get_posts','university_adjust_queries');

function university_map_key( $api) {
    $api['key'] = 'AIzaSyAkxje44auufZI2_lDR6ttvD-jGsrSuXUA';
    return $api;
}
add_filter( 'acf/fields/google_map/api','university_map_key');

add_action('admin_init','redirect_subs');
function redirect_subs() {
    $currentuser = wp_get_current_user();
    if (count($currentuser->roles)==1 && $currentuser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}
add_action('wp_loaded','no_admin_bar');
function no_admin_bar() {
    $currentuser = wp_get_current_user();
    if (count($currentuser->roles)==1 && $currentuser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}
add_filter('login_headererl','personalizedHeader');
function personalizedHeader() {
    return esc_url(site_url('/'));
}
add_action('login_enqueue_scripts','personalizedCSS');
function personalizedCSS() {
    wp_enqueue_style('university_main_styles',get_stylesheet_uri());
}

add_filter( 'the_content', 'disable_wpautop_cpt', 0 );
function disable_wpautop_cpt( $content ) {
'custom_post_slug' === get_post_type() && remove_filter( 'the_content', 'wpautop' );
return $content;
}

add_filter('wp_insert_post_data','makeNotePrivate',10,2);
function makeNotePrivate ( $data, $postarr) {
    if ($data['post_type'] == 'note') {
        if (count_user_posts(get_current_user_id(),'note') > 25 && !$postarr['ID']) {
            die ('You have reached your maximum notes limit');
        }
        $data['post_content'] = sanitize_textarea_field( $data['post_content'] );
        $data['post_title'] = sanitize_text_field( $data['post_title'] );
    }
   if ($data['post_type'] == 'note' && $data['post_status'] != 'trash') {
        $data['post_status'] = 'private';
    }
    return $data; 
}
function university_post_types() {
    register_post_type('events',array(
        'capability_type' => 'event',
        'map_meta_cap' => true,
        'supports'=>array('title','editor','excerpt'),
        'rewrite'=> array('slug'=>'events'),
        'has_archive' => true,
        'public'=> true,
        'labels'=>array(
            'name' => 'Events',
            'add_new_item' => 'Add New Event',
            'edit_item' => 'Edit Event',
            'all_items'=>'All Events',
            'singular_name'=>'Event'

        ),
        'menu_icon' => 'dashicons-calendar',
    ));
    //Programs Post Type
    register_post_type('program',array(
        'supports'=>array('title','editor'),
        'rewrite'=> array('slug'=>'programs'),
        'has_archive' => true,
        'public'=> true,
        'labels'=>array(
            'name' => 'Programs',
            'add_new_item' => 'Add New Program',
            'edit_item' => 'Edit Program',
            'all_items'=>'All Programs',
            'singular_name'=>'Program'

        ),
        'menu_icon' => 'dashicons-awards',
    ));
    //Professor post type
    register_post_type('professor',array(
        'show_in_rest' => true,
        'supports'=>array('title','editor','thumbnail'),
        'rewrite'=> array('slug'=>'professors'),
        'has_archive' => true,
        'public'=> true,
        'labels'=>array(
            'name' => 'Professors',
            'add_new_item' => 'Add New Professor',
            'edit_item' => 'Edit Professor',
            'all_items'=>'All Professors',
            'singular_name'=>'Professor'

        ),
        'menu_icon' => 'dashicons-welcome-learn-more',
    ));
    //Campus Post Type
    register_post_type('campus',array(
        'supports'=>array('title','editor','excerpt'),
        'rewrite'=> array('slug'=>'campuses'),
        'has_archive' => true,
        'public'=> true,
        'labels'=>array(
            'name' => 'Campuses',
            'add_new_item' => 'Add New Campus',
            'edit_item' => 'Edit Campus',
            'all_items'=>'All Campuses',
            'singular_name'=>'Campus'

        ),
        'menu_icon' => 'dashicons-location-alt',
    ));
    register_post_type('note',array(
        'capability_type'=>'note',
        'map_meta_cap'=>true,
        'show_in_rest' => true,
        'supports'=>array('title','editor'),
        'rewrite'=> array('slug'=>'notes'),
        'has_archive' => true,
        'public'=> false,
        'show_ui'=>true,
        'labels'=>array(
            'name' => 'Notes',
            'add_new_item' => 'Add New Note',
            'edit_item' => 'Edit Note',
            'all_items'=>'All Notes',
            'singular_name'=>'Note'

        ),
        'menu_icon' => 'dashicons-welcome-write-blog',
    ));
}
add_action( 'init', 'university_post_types');
?>