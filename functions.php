<?php 
require get_theme_file_path('/additional/search-route.php');
function university_custom_rest(){
    register_rest_field( 'post','authorName', array(
        'get_callback' => function(){return get_author_name();}
    ));
}
add_action( 'rest_api_init','university_custom_rest');

function pageBanner($args = NULL){
    if(!$args['title']){
        $args['title'] = get_the_title();
    }
    if(!$args['subtitle']){
        $args['subtitle'] = get_field('page_banner_subtitle');
    }
    if(!$args['photo']){
        if(get_field('page_banner_background_image')){
            $args['photo'] = get_field('page_banner_background_image')['sizes']['pageBanner'];

        }else{
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

function university_files(){
    wp_enqueue_script('main-university-js',get_theme_file_uri('/js/scripts-bundled.js'),NULL,1.0,true);
    wp_enqueue_style('custon-google-fonts','//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awsome','//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('university_main_styles',get_stylesheet_uri());
    wp_localize_script( 'main-university-js', 'universityData', array(
        'root_url' => get_site_url()
    ));
}
add_action('wp_enqueue_scripts','university_files');

function university_features(){
    register_nav_menu( 'footerMenuOne', 'Footer Menu One' );
    register_nav_menu( 'footerMenuTwo', 'Footer Menu Two' );
    register_nav_menu( 'headerMenuLocation', 'Header Menu Location' );
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size( 'professorLandscape', 400,260, true );
    add_image_size( 'professorPortrait',480, 650, true);
    add_image_size('pageBanner',15000,350,true);
}
add_action('after_setup_theme','university_features');

function university_adjust_queries($query){
    if(!is_admin() AND is_post_type_archive( 'program' ) AND $query->is_main_query()){
        $query->set('orderby','title');
        $query->set('order','ASC');
        $query->set('posts_per_page',-1);
    }
    if(!is_admin() AND is_post_type_archive( 'events' ) AND $query->is_main_query()){
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

function universityMapKey($api){
    $api['key'] = 'AIzaSyAkxje44auufZI2_lDR6ttvD-jGsrSuXUA';
    return $api;
}
add_filter( 'acf/fields/google_map/api','universityMapKey');

add_action('admin_init','redirectSubs');
function redirectSubs(){
    $currentUser = wp_get_current_user();
    if(count($currentUser->roles)==1 AND $currentUser->roles[0] == 'subscriber'){
        wp_redirect(site_url('/'));
        exit;
    }
}
add_action('wp_loaded','noAdminBar');
function noAdminBar(){
    $currentUser = wp_get_current_user();
    if(count($currentUser->roles)==1 AND $currentUser->roles[0] == 'subscriber'){
        show_admin_bar(false);
    }
}
add_filter('login_headererl','personalizedHeader');
function personalizedHeader(){
    return esc_url(site_url('/'));
}
add_action('login_enqueue_scripts','personalizedCSS');
function personalizedCSS(){
    wp_enqueue_style('university_main_styles',get_stylesheet_uri());
}

add_filter( 'the_content', 'disable_wpautop_cpt', 0 );
function disable_wpautop_cpt( $content ) {
'custom_post_slug' === get_post_type() && remove_filter( 'the_content', 'wpautop' );
return $content;
}
?>