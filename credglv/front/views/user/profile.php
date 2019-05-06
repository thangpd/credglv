<?php
if (isset($_POST['uploadclick'])) {
    $user_id = get_current_user_id();
    if ($_FILES['user_passports']['error'] <= 0) {
        $type_pp = $_FILES['user_passports']['type'];
        $explode = explode('/', $type_pp);
        if ($explode[0] == 'image') {
            $from_pp = $_FILES['user_passports']['tmp_name'];
            $to = wp_upload_dir()['basedir'];
            $name_pp = $user_id . '_pp_' . $_FILES['user_passports']['name'];
            $url = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_pp;
            $src = $to . '/credglv/img/' . $name_pp;
            if (!is_file($src)) {
                move_uploaded_file($from_pp, $src);
                update_user_meta($user_id, 'passports', $url);
            }
        }
    }
    if ($_FILES['user_indentification']['error'] <= 0) {
        $type_iden = $_FILES['user_indentification']['type'];
        $explode = explode('/', $type_iden);
        if ($explode[0] == 'image') {
            $from_iden = $_FILES['user_indentification']['tmp_name'];
            $to = wp_upload_dir()['basedir'];
            $name_iden = $user_id . '_iden_' . $_FILES['user_indentification']['name'];
            $url = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_iden;
            $src = $to . '/credglv/img/' . $name_iden;
            if (!is_file($src)) {
                move_uploaded_file($from_iden, $src);
                update_user_meta($user_id, 'iden', $url);
            }
        }
    }
    $user_gender = $_POST['user_gender'];
    $user_fullname = $_POST['user_fullname'];
    $user_date = $_POST['user_date'];
    $user_phone = $_POST['user_phone'];
    $user_address = $_POST['user_address'];
    $user_country = $_POST['user_country'];
    if(empty($user_fullname)|| empty($user_phone) || !is_numeric($user_phone) ||empty($user_address)) {
        try {
            if (empty($user_fullname)) {
                throw new Exception('Invalid Full Name');
            }
            if (empty($user_phone) || !is_numeric($user_phone)) {
                throw new Exception('Invalid Phone Number');
            }
            if (empty($user_address)) {
                throw new Exception('Invalid Address');
            }
        } catch (Exception $e) {
            $layout = '';
            $layout .= '<div style="background-color: red;text-align: center">' . $e->getMessage() . '</div>';
            echo $layout;
        }
    }else if(!empty($user_fullname) && !empty($user_phone) && is_numeric($user_phone) && !empty($user_address)){
        update_user_meta($user_id, 'user_fullname', $user_fullname);
        update_user_meta($user_id, 'user_phone', $user_phone);
        update_user_meta($user_id, 'user_address', $user_address);
        update_user_meta($user_id, 'user_gender', $user_gender);
        update_user_meta($user_id, 'user_date', $user_date);
        update_user_meta($user_id, 'user_country', $user_country);
        $layout = '';
        $layout .= '<div style="background-color: green;text-align: center;color: white">Update Success</div>';
        echo $layout;
    }

}



$user_id = get_current_user_id();
$img_pp = get_user_meta($user_id, 'passports');
$img_iden = get_user_meta($user_id, 'iden');
$get_gender = get_user_meta($user_id, 'user_gender');
$get_fullname = get_user_meta($user_id, 'user_fullname');
$get_date = get_user_meta($user_id, 'user_date');
$get_phone = get_user_meta($user_id, 'user_phone');
$get_address = get_user_meta($user_id, 'user_address');
$get_country = get_user_meta($user_id, 'user_country');




$html  = '';
$html .= '<form method="post" enctype="multipart/form-data">';
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
$html .= '<input type="submit" name="uploadclick" value="Update"/>';
$html .= '</from>';
echo $html;

$upload = wp_upload_dir();
$upload_dir = $upload['basedir'];
$upload_dir = $upload_dir . '/credglv/img';
if (! is_dir($upload_dir)) {
    wp_mkdir_p( $upload_dir);
}
