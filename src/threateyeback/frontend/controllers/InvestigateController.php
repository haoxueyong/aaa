<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use common\models\Config;
use common\models\Investigate;
use common\models\IocScanning;

/**
 * 安全调查 controller
 */
class InvestigateController extends Controller {

    /**
     * @inheritdoc
     */
    public function behaviors() {
        if (Config::getLicense()['validLicenseCount'] == 0) {
            $rules = [];
        } else {
            $rules = [
                ['actions' => [], 'allow' => false, 'roles' => ['?']],
                ['actions' => [], 'allow' => true, 'roles' => ['admin']],
                ['actions' => ['ioc-scanning-del', 'download-ioc-template', 'upload-file'], 'allow' => false, 'roles' => ['@']]
            ];
        }
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['dns-investigation', 'dns-investigation-export', 'ipurl-communication-investigation', 'ipurl-communication-investigation-export', 'file-investigation', 'file-investigation-export', 'flowsize-timelength-investigation', 'flowsize-timelength-investigation-export', 'flow-direction-investigation', 'flow-direction-investigation-export', 'ioc-scanning-list', 'ioc-scanning-download-test', 'ioc-scanning-download', 'ioc-scanning-del', 'download-ioc-template', 'upload-file'],
                'rules' => $rules
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                // 'logout' => ['post'],
                // 'test' => ['post'],
                ]
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions() {
        return [
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionError() {
        return return_format('该页面不存在！', 404);
    }

    /**
     * Displays homepage.
     *
     * @return mixed
     */
    public $enableCsrfValidation = false;

    //DNS调查（页面列表）
    public function actionDnsInvestigation() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::DnsInvestigation('DnsSearch', $filter_data);
        return return_format($data);
    }

    //DNS调查（下载）
    public function actionDnsInvestigationExport() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::DnsInvestigationExport('DnsSearch', $filter_data);
        if (is_string($data)) {
            return return_format($data);
        }
        $EXCEL_OUT = iconv('UTF-8', 'GBK', "时间,DNS服务器IP,主机IP,类型,域名,解析IP,TTL\n");
        foreach ($data as $item) {
            $rrname = array_key_exists('rrname', $item) ? $item['rrname'] : '';
            $rdata = array_key_exists('rdata', $item) ? $item['rdata'] : '';
            $ttl = array_key_exists('ttl', $item) ? $item['ttl'] : '';
            try {
                $line = iconv('UTF-8', 'GBK//IGNORE', $item['timestamp'] . ',' .
                        $item['dns_ip'] . ',' .
                        $item['host_ip'] . ',' .
                        $item['type'] . ',' .
                        $rrname . ',' .
                        $rdata . ',' .
                        $ttl . ',' .
                        "\n"
                );
            } catch (Exception $e) {
                break;
            }
            $EXCEL_OUT .= $line;
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=DNS调查_" . date("Y.m.d", $filter_data['start_time']) . "-" . date("Y.m.d", $filter_data['end_time']) . ".csv");
        echo $EXCEL_OUT;
        exit();
    }

    //IPURL通信调查（页面列表）
    public function actionIpurlCommunicationInvestigation() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigation('IPURLSearch', $filter_data);
        return return_format($data);
    }

    //IPURL通信调查（下载）
    public function actionIpurlCommunicationInvestigationExport() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigationExport('IPURLSearch', $filter_data);
        if (is_string($data)) {
            return return_format($data);
        }
        $EXCEL_OUT = iconv('UTF-8', 'GBK', "时间,源IP,源端口,目的IP,目的端口,Email地址,应用\n");
        foreach ($data['data'] as $item) {
            try {
                $line = iconv('UTF-8', 'GBK//IGNORE', $item['timestamp'] . ',' .
                        $item['src_ip'] . ',' .
                        $item['src_port'] . ',' .
                        $item['dest_ip'] . ',' .
                        $item['dest_port'] . ',' .
                        $item['email'] . ',' .
                        $item['application'] . ',' .
                        "\n"
                );
            } catch (Exception $e) {
                break;
            }
            $EXCEL_OUT .= $line;
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=IPURL调查_" . date("Y.m.d", $filter_data['start_time']) . "-" . date("Y.m.d", $filter_data['end_time']) . ".csv");
        echo $EXCEL_OUT;
        exit();
    }

