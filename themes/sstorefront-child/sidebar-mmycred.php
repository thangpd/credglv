<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package storefront
 */

if ( ! is_active_sidebar( 'mmycred-area' )  ) {
	return;
}
if(is_user_logged_in()){
?>

<div id="secondary" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'mmycred-area' ); ?>
</div><!-- #secondary -->
<?php } ?>