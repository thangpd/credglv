<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 *
 * @since 1.0
 *
 * @var WP_Post $page
 */

 ?>
<?php if ($isHome == false):?>
    <div class="credglv-message error" id="credglv-set-homepahe">
        <?php echo __('Would you like to set this page as your homepage? Click : ', 'credglv')?>
        <a href="javascript:void(0)" class="ajax-button" data-action="set_credglv_homepage" data-target="#credglv-set-homepahe">here</a>
    </div>
<?php endif;?>
<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <!--<header class="entry-header">
        <?php /*//the_title( '<h1 class="entry-title">', '</h1>' ); */?>
    </header><-->
    <div class="entry-content">
        <?php if(!empty($page)) :?>
        <?php
            echo apply_filters( 'the_content', $page->post_content );
        ?>
        <?php else :?>
            <div class="credglv-message error">
                <?php echo __('No content for homepage found', 'credglv')?>
            </div>
        <?php endif;?>
    </div><!-- .entry-content -->
</article><!-- #post-## -->
