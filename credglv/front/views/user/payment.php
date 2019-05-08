<?php
$user_id = get_current_user_id();
$flag = 0;
function render_payment_html()
{
    function get_bankname()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_name')) == 0) {
            $get_bankname = [''];
        } else {
            $get_bankname = get_user_meta($user_id, 'bank_name');
        }
        return $get_bankname[0];
    }

    function get_bankbranch()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_branch')) == 0) {
            $get_bankbranch = [''];
        } else {
            $get_bankbranch = get_user_meta($user_id, 'bank_branch');
        }
        return $get_bankbranch[0];
    }

    function get_bankcountry()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_country')) == 0) {
            $get_bankcountry = [''];
        } else {
            $get_bankcountry = get_user_meta($user_id, 'bank_country');;
        }
        return $get_bankcountry[0];
    }

    function get_bankowner()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_owner')) == 0) {
            $get_bankowner = [''];
        } else {
            $get_bankowner = get_user_meta($user_id, 'bank_owner');;
        }
        return $get_bankowner[0];
    }

    function get_banknumber()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_number')) == 0) {
            $get_banknumber = [''];
        } else {
            $get_banknumber = get_user_meta($user_id, 'bank_number');
        }
        return $get_banknumber[0];
    }

    function get_bankcard(){
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'user_paymentcard')) == 0) {
            $get_bankcard = [''];
        } else {
            $get_bankcard = get_user_meta($user_id, 'user_paymentcard');
        }
        return $get_bankcard[0];
    }

    $get_bankname = get_bankname();
    $get_bankbranch = get_bankbranch();
    $get_bankcountry = get_bankcountry();
    $get_bankowner = get_bankowner();
    $get_banknumber = get_banknumber();
    $get_bankcard = get_bankcard();

    $html = '';
    $html .= '<form method="post" enctype="multipart/form-data">';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankname">Bank Name';
    $html .= '<select id="user_bankname" name="user_bankname">';
    $args = ['VNTPBANK'=>'VIETNAM - NGAN HANG TMCP TIEN PHONG (TPBANK)','VNHDB'=>'VIETNAM - NGAN HANG TMCP PHAT TRIEN TP.HCM (HDB)','VNVPBANK'=>'VIETNAM - NGAN HANG TMCP VIET NAM THINH VUONG (VPBANK)','VNMB'=>'VIETNAM - NGAN HANG TMCP QUAN DOI (MB)','VNSACOMBANK'=>'VIETNAM - NGAN HANG TMCP SAI GON THUONG TIN (SACOMBANK)','VNACB'=>'VIETNAM - NGAN HANG TMCP A CHAU (ACB)','VNEXIMBANK'=>'VIETNAM - NGAN HANG TMCP XUAT NHAP KHAU VIET NAM (EXIMBANK)','VNSCB'=>'VIETNAM - NGAN HANG TMCP SAI GON (SCB)','VNDONGABANK'=>'VIETNAM - NGAN HANG TMCP DONG A (DONGABANK)','VNSAIGONBANK'=>'VIETNAM - NGAN HANG TMCP SAI GON CONG THUONG (SAIGONBANK)','VNVID'=>'VIETNAM - PUBLIC BANK VIETNAM - VID PUBLIC BANK','VNMSB'=>'VIETNAM - NGAN HANG TMCP HANG HAI VIET NAM (MSB)','VNSHBVN'=>'VIETNAM - NGAN HANG TNHH MTV SHINHAN VIET NAM (SHBVN)','VNVIB'=>'VIETNAM - NGAN HANG TMCP QUOC TE VIB','VNTECHCOMBANK'=>'VIETNAM - NGAN HANG TMCP KY THUONG VIET NAM (TECHCOMBANK)','VNVIETCOMBANK'=>'VIETNAM - NGAN HANG TMCP NGOAI THUONG VIET NAM (VIETCOMBANK)','MALAYSIAMAYBANK'=>'MALAYSIA - MAYBANK','MALAYSIACIMBBANK'=>'MALAYSIA - CIMB BANK','MALAYSIAPUBLICBANKBERHAD'=>'MALAYSIA - PUBLIC BANK BERHAD','MALAYSIARHBBANK'=>'MALAYSIA - RHB BANK','MALAYSIAHONGLEONGBANK'=>'MALAYSIA - HONG LEONG BANK','MALAYSIAAMBANKGROUP'=>'MALAYSIA - AMBANK GROUP','MALAYSIAUNITEDOVERSEASBANK(MALAYSIA)'=>'MALAYSIA - UNITED OVERSEAS BANK (MALAYSIA)','MALAYSIABANKRAKYAT'=>'MALAYSIA - BANK RAKYAT','MALAYSIAOCBCBANK(MALAYSIA)BERHAD'=>'MALAYSIA - OCBC BANK (MALAYSIA) BERHAD','MALAYSIAHSBCBANKMALAYSIABERHAD'=>'MALAYSIA - HSBC BANK MALAYSIA BERHAD'];
    foreach($args as $key=>$value){
        if($key == $get_bankname){
            $html .='<option selected="selected" value="'.$key.'">'.$value.'</option>';
        }
        else{
            $html .='<option value="'.$key.'">'.$value.'</option>';
        }

    }
    $html .= '</select>';
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankbranch">Bank Branch';
    $html .= '<input type="text" id="user_bankbranch" name="user_bankbranch" value="' . $get_bankbranch . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankcountry">Bank Country';
    $html .= '<select id="user_bankcountry" name="user_bankcountry">';
    $args_1 = ['vietnam'=>'Vietnam','malaysia'=>'Malaysia'];
    foreach($args_1 as $key=>$value){
        if($key == $get_bankcountry){
            $html .='<option selected="selected" value="'.$key.'">'.$value.'</option>';
        }
        else{
            $html .='<option value="'.$key.'">'.$value.'</option>';
        }

    }
    $html .= '</select>';
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankowner">Bank Owner';
    $html .= '<input type="text" id="user_bankowner" name="user_bankowner" value="' . $get_bankowner . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_banknumber">Bank Number';
    $html .= '<input type="text" id="user_banknumber" name="user_banknumber" value="' . $get_banknumber . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_paymentcard">Bank Card';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><img style="width:125px;height:125px" src="'.$get_bankcard.'" alt="" class="update_img"></p>';
    $html .= '<input type="file" id="user_paymentcard" name="user_paymentcard" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankpin">Pin';
    $html .= '<input type="text" id="user_bankpin" name="user_bankpin" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';

    $html .= '<input type="submit" name="paymentclick" value="Update"/>';
    $html .= '</from>';
    echo $html;
}
function render_payment_error_html()
{   global $flag;
    function get_bankname()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_name')) == 0) {
            $get_bankname = [''];
        } else {
            $get_bankname = get_user_meta($user_id, 'bank_name');
        }
        return $get_bankname[0];
    }
    function get_bankbranch()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_branch')) == 0) {
            $get_bankbranch = [''];
        } else {
            $get_bankbranch = get_user_meta($user_id, 'bank_branch');
        }
        return $get_bankbranch[0];
    }
    function get_bankcountry()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_country')) == 0) {
            $get_bankcountry = [''];
        } else {
            $get_bankcountry = get_user_meta($user_id, 'bank_country');;
        }
        return $get_bankcountry[0];
    }
    function get_bankowner()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_owner')) == 0) {
            $get_bankowner = [''];
        } else {
            $get_bankowner = get_user_meta($user_id, 'bank_owner');;
        }
        return $get_bankowner[0];
    }
    function get_banknumber()
    {
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'bank_number')) == 0) {
            $get_banknumber = [''];
        } else {
            $get_banknumber = get_user_meta($user_id, 'bank_number');
        }
        return $get_banknumber[0];
    }
    function get_bankcard(){
        $user_id = get_current_user_id();
        if (sizeof(get_user_meta($user_id, 'user_paymentcard')) == 0) {
            $get_bankcard = [''];
        } else {
            $get_bankcard = get_user_meta($user_id, 'user_paymentcard');
        }
        return $get_bankcard[0];
    }

    $get_bankname = get_bankname();
    $get_bankbranch = get_bankbranch();
    $get_bankcountry = get_bankcountry();
    $get_bankowner = get_bankowner();
    $get_banknumber = get_banknumber();
    $get_bankcard = get_bankcard();

    $bankname= $_POST['user_bankname'];
    $bankbranch = $_POST['user_bankbranch'];
    $bankcountry = $_POST['user_bankcountry'];
    $bankowner = $_POST['user_bankowner'];
    $banknumber = $_POST['user_banknumber'];


    $html = '';
    $html .= '<form method="post" enctype="multipart/form-data">';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankname">Bank Name';
    $html .= '<select id="user_bankname" name="user_bankname">';
    $args = ['VNTPBANK'=>'VIETNAM - NGAN HANG TMCP TIEN PHONG (TPBANK)','VNHDB'=>'VIETNAM - NGAN HANG TMCP PHAT TRIEN TP.HCM (HDB)','VNVPBANK'=>'VIETNAM - NGAN HANG TMCP VIET NAM THINH VUONG (VPBANK)','VNMB'=>'VIETNAM - NGAN HANG TMCP QUAN DOI (MB)','VNSACOMBANK'=>'VIETNAM - NGAN HANG TMCP SAI GON THUONG TIN (SACOMBANK)','VNACB'=>'VIETNAM - NGAN HANG TMCP A CHAU (ACB)','VNEXIMBANK'=>'VIETNAM - NGAN HANG TMCP XUAT NHAP KHAU VIET NAM (EXIMBANK)','VNSCB'=>'VIETNAM - NGAN HANG TMCP SAI GON (SCB)','VNDONGABANK'=>'VIETNAM - NGAN HANG TMCP DONG A (DONGABANK)','VNSAIGONBANK'=>'VIETNAM - NGAN HANG TMCP SAI GON CONG THUONG (SAIGONBANK)','VNVID'=>'VIETNAM - PUBLIC BANK VIETNAM - VID PUBLIC BANK','VNMSB'=>'VIETNAM - NGAN HANG TMCP HANG HAI VIET NAM (MSB)','VNSHBVN'=>'VIETNAM - NGAN HANG TNHH MTV SHINHAN VIET NAM (SHBVN)','VNVIB'=>'VIETNAM - NGAN HANG TMCP QUOC TE VIB','VNTECHCOMBANK'=>'VIETNAM - NGAN HANG TMCP KY THUONG VIET NAM (TECHCOMBANK)','VNVIETCOMBANK'=>'VIETNAM - NGAN HANG TMCP NGOAI THUONG VIET NAM (VIETCOMBANK)','MALAYSIAMAYBANK'=>'MALAYSIA - MAYBANK','MALAYSIACIMBBANK'=>'MALAYSIA - CIMB BANK','MALAYSIAPUBLICBANKBERHAD'=>'MALAYSIA - PUBLIC BANK BERHAD','MALAYSIARHBBANK'=>'MALAYSIA - RHB BANK','MALAYSIAHONGLEONGBANK'=>'MALAYSIA - HONG LEONG BANK','MALAYSIAAMBANKGROUP'=>'MALAYSIA - AMBANK GROUP','MALAYSIAUNITEDOVERSEASBANK(MALAYSIA)'=>'MALAYSIA - UNITED OVERSEAS BANK (MALAYSIA)','MALAYSIABANKRAKYAT'=>'MALAYSIA - BANK RAKYAT','MALAYSIAOCBCBANK(MALAYSIA)BERHAD'=>'MALAYSIA - OCBC BANK (MALAYSIA) BERHAD','MALAYSIAHSBCBANKMALAYSIABERHAD'=>'MALAYSIA - HSBC BANK MALAYSIA BERHAD'];
    foreach($args as $key=>$value){
        if($key == $bankname){
            $html .='<option selected="selected" value="'.$key.'">'.$value.'</option>';
        }
        else{
            $html .='<option value="'.$key.'">'.$value.'</option>';
        }

    }
    $html .= '</select>';
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankbranch">Bank Branch';
    if($flag==2) {
        $html .= '<input type="text" id="user_bankbranch" name="user_bankbranch" value="' . $get_bankbranch . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }else{
        $html .= '<input type="text" id="user_bankbranch" name="user_bankbranch" value="' . $bankbranch . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankcountry">Bank Country';
    $html .= '<select id="user_bankcountry" name="user_bankcountry">';
    $args_1 = ['vietnam'=>'Vietnam','malaysia'=>'Malaysia'];
    foreach($args_1 as $key=>$value){
        if($key == $bankcountry){
            $html .='<option selected="selected" value="'.$key.'">'.$value.'</option>';
        }
        else{
            $html .='<option value="'.$key.'">'.$value.'</option>';
        }

    }
    $html .= '</select>';
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankowner">Bank Owner';
    if($flag==4) {
        $html .= '<input type="text" id="user_bankowner" name="user_bankowner" value="' . $get_bankowner . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }else{
        $html .= '<input type="text" id="user_bankowner" name="user_bankowner" value="' . $bankowner . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_banknumber">Bank Number';
    if($flag==5) {
        $html .= '<input type="text" id="user_banknumber" name="user_banknumber" value="' . $get_banknumber . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }
    else{
        $html .= '<input type="text" id="user_banknumber" name="user_banknumber" value="' . $banknumber . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }
    $html .= '</p>';

    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankcard">Bank Card';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide"><img style="width:125px;height:125px" src="'.$get_bankcard.'" alt="" class="update_img"></p>';
    $html .= '<input type="file" id="user_bankcard" name="user_bankcard" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankpin">Pin';
    $html .= '<input type="text" id="user_bankpin" name="user_bankpin" value="" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<input type="submit" name="paymentclick" value="Update"/>';
    $html .= '</from>';
    echo $html;
}
$upload = wp_upload_dir();
$upload_dir = $upload['basedir'];
$upload_dir = $upload_dir . '/credglv/img';
if (! is_dir($upload_dir)) {
    wp_mkdir_p( $upload_dir);
}
if(isset($_POST['paymentclick'])){
    $user_id = get_current_user_id();
    if(isset($_FILES['user_paymentcard'])) {
        if ($_FILES['user_paymentcard']['error'] <= 0) {
            $type_bc = $_FILES['user_paymentcard']['type'];
            $explode = explode('/', $type_bc);
            if ($explode[0] == 'image') {
                $from_bc = $_FILES['user_paymentcard']['tmp_name'];
                $to = wp_upload_dir()['basedir'];
                $timezone = +7;
                $time = gmdate("Y-m-j-H-i-s", time() + 3600 * ($timezone + date("I")));
                $name_bc = $user_id . '_bc_' . $time . '_' . $_FILES['user_paymentcard']['name'];
                $url = wp_upload_dir()['baseurl'] . '/credglv/img/' . $name_bc;
                $src = $to . '/credglv/img/' . $name_bc;
                if (!is_file($src)) {
                    move_uploaded_file($from_bc, $src);
                    update_user_meta($user_id, 'user_paymentcard', $url);
                }
            }
        }
    }

    $bankname= $_POST['user_bankname'];
    $bankbranch = $_POST['user_bankbranch'];
    $bankcountry = $_POST['user_bankcountry'];
    $bankowner = $_POST['user_bankowner'];
    $banknumber = $_POST['user_banknumber'];
    $bankpin = $_POST['user_bankpin'];


    if(empty($bankname) || empty($bankbranch) || empty($bankcountry) || empty($bankowner) || empty($banknumber) || empty($bankpin) || $bankpin != '7777' ) {
            if (empty($bankname)) {
                echo '<div style="background-color: red;text-align: center;color:white;">Invalid Bank Name</div>';
                global $flag ;
                $flag = 1;
            }
            else if (empty($bankbranch)) {
                echo '<div style="background-color: red;text-align: center;color:white;">Invalid Bank Branch</div>';
                global $flag ;
                $flag = 2;
            }
            else if (empty($bankcountry)) {
                echo '<div style="background-color: red;text-align: center;color:white;">Invalid Bank Country</div>';
                global $flag ;
                $flag = 3;
            }
            else if (empty($bankowner)) {
                echo '<div style="background-color: red;text-align: center;color:white;">Invalid Bank Owner</div>';
                global $flag ;
                $flag = 4;
            }
            else if (empty($banknumber)) {
                echo '<div style="background-color: red;text-align: center;color:white;">Invalid Bank Number</div>';
                global $flag ;
                $flag = 5;
            }
            else if(empty($bankpin) || $bankpin != '7777'){
                echo '<div style="background-color: red;text-align: center;color:white;">Invalid Bank Pin</div>';
                global $flag ;
                $flag = 6;
            }



    }else if(!empty($bankname) && !empty($bankbranch) && !empty($bankcountry) && !empty($bankowner) && !empty($banknumber) && !empty($bankpin) && $bankpin == '7777') {
        update_user_meta($user_id, 'bank_name', $bankname);
        update_user_meta($user_id, 'bank_branch', $bankbranch);
        update_user_meta($user_id, 'bank_country', $bankcountry);
        update_user_meta($user_id, 'bank_owner', $bankowner);
        update_user_meta($user_id, 'bank_number', $banknumber);
        $layout = '';
        $layout .= '<div style="background-color: green;text-align: center;color: white">Update Success</div>';
        echo $layout;
        $flag = 0;
    }
}
global $flag;
if($flag == 0){
    render_payment_html();
}else{
    render_payment_error_html();
}
