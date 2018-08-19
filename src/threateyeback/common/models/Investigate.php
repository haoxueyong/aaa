<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace common\models;

/**
 * 安全调查的操作类
 *
 * @author yong
 */
class Investigate {

    //DNS调查页面展示使用
    public static function DnsInvestigation($function = '', $filter_data = []) {
        $data = redisCommunication($function, $filter_data);
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        $return = self::changeData($data);
        //分页
        $final_data = paging($filter_data['current_page'], $filter_data['per_page_count'], $data['total_count']);
        $final_data['data'] = $return;
        return $final_data;
    }

    //dns调查下载使用
    public static function DnsInvestigationExport($function = '', $filter_data = []) {
        $data = redisCommunication($function, $filter_data);
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        $return = self::changeData($data);
        //分页
        return $return;
    }

    //安全调查页面展示使用
    public static function SafetyInvestigation($function = '', $filter_data = []) {
        $data = redisCommunication($function, $filter_data);
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        //分页
        $final_data = paging($filter_data['current_page'], $filter_data['per_page_count'], $data['total_count']);
        $final_data['data'] = $data;
        return $final_data;
    }

    //安全调查下载使用（导出）
    public static function SafetyInvestigationExport($function = '', $filter_data = []) {
        $data = redisCommunication($function, $filter_data);
        //如果返回错误信息，则直接返回
        if (is_string($data)) {
            return $data;
        }
        return $data;
    }

    //被DNS调查的两个方法调用
    private function changeData($data) {
        $return = [];
        foreach ($data['data'] as $k => $v) {
            if ($v['dns']['type'] == 'query') {
                $return[$k]['host_ip'] = $v['src_ip'];
                $return[$k]['dns_ip'] = $v['dest_ip'];
            } else if ($v['dns']['type'] == 'answer') {
                $return[$k]['host_ip'] = $v['dest_ip'];
                $return[$k]['dns_ip'] = $v['src_ip'];
                $return[$k]['rdata'] = $v['dns']['rdata'];
                $return[$k]['ttl'] = $v['dns']['ttl'];
                $return[$k]['rcode'] = $v['dns']['rcode'];
            }
            $return[$k]['type'] = $v['dns']['type'];
            $return[$k]['rrtype'] = $v['dns']['rrtype'];
            $return[$k]['rrname'] = $v['dns']['rrname'];
            $return[$k]['id'] = $v['dns']['id'];
            $return[$k]['timestamp'] = date('Y-m-d H:i:s', strtotime($v['timestamp']));
        }
        return $return;
    }

}
