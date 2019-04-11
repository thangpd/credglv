<?php
/*

- Use PAYTM_ENVIRONMENT as 'PROD' if you wanted to do transaction in production environment else 'TEST' for doing transaction in testing environment.
- Change the value of PAYTM_MERCHANT_KEY constant with details received from Paytm.
- Change the value of PAYTM_MERCHANT_MID constant with details received from Paytm.
- Change the value of PAYTM_MERCHANT_WEBSITE constant with details received from Paytm.
- Above details will be different for testing and production environment.

*/
$merchant_key = get_option('wmc_paytm_merchant_key');
$merchant_id = get_option('wmc_paytm_merchant_mid');
$merchant_guid = get_option('wmc_paytm_merchant_guid');
$merchant_sales_guid = get_option('wmc_paytm_merchant_sales_wallet_guid');
$currency_code = get_option('wmc_paytm_currency_code');
$paytm_enviorment = get_option('wmc_paytm_envioment');

if($paytm_enviorment == 'yes' || $paytm_enviorment == ''){
	define('PAYTM_ENVIRONMENT', 'TEST'); // PROD
}else{
	define('PAYTM_ENVIRONMENT', 'PROD'); // PROD
}
define('PAYTM_MERCHANT_KEY', $merchant_key); //Change this constant's value with Merchant key downloaded from portal
define('PAYTM_MERCHANT_MID', $merchant_id); //Change this constant's value with MID (Merchant ID) received from Paytm
define('PAYTM_MERCHANT_GUID', $merchant_guid); //Change this constant's value with MGUID (Merchant Guid) received from Paytm
define('PAYTM_SALES_WALLET_GUID', $merchant_sales_guid); //Change this constant's value with Sales Wallet Guid received from Paytm
define('PAYTM_MERCHANT_WEBSITE', site_url()); //Change this constant's value with Website name received from Paytm
define('PAYTM_CURRENCY_CODE', $currency_code);

$PAYTM_DOMAIN = "pguat.paytm.com";
$PAYTM_WALLET_DOMAIN = "trust-uat.paytm.in";
if (PAYTM_ENVIRONMENT == 'PROD') {
	$PAYTM_DOMAIN = 'secure.paytm.in';
	$PAYTM_WALLET_DOMAIN = "trust.paytm.in";
}

define('PAYTM_REFUND_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/REFUND');
define('PAYTM_STATUS_QUERY_URL', 'https://'.$PAYTM_DOMAIN.'/oltp/HANDLER_INTERNAL/TXNSTATUS');
define('PAYTM_TXN_URL', 'https://'.$PAYTM_DOMAIN.'/oltp-web/processTransaction');
define('PAYTM_GRATIFICATION_URL', 'https://'.$PAYTM_WALLET_DOMAIN.'/wallet-web/salesToUserCredit');
define('PAYTM_CHECK_STATUS_URL', 'https://'.$PAYTM_WALLET_DOMAIN.'/wallet-web/checkStatus');

?>