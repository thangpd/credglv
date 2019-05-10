<?php
/**
 * @copyright © 2017 by Solazu Co.,LTD
 * @project Learn Master Plugin
 *
 * @since 1.0
 *
 */
?>
<?php credglv()->helpers->general->registerPjax( 'save-setting-course', 'div' ) ?>
<div class="credglv-setting">
    <form data-container="#save-setting-course" data-pjax action="<?php echo admin_url( 'admin-ajax.php' ) ?>"
          method="post">
        <input type="hidden" name="action" value="setting-course-save"/>
		<?php if ( isset( $message ) ): ?>
            <div class="notice notice-success is-dismissible">
                <p><?php _e( $message, 'credglv' ); ?></p>
            </div>
		<?php endif; ?>
        <fieldset class="credglv-setting-block">
            <legend><?php echo __( 'Course list settings', 'credglv' ) ?></legend>
			<?php foreach ( $courseListOptions as $option ): ?>
                <div class="la-form-group">
					<?php echo $option ?>
                </div>
			<?php endforeach; ?>
        </fieldset>


        <figure class="tabBlock">
            <ul class="tabBlock-tabs">
                <li class="tabBlock-tab is-active">Pricing Box 1</li>
                <li class="tabBlock-tab">Pricing Box 2</li>
                <li class="tabBlock-tab">Pricing Box 3</li>
            </ul>
            <div class="tabBlock-content">
                <div class="tabBlock-pane">
                    <div class="la-form-group">
                        <ul>
                            <li>
                                <label>
                                    Enable Pricing Box 1:
                                    <input type="checkbox"
                                           name="Options[credglv_price_box1_enable]" <?php if ( credglv()->config->credglv_price_box1_enable ) {
										echo 'checked';
									} ?>/>
                                </label>
                            </li>
                            <li>
                                <label>Title: <input type="text" name="Options[credglv_price_box1_title]"
                                                     value="<?php
								                     $title = '';
								                     $title = credglv()->config->credglv_price_box1_title;
								                     echo ! empty( $title ) ? esc_html( $title ) : 'Certificate'; ?>">
                                </label>
                            </li>
                            <!--<li>
                                <label>Price:
                                    <input type="number" name="Options[credglv_price_box1_price]"
                                           value="<?php
