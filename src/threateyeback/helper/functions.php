<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of functions
 * 自定义函数
 * @author lenovo
 */
//打印变量的函数
function p($arr) {
    echo '<pre style="border:1px solid grey;padding:10px;border-radius:5px;background:#ccc">';
    print_r($arr);
    echo '</pre>';
}

/**
 * 通过判断返回值确定restful返回时的格式
 * @param  $data 需要验证的参数值
 * @return array
 */
function return_format($data, $error_code = 0) {
    if ($error_code) {
        $return = ['status' => $error_code, 'msg' => $data, 'data' => ''];
        return $return;
    }
    if ($data && is_string($data)) {
        $return = ['status' => 1, 'msg' => $data, 'data' => ''];
    } else if ($data && is_bool($data)) {
        $return = ['status' => 0, 'msg' => '', 'data' => ""];
    } else if ($data && is_array($data)) {
        $return = ['status' => 0, 'msg' => '', 'data' => $data];
    } else if ($data == null) {
        $return = ['status' => 0, 'msg' => '', 'data' => $data];
    }
    return $return;
}

/**
 * 和redis通信的时候生成key
 * @param  
 * @return array
 */
function getRedisCommunicationKey($query) {
    $time = microtime();
    $str = $query . "_" . Yii::$app->user->identity->id . substr($time, -3) . substr($time, 2, 6) . mt_rand(10000, 99999);
    $key['request_key'] = 'request::' . $str;
    $key['reply_key'] = 'reply::' . $str;
    $key['commond_id'] = $str;
    return $key;
}

/**
 * 封装PHP和redis的通信
 * @param  
 * @return array
 */
function redisCommunication($function = '', $filter_data = []) {
    $redis = Yii::$app->redis8;
    //获取超时时间
    $timeoutRedisPhp = Yii::$app->params['timeoutRedisPhp'] * 10;
    $key = getRedisCommunicationKey($function);
    $search_data['func'] = $function;
    $search_data['para'] = $filter_data;
    $write_result1 = $redis->set($key['request_key'], json_encode($search_data));
    //设置过期时间
    $redis->expire($key['request_key'], 120);
    $write_result2 = $redis->lpush('request', $key['commond_id']);
    if ($write_result1 != 1 || $write_result2 < 1) {
        return '请求发送失败';
    }
    $return_data = [];
    for ($i = 0; $i < $timeoutRedisPhp; $i++) {
        usleep(100000);
        $key_exist = $redis->exists($key['reply_key']);
        if ($key_exist == 1) {
            $return_data = $redis->get($key['reply_key']);
            break;
        }
    }
    if (empty($return_data)) {
        return '请求超时';
    }
    //判断返回值
    $data = analysisRedisReturn(json_decode($return_data, true));
    return $data;
}

/**
 * 解析后端的返回值
 * @param  
 * @return array
 */
function analysisRedisReturn($return) {
    //错误时直接返回提示
    switch ($return['ret_code']) {
        case 0:
            $data = $return['result'];
            break;
//        case 1:
//            $data['total_count'] = 0;
//            $data['data'] = [];
//            break;
        case 2:
            $data = '查询结果超过1000条,请重置查询条件';
            break;
        case 3:
            $data = '文件上传失败';
            break;
        default:
            $data = '系统等其他错误';
    }
    return $data;
}

/**
 * 封装分页的方法
 * @param  
 * @return array
 */
function paging($current_page = 1, $per_page_count = 15, $total_count = 0) {
    $maxPage = ceil($total_count / $per_page_count);
    $page = $current_page > $maxPage ? $maxPage : $current_page;
    return ['count' => $total_count, 'pageNow' => intval($page), 'maxPage' => $maxPage, 'rows' => intval($per_page_count)];
}

/**
 * api调用
 * @param  
 * @return array
 */
function isAPI() {
    $headers = Yii::$app->request->headers;
    if (stristr($headers['accept'], 'application/json') !== false) {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
    } else {
        Yii::$app->response->format = \yii\web\Response::FORMAT_XML;
    }
}

/**
 * 判断ip是否在某个网段
 * @param  
 * @return true是，false否
 */
//function checkIPInNetworkSegment($ip, $network_segment, $net_mask) {
//    $arr = explode('.', $net_mask);
//    p($arr);
//    die;
//    $mask1 = 32 - $net_mask;
//    return ((ip2long($ip) >> $mask1) == (ip2long($network_segment) >> $mask1));
//}
