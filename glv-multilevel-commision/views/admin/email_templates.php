<?php
$title = __('Email templates', 'wmc');
$joining_mail_template = stripcslashes( get_option('joining_mail_template', '') );
$joining_mail_subject = stripcslashes( get_option('joining_mail_subject', __('Referral Program Team','wmc')) );
$joining_mail_heading = stripcslashes( get_option('joining_mail_heading', __('Referral Program Team','wmc')) );
$referral_user_template = stripcslashes( get_option('referral_user_template', '') );
$referral_user_subject = stripcslashes( get_option('referral_user_subject', __('Referral Program Team','wmc')) );
$referral_user_heading = stripcslashes( get_option('referral_user_heading', __('Referral Program Team','wmc')) );
$expire_notification_template = stripcslashes( get_option('expire_notification_template', '') );
$expire_notification_subject = stripcslashes( get_option('expire_notification_subject',__('Referral Program Team','wmc')) );
$expire_notification_heading = stripcslashes( get_option('expire_notification_heading', __('Referral Program Team','wmc')) );
?>
<h3>
<?php
//echo esc_html( $title );
?>
</h3>
                <form method="post" action="">
                    <table class="wp-list-table widefat fixed striped wmc-email-template">
                        <colgroup>
                            <col style="width: 70%;"/>
                            <col style="width: 30%;"/>                            
                        </colgroup>
                        <tbody>
                        <tr><td colspan="2"> <h3><?php _e('Joining mail for referral program', 'wmc');?></h3></td></tr>
                        <tr>                        
                            <td>                               
                                <label for="joining_mail_subject"><?php _e('Joining mail Subject', 'wmc');?></label>
                                <div><input placeholder="<?php _e('Joining mail Subject', 'wmc');?>" type="text" class="form-field" name="joining_mail_subject" id="joining_mail_subject" value="<?php echo $joining_mail_subject;?>"></div>
                                <label for="joining_mail_heading"><?php _e('Joining mail Heading', 'wmc');?></label>
                                <div><input placeholder="<?php _e('Joining mail Heading', 'wmc');?>" type="text" class="form-field" name="joining_mail_heading" id="joining_mail_heading" value="<?php echo $joining_mail_heading;?>"></div>
                                <?php echo wp_editor($joining_mail_template, 'joining_mail_template')?>
                                
                            </td>
                            <td>
                                <small><?php _e('You can use {referral_code} to replace respective referral code.', 'wmc');?></small><br/>
                                <small><?php _e('You can use {first_name} to replace respective user name.', 'wmc');?></small><br/>
                                <small><?php _e('You can use {last_name} to replace respective user name.', 'wmc');?></small>
                            </td>
                        </tr>
                        <tr><td colspan="2"> <h3><?php _e('Invitation mail for Referral users', 'wmc');?></h3></td></tr>
                        <tr>
                            <td>
                                <label for="referral_user_subject"><?php _e('Referral User E-mail Subject', 'wmc');?></label>
                                <div><input placeholder="<?php _e('Referral User E-mail Subject', 'wmc');?>" type="text" class="form-field" name="referral_user_subject" id="referral_user_subject" value="<?php echo $referral_user_subject;?>"></div>
                                <label for="referral_user_heading"><?php _e('Referral User E-mail Heading', 'wmc');?></label>
                                <div><input placeholder="<?php _e('Referral User E-mail Heading', 'wmc');?>" type="text" class="form-field" name="referral_user_heading" id="referral_user_heading" value="<?php echo $referral_user_heading;?>"></div>
                                <?php echo wp_editor($referral_user_template, 'referral_user_template')?>
                            </td>
                            <td>
                                <small><?php _e('You can use {referral_code} to replace respective referral code.', 'wmc');?></small><br/>
                                <small><?php _e('You can use {first_name} to replace respective user name.', 'wmc');?></small><br/>
                                <small><?php _e('You can use {last_name} to replace respective user name.', 'wmc');?></small><br/>
                                <small><?php _e('You can use [referral_link text="Click here"] to replace respective user referral link.', 'wmc');?></small>
                            </td>
                        </tr>
                        <tr><td colspan="2"> <h3><?php _e('Expire credit notification', 'wmc');?></h3></td></tr>
                        <tr>
                            <td>
                                <label for="expire_notification_subject"><?php _e('Expire Notification E-mail Subject', 'wmc');?></label>
                                <div><input placeholder="<?php _e(' Notification E-mail Subject', 'wmc');?>" type="text" class="form-field" name="expire_notification_subject" id="expire_notification_subject" value="<?php echo $expire_notification_subject;?>"></div>
                                 <label for="expire_notification_heading"><?php _e('Expire Notification E-mail Heading', 'wmc');?></label>
                                <div><input placeholder="<?php _e(' Notification E-mail Heading', 'wmc');?>" type="text" class="form-field" name="expire_notification_heading" id="expire_notification_heading" value="<?php echo $expire_notification_heading;?>"></div>
                                <?php echo wp_editor($expire_notification_template, 'expire_notification_template')?>
                            </td>
                            <td>
                                <small><?php _e('{available_credits} - Replace respective user credits.', 'wmc');?></small><br/>
                                <small><?php _e('{first_name} - Replace respective user name.', 'wmc');?></small><br/>
                                <small><?php _e('{last_name} - Replace respective user name.', 'wmc');?></small><br/>
                                <small><?php _e('{expire_date} - Replace respective expiry date of user credits.', 'wmc');?></small><br />
                                <small><?php _e('{validity_period} - Replace respective store credit validity.', 'wmc');?></small><br/>
                                <small><?php _e('{today_date} - Replace respective current date.', 'wmc');?></small><br/>
                                <small><?php _e('{expire_month} - Replace respective credit expired month.', 'wmc');?></small><br/>
                                <small><?php _e('{expire_credits} - Replace respective expired credits.', 'wmc');?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <p>
                        <input type="submit" class="button button-primary button-large" name="save_template" value="<?php _e('Save template', 'wmc')?>" />
                    </p>
                </form>
</div>
