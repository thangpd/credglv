<!-- User Profile -->
<div class="credglv-user-profile">
    <div class="block-wrapper">
        <div class="img-wrapper">
			<?php echo get_avatar( $user->ID,150 ) ?>
        </div>
        <div class="content-wrapper">
            <div class="name">
                <a href="<?php echo \credglv\models\UserModel::gotoProfileStatic( '', $user ) ?>"><?php the_author_meta( 'display_name', $user->ID ); ?></a>
				<?php if ( is_user_logged_in() ): ?>
                    <a href="<?php echo credglv()->page->getPageUrl( credglv()->config->getUrlConfigs( 'credglv-user-edit-profile' ) ) ?>"><i
                                class="fa fa-edit"></i></a>
				<?php endif; ?>
            </div>
            <div class="position">
				<?php
				the_author_meta( 'job_title', $user->ID );
				?>
            </div>
            <div class="social">
                <ul class="list-wrapper">
					<?php
					$link_user = get_the_author_meta( 'user_url', $user->ID );
					if ( $link_user !== '' ) { ?>
                        <li>
                            <a href="<?php echo $link_user; ?>" class="link">
                                <i class="fa fa-globe"></i>
                            </a>
                        </li>
					<?php } ?>
					<?php foreach ( \credglv\models\UserModel::listSocial() as $key => $social ) {
						$link = get_the_author_meta( $key, $user->ID );
						if ( $link !== '' ) {
							?>
                            <li>
                                <a href="<?php echo $link; ?>" class="link">
                                    <i class="icon fa <?php echo $social['icon'] ?>"></i>
                                </a>
                            </li>
						<?php }
					} ?>
                </ul>
            </div>
            <div class="content-text">
				<?php the_author_meta( 'description', $user->ID ); ?>
            </div>
        </div>
    </div>
</div>
    <!-- End User Profile -->