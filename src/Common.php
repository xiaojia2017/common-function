<?php

namespace vendor2025\CommonFunction;


/**
 * 自定义公共方法
 *
 * @author Administrator
 */
class Common
{

    /**
     * 获取当前时间
     */
    public static function getTime()
    {
        return date('Y-m-d H:i:s');
    }

    /**
     * 获取服务器IP
     *
     * @return string
     */
    public static function getRealIp()
    {
        if (getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        } elseif (getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        } elseif (getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip ? $ip : '127.0.0.1';
    }

    /**
     * 递归创建路径
     */
    public static function mkdirs($path)
    {
        if (!file_exists($path)) {
            self::mkdirs(dirname($path));
            mkdir($path, 0777, true);
            chmod($path, 0777);
        }
    }

    public static function getfname($url)
    {
        // 返回$url最后出现"/"的位置
        $pos = strrpos($url, "/");
        $pos = $pos ? $pos : strrpos($url, "\\");
        $pos = $pos ? $pos : 0;
        if ($pos == false) {
            $pos = -1;
        }
        // 取得$url长度
        $len = strlen($url);
        if ($len < $pos) {
            return false;
        } else {
            // substr截取指定位置指定长度的子字符串
            $filename = substr($url, $pos + 1, $len - $pos - 1);
            return $filename;
        }
    }

    /**
     * 文件下载
     *
     * @param string $realName
     */
    public static function downloadFile($file, $realName = '')
    {
        // if (!is_file($file)) { die("<b>404 File not found!</b>"); }
        // Gather relevent info about file
        $len = filesize($file);
        $filename = self::getfname($file);
        $file_extension = strtolower(substr(strrchr($filename, "."), 1));
        // This will set the Content-Type to the appropriate setting for the file
        switch ($file_extension) {
            case "pdf":
                $ctype = "application/pdf";
                break;
            case "exe":
                $ctype = "application/octet-stream";
                break;
            case "zip":
                $ctype = "application/zip";
                break;
            case "doc":
                $ctype = "application/msword";
                break;
            case "xls":
                $ctype = "application/vnd.ms-excel";
                break;
            case "ppt":
                $ctype = "application/vnd.ms-powerpoint";
                break;
            case "gif":
                $ctype = "image/gif";
                break;
            case "png":
                $ctype = "image/png";
                break;
            case "jpeg":
            case "jpg":
                $ctype = "image/jpg";
                break;
            case "mp3":
                $ctype = "audio/mpeg";
                break;
            case "wav":
                $ctype = "audio/x-wav";
                break;
            case "mpeg":
            case "mpg":
            case "mpe":
                $ctype = "video/mpeg";
                break;
            case "mov":
                $ctype = "video/quicktime";
                break;
            case "avi":
                $ctype = "video/x-msvideo";
                break;
            // The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
            case "php":
            case "htm":
            case "html":
            case "txt":
                die("<b>Cannot be used for " . $file_extension . " files!</b>");
                break;
            default:
                $ctype = "application/force-download";
        }
        ob_end_clean();
        // Begin writing headers
        header("Pragma: public");
        header("Expires: 0");
        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        // Use the switch-generated Content-Type
        header("Content-Type: $ctype");
        // Force the download

        if (!empty($realName)) {
            $filename = $realName;
        }

        $header = "Content-Disposition: attachment; filename=" . $filename . ";";
        header($header);
        header("Content-Transfer-Encoding: binary");
        header("Content-Length: " . $len);
        @readfile($file);

        exit();
    }

    /**
     * 获取前一个页面url
     *
     * @return string|unknown
     */
    public static function getUrlReferrer()
    {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '/';
    }

    public static function goUrl($url)
    {
        return \Yii::$app->getResponse()->redirect($url);
    }

    public static function defaultUploadPath($type = 'img')
    {
        $path = '/' . date("Ymd") . '/' . date('YmdHis') . '-' . rand(100, 10000);
        if (!is_dir(\Yii::$app->params['upload_warehouse_img_dir'] . dirname($path))) {
            // 第三个参数是“true”表示能创建多级目录，iconv防止中文目录乱码
            mkdir(\Yii::$app->params['upload_warehouse_img_dir'] . dirname($path), 0777, true);
        }
        return $path;
    }

    /**
     * 创建数字随机码
     */
    public static function getRandomNum($length = 6)
    {
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= chr(mt_rand(48, 57));
        }
        return $code;
    }

