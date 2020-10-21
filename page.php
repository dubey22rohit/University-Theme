<?php
get_header();
while (have_posts()) {
  the_post();
  page_banner(array(
    'title' => 'About Us',
    'subtitle' => 'What we do',

  ));
?>

  <div class="container container--narrow page-section">

    <?php
    $the_parent  = wp_get_post_parent_id(get_the_ID());
    if ($the_parent) { ?>
      <div class="metabox metabox--position-up metabox--with-home-link">
        <p><a class="metabox__blog-home-link" href="<?php echo esc_url(get_permalink($the_parent)) ?>"><i class="fa fa-home" aria-hidden="true"></i> Back to <?php echo esc_attr(get_the_title($the_parent)) ?></a> <span class="metabox__main"><?php echo esc_attr(the_title()); ?></span></p>
      </div>
    <?php }
    ?>


    <?php
    $test_array = get_pages(array(
      'child_of' => get_the_ID()
    ));
    if ($the_parent || $test_array) { ?>
      <div class="page-links">
        <h2 class="page-links__title"><a href="<?php echo esc_url(get_permalink($the_parent)) ?>"><?php echo esc_attr(get_the_title($the_parent)) ?></a></h2>
        <ul class="min-list">
          <?php
          if ($the_parent) {
            $find_child_of = $the_parent;
          } else {
            $find_child_of = get_the_ID();
          }
        wp_list_pages(array(
            'title_li' => null,
            'child_of' => $find_child_of,
          ));
          ?>
        </ul>
      </div>

      <div class="generic-content">
        <?php the_content(); ?>
      </div>

  </div>
<?php } ?>

<?php }
get_footer();
?>