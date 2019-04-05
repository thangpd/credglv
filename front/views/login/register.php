<?php
$login_action = admin_url( 'admin-ajax.php?action=credglv_login' );

//get redirect_to= ;
$login_action .= '&' . 'redirect_to=' . $redirect_to;

//get logo site
$logo_url = '';


?>


<header class="entry-header">
    <h1 class="entry-title"><?php echo __( 'Be a GLV Member – Be Gold', 'credglv' ); ?></h1>
</header><!-- .entry-header -->

<div class="entry-content">
    <p><?php echo __( 'You are about to register to be an official GLV member. Please confidentially fill in the below form to be apart
        of us.', 'credglv' ) ?></p>
    <form action="" class="form-login-cred">
        <div class="form-group  selector-referrer">
            <label for="referrer"><?php echo __( 'Referrer', 'credglv' ); ?>
            </label><span class="selector-search"></span>
            <select id="referrer" class="referrer-ajax-search form-control disable" style="width:100%"></select>
        </div>
        <div class="form-group">
            <label for="email"><?php echo __( 'Email', 'credglv' ); ?>
            </label><span class="email"></span>
            <input type="email" id="email" required>
        </div>
        <div class="form-group">
            <label for="password"><?php echo __( 'Password', 'credglv' ); ?>
            </label><span class="password"></span>
            <input type="password" id="password" required>
        </div>
        <div class="form-group">
            <label for="repassword"><?php echo __( 'Repeat Password', 'credglv' ); ?>
            </label><span class="repassword"></span>
            <input type="password" id="repassword" required>
        </div>
        <div class="form-group">
            <label for="phone"><?php echo __( 'Phone', 'credglv' ); ?>
            </label><span class="phone"></span>
            <input type="text" id="phone" required>
        </div>
		<?php \credglv\front\controllers\ThirdpartyController::getInstance()->captcha_field(); ?>
        <span class="submit" style="display:block"></span>
        <button type="submit" class="btn btn-primary btn-submit"><?php echo __( 'Submit', 'credglv' ); ?></button>
    </form>

</div><!-- .entry-content -->