    /**
     * 生成随机字符串
     *
     * @param number $length
     * @return string
     */
    public static function getRandomChar($length = 6)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            // 这里提供两种字符获取方式
            // 第一种是使用substr 截取$chars中的任意一位字符；
            // 第二种是取字符数组$chars 的任意元素
            // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
            $code .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $code;
    }

    public static function getCode($prefix = 'C', $userId = 0)
    {
        mt_srand((double)microtime() * 1000000);
        return strtoupper($prefix . date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT));
    }

    /**
     * 返回数据格式化信息
     *
     * @param $code 状态
     * @param string $msg 消息
     * @param array $data
     * @param array $ext 扩展参数
     * @return mixed
     */
    public static function responseJson($code, $msg = '', $data = array(), $ext = array())
    {
        $responseArr = array(
            'state' => $code,
            'msg'   => $msg,
            'data'  => $data
        );
        foreach ($ext as $k => $v) {
            $responseArr[$k] = $v;
        }
        header("Content-type: application/json");
        die(json_encode($responseArr));
    }

    /**
     * 格式化时间
     */
    public static function formatDate($date)
    {
        $formatDate = date('d-m-Y H:i', strtotime($date));
        return $formatDate;
    }

    public static function formatDateDay($date)
    {
        $formatDate = date('Y-m-d', strtotime($date));
        return $formatDate;
    }
    public static function formatDateMonthDay($date)
    {
        $formatDate = date('m-d H:i', strtotime($date));
        return $formatDate;
    }

    /**
     * 删除目录
     *
     * @param unknown $dir
     * @return boolean
     */
    public static function deldir($dir)
    {
        // 先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::deldir($fullpath);
                }
            }
        }

        closedir($dh);
        // 删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 随机生成用户名 code_17...时间戳
     */
    public static function getUserCode($type = '')
    {
        if (!empty($type)) {
            $code = $type . '_' . time();
        } else {
            $code = time();
        }
        return $code;
    }

    /**
     * 截取文字
     */
    public static function subtext($text, $length)
    {
        if (mb_strlen($text, 'utf8') > $length) {
            return mb_substr($text, 0, $length, 'utf8') . '...';
        }
        return $text;
    }

    /**
     * 二维数组排序
     *
     * @param array $arr 二维数组
     * @param string $keys 要排序的字段
     */
    public static function arraySort($arr, $keys, $type = 'desc')
    {
        $keysvalue = $new_array = array();
        foreach ($arr as $k => $v) {
            $keysvalue[$k] = $v[$keys];
        }
        if ($type == 'asc') {
            asort($keysvalue);
        } else {
            arsort($keysvalue);
        }
        reset($keysvalue);
        foreach ($keysvalue as $k => $v) {
            $new_array[$k] = $arr[$k];
        }
        return $new_array;
    }

    /**
     * 邮箱、手机账号中间字符串以*隐藏
     */
    public static function hideStar($str)
    {
        if (strpos($str, '@')) {
            $email_array = explode("@", $str);
            $prevfix = (strlen($email_array[0]) < 4) ? "" : substr($str, 0, 3); // 邮箱前缀
            $count = 0;
            $str = preg_replace('/([\d\w+_-]{0,100})@/', '***@', $str, -1, $count);
            $rs = $prevfix . $str;
        } else {
            $pattern = '/(1[3458]{1}[0-9])[0-9]{4}([0-9]{4})/i';
            if (preg_match($pattern, $str)) {
                $rs = preg_replace($pattern, '$1****$2', $str); // substr_replace($name,'****',3,4);
            } else {
                $strlen = mb_strlen($str, 'utf-8');
                $firstStr = mb_substr($str, 0, 1, 'utf-8');
                $lastStr = mb_substr($str, -1, 1, 'utf-8');
                if ($strlen < 3) {
                    $rs = $firstStr . str_repeat('*', 3);
                } else {
                    $rs = $firstStr . '***' . $lastStr;
                }
            }
        }
        return $rs;
    }

    /**
     * 超出长度用户名中间字符串以*隐藏
     */
    public static function userHideStar($str)
    {
        // 去前后空格
        $str = trim($str);
        $rs = $str;

        // 字符串长度
        $strLen = mb_strlen($str, 'utf-8');
        if ($strLen >= 4) {
            $str1 = mb_substr($str, 0, 1, 'utf-8');
            $str2 = mb_substr($str, $strLen - 1, 1, 'utf-8');
            $rs = $str1 . '***' . $str2;
        }

        return $rs;
    }

    // 截止时间
    public static function formatEndTime($time)
    {
        $time = strtotime($time) - time();
        if ($time < 0) {
            $str = '已';
        } elseif ($time < 60) {
            $str = '即将';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟后';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时后';
        } elseif ($time > 60 * 60 * 24 * 1) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '明天';
            } else {
                $str = $d . '天后';
            }
        }
        return $str;
    }

    /**
     * 获取名称
     */
    public static function getServiceNameById($serviceId)
    {
        $serviceArr = DataCache::getWarehouseService();
        return isset($serviceArr[$serviceId]) ? $serviceArr[$serviceId] : '';
    }

    /**
     * 打印方法
     */
    public static function printfInfo($data)
    {
        foreach ($data as $key => $value) {
            echo "<font color='#f00;'>$key</font> : $value <br/>";
        }
    }

    /**
     * 获取微信配置
     *
     * @return array
     */
    public static function getWechatConfig()
    {
        $wechat = \Yii::$app->params['wechat'];
        $wcArr = array(
            'token'          => $wechat['Token'],
            'encodingaeskey' => $wechat['EncodingAESKey'],
            'appid'          => $wechat['AppID'],
            'appsecret'      => $wechat['AppSecret'],
            'debug'          => ''
        );
        return $wcArr;
    }


    /**
     * function：计算两个日期相隔多少年，多少月，多少天
     * param string $date1[格式如：2011-11-5]
     * param string $date2[格式如：2012-12-01]
     * return array array('年','月','日');
     */
    public static function diffDate($date1, $date2)
    {
        if (strtotime($date1) > strtotime($date2)) {
            $tmp = $date2;
            $date2 = $date1;
            $date1 = $tmp;
        }
        list ($Y1, $m1, $d1) = explode('-', $date1);
        list ($Y2, $m2, $d2) = explode('-', $date2);
        $Y = $Y2 - $Y1;
        $m = $m2 - $m1;
        $d = $d2 - $d1;
        if ($d < 0) {
            $d += (int)date('t', strtotime("-1 month $date2"));
            $m--;
        }
        if ($m < 0) {
            $m += 12;
            $Y--;
        }
        return array(
            'year'  => $Y,
            'month' => $m,
            'day'   => $d
        );
    }

    /**
     * 计算两个日期相差多少天
     */
    public static function diffBetweenTwoDays($day1, $day2)
    {
        $second1 = strtotime($day1);
        $second2 = strtotime($day2);

        if ($second1 < $second2) {
            $tmp = $second2;
            $second2 = $second1;
            $second1 = $tmp;
        }
        return ($second1 - $second2) / 86400;
    }


    /**
     * 自动给文章关键词加超链接
     * auth:tianya
     * 2019-06-27
     */
    public static function replaceKeyword($arr, $str)
    {
        $i = 0;
        foreach ($arr as $k => $v) {
            if ($i >= 3) {
                break;
            }
            $pattern1 = '/' . $k . '/';
            preg_match_all($pattern1, $str, $matches1, PREG_OFFSET_CAPTURE);
            if (empty($matches1[0])) { //匹配不到关键词
                continue;
            } else {
                $pattern = '/' . $k . '(?![^<]*<\/a>)/';
                preg_match_all($pattern, $str, $matches, PREG_OFFSET_CAPTURE); //匹配不在a标签里面的关键词

                if (empty($matches[0])) { //关键词都在a标签里
                    continue;
                } elseif ($matches[0][0][1] == $matches1[0][0][1]) {//第一个不在a标签里的关键词所在位置是关键词第一次出现的位置
                    $a_str = "<a href='$v'>" . $k . '</a>'; //替换字符串长度
                    $start = $matches[0][0][1]; //第一次出现的位置
                    $lenth = strlen($k);
                    $str = substr_replace($str, $a_str, $start, $lenth);
                    $i++;
                }
            }
        }
        return $str;
    }

    // 人性化时间显示
    public static function formatTime($time)
    {
        $rtime = date("Y-m-d", strtotime($time));
        $htime = date("H:i", strtotime($time));
        $time = time() - strtotime($time);
        if ($time < 60) {
            $str = '刚刚';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . '分钟前';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . '小时前 ';
        } elseif ($time < 60 * 60 * 24 * 1) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = '昨天 ' . $htime;
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }

    /**
     * @param $str
     * @return string
     * php获取中文字符拼音首字母
     * 20191206
     */
    public static function getFirstCharter($str)
    {
        if (empty($str)) {
            return '';
        }
        $fchar = ord($str{0});
        if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }
        $s1 = iconv('UTF-8', 'gb2312', $str);
        $s2 = iconv('gb2312', 'UTF-8', $s1);
        $s = $s2 == $str ? $s1 : $str;
        $asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
        if ($asc >= -20319 && $asc <= -20284) {
            return 'A';
        }
        if ($asc >= -20283 && $asc <= -19776) {
            return 'B';
        }
        if ($asc >= -19775 && $asc <= -19219) {
            return 'C';
        }
        if ($asc >= -19218 && $asc <= -18711) {
            return 'D';
        }
        if ($asc >= -18710 && $asc <= -18527) {
            return 'E';
        }
        if ($asc >= -18526 && $asc <= -18240) {
            return 'F';
        }
        if ($asc >= -18239 && $asc <= -17923) {
            return 'G';
        }
        if ($asc >= -17922 && $asc <= -17418) {
            return 'H';
        }
        if ($asc >= -17417 && $asc <= -16475) {
            return 'J';
        }
        if ($asc >= -16474 && $asc <= -16213) {
            return 'K';
        }
        if ($asc >= -16212 && $asc <= -15641) {
            return 'L';
        }
        if ($asc >= -15640 && $asc <= -15166) {
            return 'M';
        }
        if ($asc >= -15165 && $asc <= -14923) {
            return 'N';
        }
        if ($asc >= -14922 && $asc <= -14915) {
            return 'O';
        }
        if ($asc >= -14914 && $asc <= -14631) {
            return 'P';
        }
        if ($asc >= -14630 && $asc <= -14150) {
            return 'Q';
        }
        if ($asc >= -14149 && $asc <= -14091) {
            return 'R';
        }
        if ($asc >= -14090 && $asc <= -13319) {
            return 'S';
        }
        if ($asc >= -13318 && $asc <= -12839) {
            return 'T';
        }
        if ($asc >= -12838 && $asc <= -12557) {
            return 'W';
        }
        if ($asc >= -12556 && $asc <= -11848) {
            return 'X';
        }
        if ($asc >= -11847 && $asc <= -11056) {
            return 'Y';
        }
        if ($asc >= -11055 && $asc <= -10247) {
            return 'Z';
        }
        return null;
    }


    /**
     * 按照 字母 分组 并 排序
     *
     * @param {Array} $list ; 需要 排序的 数据， 一维数组
     * @param {string} $field ; 排序 需要 依据 的字段，该字段 必须为 拼音
     * @return array
     */
    public static function dataLetterSort($list, $field)
    {
        $resault = array();

        foreach ($list as $key => $val) {
            // 添加 # 分组，用来 存放 首字母不能 转为 大写英文的 数据
            $resault['#'] = array();
            // 首字母 转 大写英文
            $letter = strtoupper(substr($val[$field], 0, 1));
            // 是否 大写 英文 字母
            if (!preg_match('/^[A-Z]+$/', $letter)) {
                $letter = '#';
            }
            // 创建 字母 分组
            if (!array_key_exists($letter, $resault)) {
                $resault[$letter] = array();
            }
            // 字母 分组 添加 数据
            Array_push($resault[$letter], $val);
        }
        // 依据 键名 字母 排序，该函数 返回 boolean
        ksort($resault);
        // 将 # 分组 放到 最后
        $arr_last = $resault['#'];
        unset($resault['#']);
        $resault['#'] = $arr_last;
        return $resault;
    }

    /**
     * 产生随机字符
     * @param $length
     * @param int $numeric
     * @return string
     */
    public static function random($length, $numeric = 0)
    {
        PHP_VERSION < '4.2.0' ? mt_srand((double)microtime() * 1000000) : mt_srand();
        $seed = base_convert(md5(print_r($_SERVER, 1) . microtime()), 16, $numeric ? 10 : 35);
        $seed = $numeric ? (str_replace('0', '', $seed) . '012340567890') : ($seed . 'zZ' . strtoupper($seed));
        $hash = '';
        $max = strlen($seed) - 1;
        for ($i = 0; $i < $length; $i++) {
            $hash .= $seed[mt_rand(0, $max)];
        }
        return $hash;
    }

    public static function p(...$args)
    {
        echo "<pre>";
        foreach ($args as $arg) {
            print_r($arg);
            echo "<hr>";
        }
        exit;
    }

    /**
     *  将数字id转换成字符串
     *
     * @param int $id
     * @return string
     */
    public static function createCodeById(int $id)
    {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        $num = $id;
        $code = '';
        while ($num > 0) {
            $mod = $num % 35;
            $num = ($num - $mod) / 35;
            $code = $source_string[$mod].$code;
        }
        if (empty($code[3])) {
            $code = str_pad($code, 4, '0', STR_PAD_LEFT);
        }
        return $code;
    }

    /**
     * 将code 反解为id
     *
     * @param string $code
     * @return float|int
     */
    public static function decodeToId(string $code)
    {
        static $source_string = 'E5FCDG3HQA4B1NOPIJ2RSTUV67MWX89KLYZ';
        if (strrpos($code, '0') !== false) {
            $code = substr($code, strrpos($code, '0') + 1);
        }

        $len = strlen($code);
        $code = strrev($code);

        $num = 0;
        for ($i = 0; $i < $len; $i++) {
            $num += strpos($source_string, $code[$i]) * pow(35, $i);
        }
        return $num;
    }

    /**
     * 返回用户头像，没有则使用默认头像
     * @param $avatar
     * @return string
     */
    public static function memberAvatar($avatar)
    {
        return $avatar ?: '/image/avatar.png';
    }

    /**
     * 是否合法的email
     * @param string $str
     * @return string||false
     */
    public static function isEmail($str = '')
    {
        return preg_match('/^[\w\d\_\.]+\@[\w\d\_\.\-]+\.[\w\d]{2,4}$/', str_replace('%40', '@', $str));
    }
    //公告 时间格式化（英文）
    public static function enNoticeFormatTime($time)
    {


        $time=strtotime($time);

        if ((date('Y', time())-date('Y', $time))>=1) {
            $y= (date('Y', time())-date('Y', $time));
            if ($y>1) {
                $str = $y .  ' years ago';
            } else {
                $str = $y .  ' year ago';
            }
        } elseif ((date('m', time())-date('m', $time))>=1) {
            $m= (date('m', time())-date('m', $time));
            if ($m>1) {
                $str = $m .  ' months ago';
            } else {
                $str = $m .  ' month ago';
            }
        } elseif ((date('d', time())-date('d', $time))>=1) {
            $d= (date('d', time())-date('d', $time));
            if ($d>1) {
                $str = $d .  ' days ago';
            } else {
                $str = $d .  ' day ago';
            }
        } elseif ((date('H', time())-date('H', $time))>=1) {
            $h= (date('H', time())-date('H', $time));
            if ($h>1) {
                $str = $h .  ' hours ago ';
            } else {
                $str = $h .  ' hour ago ';
            }
        } elseif ((date('i', time())-date('i', $time))>=1) {
            $min= (date('i', time())-date('i', $time));
            if ($min>1) {
                $str = $min .  ' minutes ago ';
            } else {
                $str = $min .  ' minute ago ';
            }
        } else {
            $str = 'just now';
        }
        return $str;
    }
    // 时间格式化（英文）
    public static function enFormatTime($time)
    {

        /* todo 后期考虑优化
         * $date1 = date_create("2020-06-15 12:00:00");
        $date2 = date_create("2020-06-15 13:00:00");
        $diff = date_diff($date1, $date2);
        echo $diff->format("%R%a days");*/

        $rtime = date("m-d-Y", strtotime($time));
        $htime = date("H:i", strtotime($time));
        $time = time() - strtotime($time);

        if ($time < 60) {
            $str = 'just now';
        } elseif ($time < 60 * 60) {
            $min = floor($time / 60);
            $str = $min . ' minutes ago';
        } elseif ($time < 60 * 60 * 24) {
            $h = floor($time / (60 * 60));
            $str = $h . ' hours ago ';
        } elseif ($time < 60 * 60 * 24 * 7) {
            $d = floor($time / (60 * 60 * 24));
            if ($d == 1) {
                $str = 'yesterday ' . $htime;
            } else {
                $str = $d . ' days ago';
            }
        } else {
            $str = $rtime;
        }
        return $str;
    }

    public static function enMonth($time)
    {
        if (!empty($time)) {
            $month = date('m', strtotime($time));
            $time = str_replace(
                array('01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12'),
                array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'),
                $month
            );
        }
        return $time;
    }

    public static function errorsMsg($array)
    {
        return array_values($array)[0];
    }

    //字符串截取
    public static function subStrReplace($str, $start = 3, $end = -3)
    {

        return substr_replace($str, '***', $start, $end);
    }
}
