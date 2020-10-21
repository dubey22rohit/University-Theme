<?php get_header();
page_banner(array(
  'title' => 'Past Events',
  'subtitle' => 'See what all events we have had till now'
));
?>


<div class="container container--narrow page-section">
  <?php
  $today = gmdate('Ymd');
  $past_events  = new WP_Query(array(
    'paged' => get_query_var('paged', 1),
    'post_type' => 'events',
    'meta_key' => 'event_date',
    'orderby' => 'meta_value_num',
    'order' => 'DESC',
    'meta_query' => array(
      array(
        'key' => 'event_date',
        'compare' => '<',
        'value' => $today,
        'type' => 'numeric'
      )
    )
  )); ?>
  <?php while ($past_events->have_posts()) {
    $past_events->the_post();
    get_template_part('templates/content-event');
  }
  echo paginate_links(array( //phpcs:ignore
    'total' => $past_events->max_num_pages,
  ));
  ?>
</div>

<?php get_footer(); ?>