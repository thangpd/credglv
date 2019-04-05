<?php
/**
 * @copyright © 2019 by GLV
 * @project Cred GLV Plugin
 * @since 1.0
 *
 */

namespace credglv\helpers;


use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
use credglv\core\Config;
use credglv\core\interfaces\ControllerInterface;
use credglv\core\interfaces\HelperInterface;
use credglv\core\interfaces\PaymentGatewayInterface;


class GeneralHelper implements HelperInterface
{
    /**
     * @param $objects
     * @param $attribute
     * @return array
     */
    public function objectAttributeMap($objects, $attribute)
    {
        $attributes = [];
        foreach ($objects as $object) {
            if (property_exists($object, $attribute)) {
                $attributes[] = $object->$attribute;
            }
        }
        return $attributes;
    }


    /**
     * Render PHP file included attached params
     *
     * @param $_file_
     * @param array $_params_
     * @return string
     * @throws \Exception
     * @throws \Throwable
     */
    public function renderPhpFile($_file_, $_params_ = [])
    {
        $_obInitialLevel_ = ob_get_level();
        ob_start();
        ob_implicit_flush(false);
        extract($_params_, EXTR_OVERWRITE);
        try {
            require($_file_);
            $result = ob_get_clean();
            return $result;
        } catch (\Exception $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        } catch (\Throwable $e) {
            while (ob_get_level() > $_obInitialLevel_) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $e;
        }
    }


    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter
     * will overwrite the former (different from array_merge_recursive).
     * Recursive merging will be conducted if both arrays have an element of array
     * type and are having the same key.
     * For integer-keyed elements, the elements from the latter array will
     * be appended to the former array.
     * You can use [[UnsetArrayValue]] object to unset value from previous array or
     * [[ReplaceArrayValue]] to force replace former value instead of recursive merging.
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     * arrays via third argument, fourth argument etc.
     * @return array the merged array (the original arrays are not changed.)
     */
    public function ArrayMerge($a, $b)
    {
        $args = func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if ($v instanceof UnsetArrayValue) {
                    unset($res[$k]);
                } elseif ($v instanceof ReplaceArrayValue) {
                    $res[$k] = $v->value;
                } elseif (is_int($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (is_array($v) && isset($res[$k]) && is_array($res[$k])) {
                    $res[$k] = self::ArrayMerge($res[$k], $v);
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Get random string with defined lent
     * @param int $len
     * @return string
     */
    public function getRandomString($len = 8) {
        $hash = 'qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM1234567890';
        $code = '';
        while (strlen($code) < $len) {
            $code .= ($hash[rand(0, strlen($hash) -1 )]);
        }
        return $code;
    }


    /**
     * Fix directory path on windows
     * @param $path
     * @return mixed
     */
    public function fixPath($path) {
        $pathSep = DIRECTORY_SEPARATOR;
        return str_replace('/', $pathSep, $path);
    }

    /**
     * @param string $name
     * @return string
     */
    public function camelClassName($name)
    {
        $nameParts = preg_split('/(\s|_|\.)/i', $name);
        $name = '';
        for($i = 0; $i < count($nameParts); $i++) {
            $name .= ucfirst($nameParts[$i]);
        }
        return $name;
    }

    /**
     * Begin ajax container
     * @param $id
     */
    public function registerPjax($id, $tag = 'ul', $class = '') {
        if ((defined('DOING_AJAX') && DOING_AJAX)) {

        } else {
            echo "<{$tag} id='{$id}' class='{$class}'>";
        }
    }

    /**
     * Close ajax container
     */
    public function endPjax($tag = 'ul') {
        if ((defined('DOING_AJAX') && DOING_AJAX)) {

        } else {
            echo "</{$tag}>";
        }
    }
    /**
     * Limit words of text
     * @param  string $content
     * @param  array array $args [limit, afterStr, posStart]
     * @return string
     */
    public function limitWords($content, $args=[]) {
        $contentLimit = '';
        $content = strip_tags($content);
        extract($args);
        $total_words = substr_count($content, " " ) + 1;
        if ( empty($limit) || !is_int($limit) || $limit < 0 || $total_words <= $limit) {
            return $content;
        }
        $pos = stripos($content, " ", 0);
        if($content !== ''){
            while (strlen($content) >= $pos && $limit > 1) {
                $pos = stripos($content, " ", $pos+1);
                $limit--;
            }
            $contentLimit = substr($content, 0, $pos);
            if ( !empty($afterStr) && (strlen($content) > strlen($contentLimit)) ) {
                $contentLimit .= $afterStr;
            }
        }
        return $contentLimit; 
    }
    /**
     * @param ControllerInterface $controller
     * @param $method
     * @param array $params
     * @return mixed
     */
    /*public function ctrlBrigde(ControllerInterface $controller, $method, $params =[]) {
        return call_user_func_array([$controller, $method], $params);
    }*/


    /**
     * @param $string
     * @param int $len
     * @param string $place
     * @return string
     */
    public function subString($string, $len = 100, $place = '...')
    {
        if (strlen($string) <= $len) {
            return $string;
        }
        return substr($string, $len)  . $place;
    }


    /**
     * Get currency display of a value
     * @param $value
     * @param bool $currency
     * @return string
     */
    public function currencyFormat($value, $currency = false)
    {
        $value = (float)$value;
        
        if ($currency ===  false) {
            $currency = credglv()->config->credglv_currency;
            if (empty($currency)) {
                $currency = Config::DEFAULT_CURRENCY;
            }
        }
        $currencySymbols = array(
            'AED' => '&#1583;.&#1573;', // ?
            'AFN' => '&#65;&#102;',
            'ALL' => '&#76;&#101;&#107;',
            'AMD' => '',
            'ANG' => '&#402;',
            'AOA' => '&#75;&#122;', // ?
            'ARS' => '&#36;',
            'AUD' => '&#36;',
            'AWG' => '&#402;',
            'AZN' => '&#1084;&#1072;&#1085;',
            'BAM' => '&#75;&#77;',
            'BBD' => '&#36;',
            'BDT' => '&#2547;', // ?
            'BGN' => '&#1083;&#1074;',
            'BHD' => '.&#1583;.&#1576;', // ?
            'BIF' => '&#70;&#66;&#117;', // ?
            'BMD' => '&#36;',
            'BND' => '&#36;',
            'BOB' => '&#36;&#98;',
            'BRL' => '&#82;&#36;',
            'BSD' => '&#36;',
            'BTN' => '&#78;&#117;&#46;', // ?
            'BWP' => '&#80;',
            'BYR' => '&#112;&#46;',
            'BZD' => '&#66;&#90;&#36;',
            'CAD' => '&#36;',
            'CDF' => '&#70;&#67;',
            'CHF' => '&#67;&#72;&#70;',
            'CLF' => '', // ?
            'CLP' => '&#36;',
            'CNY' => '&#165;',
            'COP' => '&#36;',
            'CRC' => '&#8353;',
            'CUP' => '&#8396;',
            'CVE' => '&#36;', // ?
            'CZK' => '&#75;&#269;',
            'DJF' => '&#70;&#100;&#106;', // ?
            'DKK' => '&#107;&#114;',
            'DOP' => '&#82;&#68;&#36;',
            'DZD' => '&#1583;&#1580;', // ?
            'EGP' => '&#163;',
            'ETB' => '&#66;&#114;',
            'EUR' => '&#8364;',
            'FJD' => '&#36;',
            'FKP' => '&#163;',
            'GBP' => '&#163;',
            'GEL' => '&#4314;', // ?
            'GHS' => '&#162;',
            'GIP' => '&#163;',
            'GMD' => '&#68;', // ?
            'GNF' => '&#70;&#71;', // ?
            'GTQ' => '&#81;',
            'GYD' => '&#36;',
            'HKD' => '&#36;',
            'HNL' => '&#76;',
            'HRK' => '&#107;&#110;',
            'HTG' => '&#71;', // ?
            'HUF' => '&#70;&#116;',
            'IDR' => '&#82;&#112;',
            'ILS' => '&#8362;',
            'INR' => '&#8377;',
            'IQD' => '&#1593;.&#1583;', // ?
            'IRR' => '&#65020;',
            'ISK' => '&#107;&#114;',
            'JEP' => '&#163;',
            'JMD' => '&#74;&#36;',
            'JOD' => '&#74;&#68;', // ?
            'JPY' => '&#165;',
            'KES' => '&#75;&#83;&#104;', // ?
            'KGS' => '&#1083;&#1074;',
            'KHR' => '&#6107;',
            'KMF' => '&#67;&#70;', // ?
            'KPW' => '&#8361;',
            'KRW' => '&#8361;',
            'KWD' => '&#1583;.&#1603;', // ?
            'KYD' => '&#36;',
            'KZT' => '&#1083;&#1074;',
            'LAK' => '&#8365;',
            'LBP' => '&#163;',
            'LKR' => '&#8360;',
            'LRD' => '&#36;',
            'LSL' => '&#76;', // ?
            'LTL' => '&#76;&#116;',
            'LVL' => '&#76;&#115;',
            'LYD' => '&#1604;.&#1583;', // ?
            'MAD' => '&#1583;.&#1605;.', //?
            'MDL' => '&#76;',
            'MGA' => '&#65;&#114;', // ?
            'MKD' => '&#1076;&#1077;&#1085;',
            'MMK' => '&#75;',
            'MNT' => '&#8366;',
            'MOP' => '&#77;&#79;&#80;&#36;', // ?
            'MRO' => '&#85;&#77;', // ?
            'MUR' => '&#8360;', // ?
            'MVR' => '.&#1923;', // ?
            'MWK' => '&#77;&#75;',
            'MXN' => '&#36;',
            'MYR' => '&#82;&#77;',
            'MZN' => '&#77;&#84;',
            'NAD' => '&#36;',
            'NGN' => '&#8358;',
            'NIO' => '&#67;&#36;',
            'NOK' => '&#107;&#114;',
            'NPR' => '&#8360;',
            'NZD' => '&#36;',
            'OMR' => '&#65020;',
            'PAB' => '&#66;&#47;&#46;',
            'PEN' => '&#83;&#47;&#46;',
            'PGK' => '&#75;', // ?
            'PHP' => '&#8369;',
            'PKR' => '&#8360;',
            'PLN' => '&#122;&#322;',
            'PYG' => '&#71;&#115;',
            'QAR' => '&#65020;',
            'RON' => '&#108;&#101;&#105;',
            'RSD' => '&#1044;&#1080;&#1085;&#46;',
            'RUB' => '&#1088;&#1091;&#1073;',
            'RWF' => '&#1585;.&#1587;',
            'SAR' => '&#65020;',
            'SBD' => '&#36;',
            'SCR' => '&#8360;',
            'SDG' => '&#163;', // ?
            'SEK' => '&#107;&#114;',
            'SGD' => '&#36;',
            'SHP' => '&#163;',
            'SLL' => '&#76;&#101;', // ?
            'SOS' => '&#83;',
            'SRD' => '&#36;',
            'STD' => '&#68;&#98;', // ?
            'SVC' => '&#36;',
            'SYP' => '&#163;',
            'SZL' => '&#76;', // ?
            'THB' => '&#3647;',
            'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
            'TMT' => '&#109;',
            'TND' => '&#1583;.&#1578;',
            'TOP' => '&#84;&#36;',
            'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
            'TTD' => '&#36;',
            'TWD' => '&#78;&#84;&#36;',
            'TZS' => '',
            'UAH' => '&#8372;',
            'UGX' => '&#85;&#83;&#104;',
            'USD' => '&#36;',
            'UYU' => '&#36;&#85;',
            'UZS' => '&#1083;&#1074;',
            'VEF' => '&#66;&#115;',
            'VND' => '&#8363;',
            'VUV' => '&#86;&#84;',
            'WST' => '&#87;&#83;&#36;',
            'XAF' => '&#70;&#67;&#70;&#65;',
            'XCD' => '&#36;',
            'XDR' => '',
            'XOF' => '',
            'XPF' => '&#70;',
            'YER' => '&#65020;',
            'ZAR' => '&#82;',
            'ZMK' => '&#90;&#75;', // ?
            'ZWL' => '&#90;&#36;',
        );
        $currencySymbols = credglv()->hook->registerFilter('credglv_currency_symbols', $currencySymbols);
        $currency = strtoupper($currency);
        /*if (class_exists('NumberFormatter', false)) {
            $format = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            return $format->formatCurrency($value, $currency);
        }*/

        if (array_key_exists($currency, $currencySymbols)) {
            return $currencySymbols[$currency] . number_format($value, 2);
        }
        return $value;
    }

    /**
     * Get person fullname based on user settings
     * @param string $firstname
     * @param string $lastname
     * @return mixed
     */
    public function getPersonName($firstname, $lastname)
    {
        $default = '{LASTNAME} {FIRSTNAME}';
        $pos = [
            '{FIRSTNAME}' => $firstname,
            '{LASTNAME}'  => $lastname
        ];
        $default = credglv()->hook->registerFilter('credglv_person_naming', $default);
        return str_replace(array_keys($pos), array_values($pos), $default);
    }

    /**
     * @param $str
     * @return bool
     */
    public function canUnserialize($str) {
        $data = @unserialize($str);
        return $str === 'b:0;' || $data !== false;
    }

    public function baseRedirect($url) {
        $redirectHtml = <<<EOF
<meta http-equiv="refresh" content="0;URL='{$url}'">
    <script type="text/javascript">
      window.location.href='{$url}';
    </script>
EOF;
        print $redirectHtml;
        exit;
    }


    /**
     * @param $string
     * @param $array
     * @return bool
     */
    public function arrayIndex($path, $array) {
        if (preg_match_all('/\[(.*?)\]/', $path, $parts)) {
            $deps = $parts[1];
            foreach ($deps as $dep) {
                if (!isset($array[$dep])) {
                    return false;
                }
                $array = $array[$dep];
            }
            return true;
        }
    }



    /**
     * @param $string
     * @param $array
     * @return bool
     */
    public function arrayIndexSearch($path, $array) {
        if (preg_match('/\[(.*?)\]\[(.*?)\]/', $path, $parts)) {
            if( isset($array[$parts[1]]) ){
                $value = explode(',', $array[$parts[1]]);
                if( in_array($parts[2], $value) ){
                    return true;
                }

            }
        }
        return false;
    }


    /**
     * @param $str
     * @return string
     */
    public function rewriteUrl($str) {
        $str = (mb_convert_encoding($str, "UTF-8", "auto"));
        $str = preg_replace("/[^a-zA-Z0-9_| -]/", '', $str);
        return strtolower(preg_replace('/\s/', '-', $str));
    }

    /**
     * @param $buffer
     * @return mixed
     */
    public function minifyHtml($buffer) {
        $replace = array(
            //remove tabs before and after HTML tags
            '/\>[^\S ]+/s'   => '>',
            '/[^\S ]+\</s'   => '<',
            //shorten multiple whitespace sequences; keep new-line characters because they matter in JS!!!
            '/([\t ])+/s'  => ' ',
            //remove leading and trailing spaces
            '/^([\t ])+/m' => '',
            '/([\t ])+$/m' => '',
            // remove JS line comments (simple only); do NOT remove lines containing URL (e.g. 'src="http://server.com/"')!!!
            '~//[a-zA-Z0-9 ]+$~m' => '',
            //remove empty lines (sequence of line-end and white-space characters)
            '/[\r\n]+([\t ]?[\r\n]+)+/s'  => "\n",
            //remove empty lines (between HTML tags); cannot remove just any line-end characters because in inline JS they can matter!
            '/\>[\r\n\t ]+\</s'    => '><',
            //remove "empty" lines containing only JS's block end character; join with next line (e.g. "}\n}\n</script>" --> "}}</script>"
            '/}[\r\n\t ]+/s'  => '}',
            '/}[\r\n\t ]+,[\r\n\t ]+/s'  => '},',
            //remove new-line after JS's function or condition start; join with next line
            '/\)[\r\n\t ]?{[\r\n\t ]+/s'  => '){',
            '/,[\r\n\t ]?{[\r\n\t ]+/s'  => ',{',
            //remove new-line after JS's line end (only most obvious and safe cases)
            '/\),[\r\n\t ]+/s'  => '),',
            //remove quotes from HTML attributes that does not contain spaces; keep quotes around URLs!
            '~([\r\n\t ])?([a-zA-Z0-9]+)="([a-zA-Z0-9_/\\-]+)"([\r\n\t ])?~s' => '$1$2=$3$4', //$1 and $4 insert first white-space character found before/after attribute
        );

        $buffer = preg_replace(array_keys($replace), array_values($replace), $buffer);

        return $buffer;
    }

    /**
     * @param array $array
     */
    public function clearArray(&$array = []) {
        foreach ($array as $key => $value) {
            if (empty($value)) {
                unset($array[$key]);
            }
        }
    }




}