<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 */

?>

<?php
if ( class_exists( 'ACF' ) ) :
	acf_form_head();
endif;
get_header(); ?>
<div id="primary" class="content-area">

    <main class="site-main" role="main">
        <div class="hentry">

			<?php echo credglv()->page->execute(); ?>
        </div>
    </main>

</div>
<?php get_footer() ?>

