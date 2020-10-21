<?php get_header();
page_banner(array(
  'title' => 'All Programs',
  'subtitle' => 'Have a look around,there is something for everyone'
));
?>


<div class="container container--narrow page-section">
  <?php while (have_posts()) {
    the_post(); ?>
    <div class='post-item'>
      <h2 class='headline headline--medium headline--post-title'><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>

      <div class="generic-content">
        <?php the_excerpt(); ?>
        <p><a class='btn btn--blue' href="<?php the_permalink(); ?>">More Info &raquo</a></p>
      </div>
    </div>
  <?php }
  echo paginate_links(); //phpcs:ignore
  ?>
</div>

<?php get_footer(); ?>