/*									       $price = '';
									       $price = credglv()->config->credglv_price_box1_price;
									       echo ! empty( $price ) ? esc_html( $price ) : ''; */?>">
                                </label>
                            </li>
                            <li>
                                <label>Expired time:
                                    <select name="Options[credglv_price_box1_expired]">
                                        <option value="1" <?php /*if ( credglv()->config->credglv_price_box1_expired == 1 ) {
											echo 'selected';
										} */?> ><?php /*echo __( 'Per Month', 'credglv' ); */?>
                                        </option>
                                        <option value="0" <?php /*if ( credglv()->config->credglv_price_box1_expired == 0 ) {
											echo 'selected';
										} */?>><?php /*echo __( 'Unlimited', 'credglv' ); */?>
                                        </option>
                                    </select>
                                </label>
                            </li>
                            <li>-->
                                <label>Sub title: <input type="text" name="Options[credglv_price_box1_subtitle]"
                                                         value="<?php
								                         $subtitle = '';
								                         $subtitle = credglv()->config->credglv_price_box1_subtitle;
								                         echo ! empty( $subtitle ) ? esc_html( $subtitle ) : ''; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Description: <input type="text" name="Options[credglv_price_box1_description]"
                                                           value="<?php
								                           $description = '';
								                           $description = credglv()->config->credglv_price_box1_description;
								                           echo ! empty( $description ) ? esc_html( $description ) : ''; ?>">
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tabBlock-pane">
                    <div class="la-form-group">
                        <ul>
                            <li>
                                <label>
                                    Enable Pricing Box 2:
                                    <input type="checkbox"
                                           name="Options[credglv_price_box2_enable]" <?php if ( credglv()->config->credglv_price_box2_enable ) {
										echo 'checked';
									} ?>/>
                                </label>
                            </li>
                            <li>
                                <label>Title: <input type="text" name="Options[credglv_price_box2_title]"
                                                     value="<?php
								                     $title = '';
								                     $title = credglv()->config->credglv_price_box2_title;
								                     echo ! empty( $title ) ? esc_html( $title ) : 'Monthly'; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Price:
                                    <input type="number" name="Options[credglv_price_box2_price]"
                                           value="<?php
									       $price = '';
									       $price = credglv()->config->credglv_price_box2_price;
									       echo ! empty( $price ) ? esc_html( $price ) : ''; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Expired time:
                                    <select name="Options[credglv_price_box2_expired]">
                                        <option value="1" <?php if ( credglv()->config->credglv_price_box2_expired == 1 ) {
											echo 'selected';
										} ?> ><?php echo __( 'Per Month', 'credglv' ); ?>
                                        </option>
                                        <option value="0" <?php if ( credglv()->config->credglv_price_box2_expired == 0 ) {
											echo 'selected';
										} ?>><?php echo __( 'Unlimited', 'credglv' ); ?>
                                        </option>
                                    </select>
                                </label>
                            </li>
                            <li>
                                <label>Sub title: <input type="text" name="Options[credglv_price_box2_subtitle]"
                                                         value="<?php
								                         $subtitle = '';
								                         $subtitle = credglv()->config->credglv_price_box2_subtitle;
								                         echo ! empty( $subtitle ) ? esc_html( $subtitle ) : ''; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Description: <input type="text" name="Options[credglv_price_box2_description]"
                                                           value="<?php
								                           $description = '';
								                           $description = credglv()->config->credglv_price_box2_description;
								                           echo ! empty( $description ) ? esc_html( $description ) : ''; ?>">
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tabBlock-pane">
                    <div class="la-form-group">
                        <ul>
                            <li>
                                <label>
                                    Enable Pricing Box 3:
                                    <input type="checkbox"
                                           name="Options[credglv_price_box3_enable]" <?php if ( credglv()->config->credglv_price_box3_enable ) {
										echo 'checked';
									} ?>/>
                                </label>
                            </li>
                            <li>
                                <label>Title: <input type="text" name="Options[credglv_price_box3_title]"
                                                     value="<?php
								                     $title = '';
								                     $title = credglv()->config->credglv_price_box3_title;
								                     echo ! empty( $title ) ? esc_html( $title ) : 'VIP'; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Price:
                                    <input type="number" name="Options[credglv_price_box3_price]"
                                           value="<?php
									       $price = '';
									       $price = credglv()->config->credglv_price_box3_price;
									       echo ! empty( $price ) ? esc_html( $price ) : ''; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Expired time:
                                    <select name="Options[credglv_price_box3_expired]">
                                        <option value="1" <?php if ( credglv()->config->credglv_price_box3_expired == 1 ) {
											echo 'selected';
										} ?> ><?php echo __( 'Per Month', 'credglv' ); ?>
                                        </option>
                                        <option value="0" <?php if ( credglv()->config->credglv_price_box3_expired == 0 ) {
											echo 'selected';
										} ?>><?php echo __( 'Unlimited', 'credglv' ); ?>
                                        </option>
                                    </select>
                                </label>
                            </li>
                            <li>
                                <label>Sub title: <input type="text" name="Options[credglv_price_box3_subtitle]"
                                                         value="<?php
								                         $subtitle = '';
								                         $subtitle = credglv()->config->credglv_price_box3_subtitle;
								                         echo ! empty( $subtitle ) ? esc_html( $subtitle ) : ''; ?>">
                                </label>
                            </li>
                            <li>
                                <label>Description: <input type="text" name="Options[credglv_price_box3_description]"
                                                           value="<?php
								                           $description = '';
								                           $description = credglv()->config->credglv_price_box3_description;
								                           echo ! empty( $description ) ? esc_html( $description ) : ''; ?>">
                                </label>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </figure>
        <div class="la-form-group">
            <button type="submit" class="button button-primary">
                <i class="fa fa-save"></i> <?php echo __( 'Save your changes', 'credglv' ) ?>
            </button>
        </div>
    </form>
</div>

<?php credglv()->helpers->general->endPjax( 'div' ) ?>


