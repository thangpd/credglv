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

    $get_bankname = get_bankname();
    $get_bankbranch = get_bankbranch();
    $get_bankcountry = get_bankcountry();
    $get_bankowner = get_bankowner();
    $get_banknumber = get_banknumber();

    $html = '';
    $html .= '<form method="post">';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankname">Bank Name';
    $html .= '<input type="text" id="user_bankname" name="user_bankname" value="' . $get_bankname . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankbranch">Bank Branch';
    $html .= '<input type="text" id="user_bankbranch" name="user_bankbranch" value="' . $get_bankbranch . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankcountry">Country of bank';
    $html .= '<input type="text" id="user_bankcountry" name="user_bankcountry" value="' . $get_bankcountry . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankowner">Bank Owner';
    $html .= '<input type="text" id="user_bankowner" name="user_bankowner" value="' . $get_bankowner . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    $html .= '</p>';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_banknumber">Bank Number';
    $html .= '<input type="text" id="user_banknumber" name="user_banknumber" value="' . $get_banknumber . '" class="woocommerce-Input woocommerce-Input--password input-text">';
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

    $get_bankname = get_bankname();
    $get_bankbranch = get_bankbranch();
    $get_bankcountry = get_bankcountry();
    $get_bankowner = get_bankowner();
    $get_banknumber = get_banknumber();

    $bankname= $_POST['user_bankname'];
    $bankbranch = $_POST['user_bankbranch'];
    $bankcountry = $_POST['user_bankcountry'];
    $bankowner = $_POST['user_bankowner'];
    $banknumber = $_POST['user_banknumber'];

    $html = '';
    $html .= '<form method="post">';
    $html .= '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
    $html .= '<label for="user_bankname">Bank Name';
    if($flag==1) {
        $html .= '<input type="text" id="user_bankname" name="user_bankname" value="' . $get_bankname . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }else{
        $html .= '<input type="text" id="user_bankname" name="user_bankname" value="' . $bankname . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }
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
    $html .= '<label for="user_bankcountry">Country of bank';
    if($flag==3) {
        $html .= '<input type="text" id="user_bankcountry" name="user_bankcountry" value="' . $get_bankcountry . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }else{
        $html .= '<input type="text" id="user_bankcountry" name="user_bankcountry" value="' . $bankcountry . '" class="woocommerce-Input woocommerce-Input--password input-text">';
    }
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
    $html .= '<input type="submit" name="paymentclick" value="Update"/>';
    $html .= '</from>';
    echo $html;
}
if(isset($_POST['paymentclick'])){
    $bankname= $_POST['user_bankname'];
    $bankbranch = $_POST['user_bankbranch'];
    $bankcountry = $_POST['user_bankcountry'];
    $bankowner = $_POST['user_bankowner'];
    $banknumber = $_POST['user_banknumber'];
    if(empty($bankname) || empty($bankbranch) || empty($bankcountry) || empty($bankowner) || empty($banknumber)) {
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


    }else if(!empty($bankname) && !empty($bankbranch) && !empty($bankcountry) && !empty($bankowner) && !empty($banknumber)) {
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
