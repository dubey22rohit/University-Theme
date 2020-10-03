<?php get_header();
pageBanner(array(
  'title'=>'All Events',
  'subtitle'=>'See what is going on in our world'
));
?>

 
  <div class="container container--narrow page-section">
  <?php while(have_posts()){
    the_post();
    get_template_part('templates/content-events');
  }
  // echo paginate_links(array(
  //   'total'=>max_num_pages,
  // ));
  ?>
  <hr class = 'section-break'>
  <p>Looking for a recap of our past events?<a href = "<?php echo site_url('/past-events')?>">Check out our past event archives here</a></p>
  </div>
  
<?php get_footer();?>