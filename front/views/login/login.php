<header class="entry-header">
    <h1 class="entry-title"><?php echo __( 'Login page GLV', 'credglv' ); ?></h1>
</header><!-- .entry-header -->

<div class="entry-content">
    <form action="" method="post" id="login-form" class="form-login-cred">
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
        <div class="verify-block ">
			<?php \credglv\front\controllers\ThirdpartyController::getInstance()->captcha_field( array( 'callback' => 'recaptcha_callback' ) ); ?>

        </div>
        <div class="form-group hide" style="padding-top:20px;">
            <label for="otp"><?php echo __( 'OTP', 'credglv' ); ?>
            </label><span class="otp"></span>
            <input type="number" disabled id="otp">
        </div>
        <span class="error-msg-front"></span>
        <div class="form-group ">
            <label for="change_method_login">
                <input type="checkbox" id="change_method_login"> <?php echo __( 'Login by OTP', 'credglv' ); ?>
            </label>
        </div>
        <span class="submit" style="display:block"></span>
        <button id="submit-button" type="submit"
                class="btn btn-primary btn-submit"><?php echo __( 'Submit', 'credglv' ); ?></button>


        <script type="text/javascript">

            function recaptcha_callback() {
                // document.getElementById("otp").value = "ok";
                var xhttp = new XMLHttpRequest();


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

</div>
