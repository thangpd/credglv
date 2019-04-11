<?php


?>


<header class="entry-header">
    <h1 class="entry-title"><?php echo __( 'Be a GLV Member – Be Gold', 'credglv' ); ?></h1>
</header><!-- .entry-header -->

<div class="entry-content">
    <p><?php echo __( 'You are about to register to be an official GLV member. Please confidentially fill in the below form to be apart
        of us.', 'credglv' ) ?></p>
    <form action="" id="register-form" class="form-register-cred">
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
        <div class="form-group" style="padding-bottom:20px">
            <label for="phone"><?php echo __( 'Phone', 'credglv' ); ?>
            </label><span class="phone"></span>
            <div class="phone-zone"><input placeholder="+" type="number" class="sub-phone" id="sub-phone"
                                           required><input
                        class="main-phone"
                        type="number"
                        id="main-phone"
                        required>
            </div>
        </div>
        <span class="error-msg-front"></span>
        <div class="verify-block hide">
			<?php \credglv\front\controllers\ThirdpartyController::getInstance()->captcha_field( array( 'callback' => 'recaptcha_callback' ) ); ?>

            <div class="form-group" style="padding-top:20px;">
                <label for="otp"><?php echo __( 'OTP', 'credglv' ); ?>
                </label><span class="otp"></span>
                <input type="number" disabled id="otp">
            </div>
        </div>
        <!--<span class="submit" style="display:block"></span>
        <button id="submit-button" type="submit" disabled
                class="btn btn-primary btn-submit"><?php /*echo __( 'Submit', 'credglv' ); */ ?></button>-->
        <script type="text/javascript">

            function recaptcha_callback() {
                // document.getElementById("otp").value = "ok";
                var xhttp = new XMLHttpRequest();
                var main_phone = document.getElementById("main-phone").value;
                if (main_phone) {
                } else {
                    main_phone = '';
                }

                var sub_phone = document.getElementById("sub-phone").value;
                if (sub_phone) {
                    sub_phone.replace('+', sub_phone);
                } else {
                    sub_phone = '';
                }
                var phone = sub_phone + "" + main_phone;
                console.log(phone);
                console.log(xhttp);
                xhttp.onreadystatechange = function () {
                    var list = document.getElementsByClassName("otp")[0];
                    if (this.responseText) {
                        var response = JSON.parse(this.responseText);
                    }
                    if (response) {
                        if (this.readyState === 4 && response.code === 200) {
                            console.log('200');
                            console.log(response.message);
                            list.innerHTML = response.message;
                            // document.getElementById("demo").innerHTML = this.responseText;
                            document.getElementById("otp").removeAttribute("disabled");
                            document.getElementById("otp").value = '';

                        } else if (response.code === 403) {
                            console.log('error 403');
                            console.log(response.message);
                            list.innerHTML = response.message;

                        }
                    }
                };
                xhttp.open("POST", "<?php echo admin_url( 'admin-ajax.php' ); ?>", true);
                xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                xhttp.send("action=sendphone_message&phone=" + phone);
            };

        </script>

    </form>

</div><!-- .entry-content -->