    //文件调查（页面列表）
    public function actionFileInvestigation() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigation('FileSearch', $filter_data);
        return return_format($data);
    }

    //文件调查（下载）
    public function actionFileInvestigationExport() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigationExport('FileSearch', $filter_data);
        if (is_string($data)) {
            return return_format($data);
        }
        $EXCEL_OUT = iconv('UTF-8', 'GBK', "时间,文件名,哈希值,来源,主机IP,应用\n");
        foreach ($data['data'] as $item) {
            try {
                $line = iconv('UTF-8', 'GBK//IGNORE', $item['timestamp'] . ',' .
                        $item['file_name'] . ',' .
                        $item['md5'] . ',' .
                        $item['source'] . ',' .
                        $item['host_ip'] . ',' .
                        $item['application'] . ',' .
                        "\n"
                );
            } catch (Exception $e) {
                break;
            }
            $EXCEL_OUT .= $line;
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=文件调查_" . date("Y.m.d", $filter_data['start_time']) . "-" . date("Y.m.d", $filter_data['end_time']) . ".csv");
        echo $EXCEL_OUT;
        exit();
    }

    //流量大小及时长调查（页面列表）
    public function actionFlowsizeTimelengthInvestigation() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigation('FlowSizeSearch', $filter_data);
        return return_format($data);
    }

    //流量大小及时长调查（下载）
    public function actionFlowsizeTimelengthInvestigationExport() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigationExport('FlowSizeSearch', $filter_data);
        if (is_string($data)) {
            return return_format($data);
        }
        $EXCEL_OUT = iconv('UTF-8', 'GBK', "时间,主机IP,流量,链接时长,目的地址,应用\n");
        foreach ($data['data'] as $item) {
            try {
                $line = iconv('UTF-8', 'GBK//IGNORE', $item['timestamp'] . ',' .
                        $item['host_ip'] . ',' .
                        $item['flow_bytes'] . ',' .
                        $item['flow_duration'] . ',' .
                        $item['dest_ip'] . ',' .
                        $item['application'] . ',' .
                        "\n"
                );
            } catch (Exception $e) {
                break;
            }
            $EXCEL_OUT .= $line;
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=流量大小及时长调查_" . date("Y.m.d", $filter_data['start_time']) . "-" . date("Y.m.d", $filter_data['end_time']) . ".csv");
        echo $EXCEL_OUT;
        exit();
    }

    //流量方向调查（页面列表）
    public function actionFlowDirectionInvestigation() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigation('FlowDirectionSearch', $filter_data);
        return return_format($data);
    }

    //流量方向调查（下载）
    public function actionFlowDirectionInvestigationExport() {
        isAPI();
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $filter_data = Yii::$app->request->get();
        }
        if (empty($filter_data)) {
            return return_format('参数错误');
        }
        $data = Investigate::SafetyInvestigationExport('FlowDirectionSearch', $filter_data);
        if (is_string($data)) {
            return return_format($data);
        }
        $EXCEL_OUT = iconv('UTF-8', 'GBK', "时间,主机IP,流量,链接时长,目的地址,应用\n");
        foreach ($data['data'] as $item) {
            try {
                $line = iconv('UTF-8', 'GBK//IGNORE', $item['timestamp'] . ',' .
                        $item['host_ip'] . ',' .
                        $item['flow_bytes'] . ',' .
                        $item['flow_duration'] . ',' .
                        $item['dest_ip'] . ',' .
                        $item['application'] . ',' .
                        "\n"
                );
            } catch (Exception $e) {
                break;
            }
            $EXCEL_OUT .= $line;
        }
        header("Content-type:text/csv");
        header("Content-Disposition:attachment; filename=流量方向调查_" . date("Y.m.d", $filter_data['start_time']) . "-" . date("Y.m.d", $filter_data['end_time']) . ".csv");
        echo $EXCEL_OUT;
        exit();
    }

    //IOC扫描列表
    public function actionIocScanningList($page = 1, $rows = 15) {
        isAPI();
        if (Yii::$app->request->isGet) {
            $get = Yii::$app->request->get();
            $page = empty($get['page']) ? $page : $get['page'];
            $rows = empty($get['rows']) ? $rows : $get['rows'];
        }
        $query = IocScanning::find()->orderBy('id DESC')->select('id,upload_file_name,create_percent,create_status,create_time,download_file_name');
        $page = (int) $page;
        $rows = (int) $rows;
        $count = (int) $query->count();
        $maxPage = ceil($count / $rows);
        $page = $page > $maxPage ? $maxPage : $page;
        $pageData = $query->offSet(($page - 1) * $rows)->limit($rows)->asArray()->all();

        $data = [
            'data' => $pageData,
            'count' => $count,
            'maxPage' => $maxPage,
            'pageNow' => $page,
        ];
        return return_format($data);
    }

    //IOC文件下载之前的监测
    public function actionIocScanningDownloadTest() {
        isAPI();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        if (!Yii::$app->request->isGet) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isGet) {
            $ioc_id = Yii::$app->request->get('id');
        }
        if (empty($ioc_id)) {
            return return_format('参数错误');
        }
        //获取当前ioc的信息
        $ioc_info = IocScanning::find()->where(['=', 'id', $ioc_id])->asArray()->one();
        if ($ioc_info['create_status'] != 1) {
            return return_format('文件创建中，请稍后重试');
        }
        if (!file_exists('/opt/threatEye/ioc/' . $ioc_info['download_file_name'])) {
            return return_format('文件创建失败');
        }
        return return_format(true);
    }

    //IOC文件下载
    public function actionIocScanningDownload() {
        isAPI();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        $ioc_id = Yii::$app->request->get('id');
        //获取当前ioc的信息
        $ioc_info = IocScanning::find()->where(['=', 'id', $ioc_id])->asArray()->one();
        \YII::$app->response->sendFile('/opt/threatEye/ioc/' . $ioc_info['download_file_name']);
    }

    //IOC文件删除
    public function actionIocScanningDel() {
        isAPI();
        if (!Yii::$app->request->isDelete) {
            return return_format('请求失败');
        }
        if (Yii::$app->request->isDelete) {
            $ioc_id = json_decode(Yii::$app->request->getRawBody(), true)['id'];
        }
        if (empty($ioc_id)) {
            return return_format('参数错误');
        }
        //获取当前ioc的信息
        $ioc_info = IocScanning::find()->where(['=', 'id', $ioc_id])->asArray()->one();
        if (empty($ioc_info)) {
            return return_format('删除失败');
        }
        //删除数据库
        IocScanning::deleteAll(['=', 'id', $ioc_id]);
        //删除文件
        if (file_exists('/opt/threatEye/ioc/' . $ioc_info['download_file_name'])) {
            unlink('/opt/threatEye/ioc/' . $ioc_info['download_file_name']);
        }
        return return_format(true);
    }

    //下载ioc模板
    public function actionDownloadIocTemplate() {
        isAPI();
        $frontend_url = Yii::getAlias('@frontend');
        \YII::$app->response->sendFile($frontend_url . '/web/downloadfile/IOC.txt');
    }

    //上传文件
    public function actionUploadFile() {
        isAPI();
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        //当上传错误时
        if (empty($_FILES['file']['name'])) {
            return return_format('请选择.txt或.ioc的文件，重新上传');
        }
        if (strlen($_FILES['file']['name']) > 255) {
            return return_format('文件名应小于255个字符');
        }
        //生成上传目录
        $upload_dir = '/opt/threatEye/ioc/';
        $tmp_file = $_FILES['file']['tmp_name'];
        if (filesize($tmp_file) / 1024 / 1024 > 10) {
            return return_format('请选择小于10M的文件，重新上传');
        }
        $file_types = explode(".", $_FILES ['file'] ['name']);
        $file_type = $file_types[count($file_types) - 1];
        //生成唯一的文件名
        $time = microtime();
        $str = Yii::$app->user->identity->id . substr($time, -3) . substr($time, 2, 6) . mt_rand(10000, 99999);
        $file_name = $str . "." . $file_type;
        //判别是不是.txt和.ioc文件
        if (strtolower($file_type) != "txt" && strtolower($file_type) != "ioc") {
            return return_format('请选择.txt或.ioc的文件，重新上');
        }
        //判断用户传的是ioc还是txt
        if (strtolower($file_type) == 'ioc') {
            //当类型为ioc时，特殊处理
            $MD5 = "MD5\r\n";
            $IP = "IP\r\n";
            $URL = "URL\r\n";
//            $Domain = "Domain\r\n";
            //获取文件内容
            $file_contents = file_get_contents($tmp_file);
            //解析内容
            $xml = simplexml_load_string($file_contents);
            $xmljson = json_encode($xml);
            $file_contents_arr = json_decode($xmljson, true)['definition']['Indicator']['IndicatorItem'];
            //解析文件内容
            foreach ($file_contents_arr as $value) {
                switch ($value['Context']['@attributes']['search']) {
                    case 'FileItem/Md5sum':
                        $MD5 .= $value['Content'] . "\r\n";
                        break;
                    case 'RouteEntryItem/Destination':
                        $IP .= $value['Content'] . "\r\n";
                        break;
                    case 'UrlHistoryItem/URL':
                        $URL .= $value['Content'] . "\r\n";
                        break;
                    case 'Network/DNS':
                        $URL .= $value['Content'] . "\r\n";
                        break;
                    default:
                        break;
                }
            }
            //这是发给引擎的字符串
            $contents = $MD5 . $IP . $URL;
            if (substr_count($contents, "\r\n") > 103) {
                return return_format('IOC文件中记录数不应大于100条');
            }
            $myfile = fopen($upload_dir . $str . '.txt', "w") or die("Unable to open file!");
            fwrite($myfile, $contents);
            fclose($myfile);
        } else if (strtolower($file_type) == 'txt') {
            $file = fopen($tmp_file, "r");
            $i = 0;
            $contents = [];
            while (!feof($file)) {
                $content = fgets($file);
                if (!in_array(trim($content), ['MD5', 'IP', 'URL', 'Domain', ''])) {
                    $contents[$i] = $content;
                }
                $i++;
            }
            fclose($file);
            if (count($contents) > 100) {
                return return_format('IOC文件中记录数不应大于100条');
            }
            if (!copy($tmp_file, $upload_dir . $str . '.txt')) {
                return return_format('文件上传失败，请重试');
            }
        }
        //通知后台
        $data = redisCommunication('IocScanning', ['upload_file_name' => $_FILES['file']['name'], 'txt_file_name' => $str . '.txt']);
        return return_format($data);
    }

}
