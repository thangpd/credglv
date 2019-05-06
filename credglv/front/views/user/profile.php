<?php
$user_id = get_current_user_id();
$img_pp = get_user_meta($user_id, 'passports');
$img_iden = get_user_meta($user_id, 'iden');
$get_gender = get_user_meta($user_id, 'user_gender');
$get_fullname = get_user_meta($user_id, 'user_fullname');
$get_date = get_user_meta($user_id, 'user_date');
$get_phone = get_user_meta($user_id, 'user_phone');
$get_address = get_user_meta($user_id, 'user_address');
$get_country = get_user_meta($user_id, 'user_country');



$post_url =  esc_url( admin_url('admin-post.php') );
$html  = '';
$html .= '<form action="'.$post_url.'" method="post" enctype="multipart/form-data">';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_gender">Genders';
$html .= '<select id="user_gender" name="user_gender">';
$args = ['Mr.','Mrs.','Ms.'];
for($i=0;$i<count($args);$i++){
    if($args[$i]==$get_gender[0]){
        $html .='<option selected="selected">'.$args[$i].'</option>';
    }
    else{
        $html .='<option>'.$args[$i].'</option>';
    }

}
$html .= '</select>';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_fullname">Full Name';
$html .= '<input type="text" id="user_fullnames" name="user_fullname" value="'.$get_fullname[0].'" class="woocommerce-Input woocommerce-Input--password input-text">';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_date">Date of Birth';
$html .= '<input type="date" id="user_date" name="user_date" value="'.$get_date[0].'" class="woocommerce-Input woocommerce-Input--password input-text">';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_fullname">Phone Number';
$html .= '<input type="text" id="user_phone" name="user_phone" value="'.$get_phone[0].'" class="woocommerce-Input woocommerce-Input--password input-text">';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_fullname">Address';
$html .= '<input type="text" id="user_address" name="user_address" value="'.$get_address[0].'" class="woocommerce-Input woocommerce-Input--password input-text">';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_country">Country';
$html .= '<select id="user_country" name="user_country">';
$arg = ['Vietnam','Cambodia','Indonesia','Indonesia','Myanmar','Philippines','Singapore','Malaysia','Thailand'];
for($i=0;$i<count($arg);$i++){
    if($arg[$i]==$get_country[0]){
        $html .='<option selected="selected">'.$arg[$i].'</option>';
    }
    else{
        $html .='<option>'.$arg[$i].'</option>';
    }

}
$html .= '</select>';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_indentification">Identification Card';
$html .= '<p><img style="width:125px;height:125px" src="'.$img_iden[0].'"></p>';
$html .= '<input type="file" id="user_indentification" name="user_indentification" class="woocommerce-Input woocommerce-Input--password input-text">';
$html .= '</p>';
$html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
$html .= '<label for="user_passports">Passport';
$html .= '<p><img style="width:125px;height:125px" src="'.$img_pp[0].'"></p>';
$html .= '<input type="file" id="user_passports" name="user_passports" class="woocommerce-Input woocommerce-Input--password input-text">';
$html .= '</p>';
$html .= '<input type="submit" name="action" value="Update-Profile"/>';
$html .= '</from>';
echo $html;

$upload = wp_upload_dir();
$upload_dir = $upload['basedir'];
$upload_dir = $upload_dir . '/credglv/img';
if (! is_dir($upload_dir)) {
    wp_mkdir_p( $upload_dir);
}
