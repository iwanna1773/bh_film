<?php if(!is_search()): ?>
</div><!-- end .row -->
</div><!-- end .container -->
</main><!-- end .site-main -->
<?php endif; ?>
<?php
//check for blank page
$footer_value = '';
if($post) {
    $footer_value = get_post_meta($post->ID, $key ='anps_blank_page_disable_footer', $single = true );
}
if(!isset($footer_value) || $footer_value!='on') {
    get_sidebar( 'footer' ); 
}
?>
<?php if (get_option('anps_scroll_to_top', 0) == 1 ): ?>
	<div id="scrolltop" class="fixed scrollup"><a href="#" class="transparent" title="Scroll to top"><i class="fa fa-angle-up"></i></a></div>
<?php endif;?> 
</div> <!-- .site -->
<?php wp_footer(); ?>
</body>
</html>