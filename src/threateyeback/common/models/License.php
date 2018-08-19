<?php

namespace common\models;

use Yii;

class License {

    /**
     * License model
     *
     */
    private $details = null;
    public $key = '';
    private $ciphertext = '';

    public function __construct($filePath = false) {
        if ($filePath !== false) {
            if (is_file($filePath)) {
                $bstr = file_get_contents($filePath);
            } else {
                $bstr = $filePath;
            }
            $this->ciphertext = $this->bin2bstr($bstr);
            $this->key = Yii::$app->cache->get('MachineCode');
        }
    }

    function bin2bstr($input) {
        if (!is_string($input))
            return null;
        $input = str_split($input, 4);
        $str = '';
        foreach ($input as $v) {
            $str .= base_convert($v, 2, 16);
        }
        $str = pack('H*', $str);
        return $str;
    }

    function bstr2bin($input) {
        if (!is_string($input))
            return null;
        $value = unpack('H*', $input);
        $value = str_split($value[1], 1);
        $bin = '';
        foreach ($value as $v) {
            $b = str_pad(base_convert($v, 16, 2), 4, '0', STR_PAD_LEFT);
            $bin .= $b;
        }
        return $bin;
    }

    private function createSN($details) {
        if (empty($details)) {
            return null;
        }
        ksort($details);
        $details_str = '';
        foreach ($details as $key => $value) {
            if ($key == 'SN') {
                continue;
            }
            $details_str = $details_str . $value;
        }
        $strArr = ['2', '3', '4', '5', '6', '7', '8', '9', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'];
        $hash_str = md5($details_str, true);
        $bin = $this->bstr2bin($hash_str);
        $SN = '';
        for ($i = 0; $i < 125; $i = $i + 5) {
            $num = base_convert(substr($bin, $i, 5), 2, 10);
            if (($i) % 25 == 0 && $i != 0) {
                $SN = $SN . '-';
            }
            $SN = $SN . $strArr[(int) $num];
        }
        return $SN;
    }

    private function encode() {
        $str = json_encode($this->details);
        $key = $this->key;
        $this->ciphertext = $this->authcode($str, 'ENCODE', $key, 0);
        return $this->ciphertext;
    }

    private function decode() {
        $str = $this->ciphertext;
        $key = $this->key;
        $details = json_decode($this->authcode($str, 'DECODE', $key, 0), true);
        if ($details['SN'] == $this->createSN($details)) {
            $this->details = $details;
            return $this->details;
        } else {
            return null;
        }
    }

    private function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
        $ckey_length = 4;
        $key = md5($key);
        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) :
                substr(md5(microtime()), -$ckey_length)) : '';
        $cryptkey = $keya . md5($keya . $keyc);
        $key_length = strlen($cryptkey);
        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
                sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
        $string_length = strlen($string);
        $result = '';
        $box = range(0, 255);
        $rndkey = array();
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }
        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        for ($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }
        if ($operation == 'DECODE') {
            if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
                    substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc . str_replace('=', '', base64_encode($result));
        }
    }

    public function import() {
        $conf = Config::getLicense();
        $details = $this->details;
        if (array_key_exists($details['SN'], $conf['list'])) {
            return $conf;
        }
        $conf['list'][$details['SN']] = $details;
        Config::setLicense($conf);
        News::deleteAll(['type' => News::TYPE_OVERRUN]);
        return Config::getLicense();
    }

    public function __set($name, $value) {
        if ($name == 'details' && empty($value['SN'])) {
            $value['SN'] = $this->createSN($value);
        }
        $this->$name = $value;
    }

    public function __get($name) {
        $value = $this->$name;
        if ($name == 'details' && $value == null) {
            $value = $this->decode();
        }
        if ($name == 'ciphertext') {
            $value = $this->encode();
        }
        return $value;
    }

}
