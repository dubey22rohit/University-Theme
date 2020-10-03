<?php
get_header();
while(have_posts()){
    the_post();
    pageBanner();
     ?>
    
  <div class="container container--narrow page-section">
  <div class="metabox metabox--position-up metabox--with-home-link">
  <?php $eventDate = new DateTime(get_field('event_date'))?>
      <p><a class="metabox__blog-home-link" href="<?php echo get_post_type_archive_link('events')?>"><i class="fa fa-home" aria-hidden="true"></i> Events Home</a> <span class="metabox__main"><?php the_title();?> on <?php echo $eventDate->format('dMY');?> </span></p>
  </div>
  <div class='generic-content'>
      <?php the_content();?>
  <?php
  $relatedPrograms = get_field('related_programs');
  if($relatedPrograms){
    echo '<hr class = " section-break">';
    echo '<h2 class = "headline headline--medium">Related Programs:</h2>';
    echo '<ul class = "link-list min-list"></ul>';
    foreach($relatedPrograms as $program){?>
    <li><a href="<?php the_permalink($program)?>"><?php echo get_the_title($program)?></a></li>
    <?php }
    echo '</ul>';
  }?>
  </div>

    <?php }
      get_footer();
?>