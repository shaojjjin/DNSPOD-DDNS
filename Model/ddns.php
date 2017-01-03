<?php
/*
 * DNSPOD
 */
namespace Model;

class Ddns {
    // 用户token
    private $token ='';
    // 需要动态ip的域名
    private $domain = '';
    // 子域名，若为    顶级域名，则为 @
    private $sub_domain = '';
    // 域名记录id
    private $domain_id = '';
    // 错误信息
    public $error = '';

    /*
     * 初始化
     */
    public function __construct($token = '', $domain = '', $sub = '')
    {
        $this->token      = $token;
        $this->domain     = $domain;
        $this->sub_domain = $sub;
        $this->domain_id  = $this->getDomainId();
    }

    /*
     * 设置当前域名记录id
     */
    private function getDomainId()
    {
        $getDomainInfo = $this->apiData('Domain.Info', array('domain' => $this->domain));
        if ($getDomainInfo['status']['code'] == 1) {
            $domainInfo      = $getDomainInfo['domain'];
            return $domainInfo['id'];
        } else {
            //TODO 记录失败信息
        }
    }
    
    /*
     *  获取域名记录信息
     */
    public function getAllRecordData($key = '', $sub = '')
    {
        $getDomainRecordData = $this->apiData('Record.List', array(
            'domain'     => $this->domain,
            'sub_domain' => $sub
        ));

        if ($getDomainRecordData['status']['code'] == 1) {
            if (empty($key)) {
                return $getDomainRecordData;
            } else {
                return $getDomainRecordData[$key];
            }
        } else {
            //TODO 记录失败信息
        }
    }

    /**
     * 获取子域名记录id
     * @param string $sub_domain
     * @return mix
     */
    public function getSubDomainRecordId($sub_domain = '')
    {
        if (empty($sub_domain)) return false;

        $recordData = $this->getAllRecordData('', $sub_domain);
        if ($recordData['status']['code'] == 1) {
            $recordInfos = $recordData['records'];
            return  $recordInfos[0]['id'];
        } else {
            //TODO 记录失败信息
        }
    }

    /*
     * 新建域名记录
     */
    public function createRecord($ip = '')
    {
        if (empty($ip)) $ip = $this->getMyIP();

        $data = array(
            'domain_id' => $this->domain_id,
            'sub_domain' => $this->sub_domain,
            'record_type' => 'A',
            'record_line' => '默认',
            'value' => $ip,
            'ttl' => '600'
        );

        $createNewRecord = $this->apiData('Record.Create', $data);

        if ($createNewRecord) {

        }
    }

    /*
     * 修改域名记录
     */
    public function modifyRecord($ip = '')
    {
        if (empty($ip)) $ip = $this->getMyIP();

        $data = array(
            'domain_id' => $this->domain_id,
            'record_id' => $this->getSubDomainRecordId($this->sub_domain),
            'sub_domain' => $this->sub_domain,
            'record_type' => 'A',
            'record_line' => '默认',
            'value' => $ip
        );

        $modifyRecord = $this->apiData('Record.Modify', $data);

        if ($modifyRecord) {

        }
    }

    /*
     * 获取用户当前ip
     */
    public function getMyIP()
    {
        $ip = file_get_contents('http://greak.net/ip');

        if (!$ip) {
            $this->error = '获取ip失败';
            return false;
        }

        return $ip;
    }

    /*
     * curl api 操作
     */
    public function apiData($api = '', $param = array(), $method = 'post', $exit = false)
    {
        if (empty($api)) {
            $this->error = '参数错误';
            return false;
        }

        $method = strtolower($method);
        $url    = API_URL . $api; //接口地址

        $userinfo =  array( //数组处理
            'login_token' => $this->token,
            'format' => 'json',
            'lang'   => 'cn',
            'error_on_empty' => 'no'
        );
        $param = array_merge($param, $userinfo);

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($param));

        $data = curl_exec($ch);

        if($method == 'post'){
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $param);
        }
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        $data = curl_exec($ch);
        curl_close($ch);

        if($exit){
            echo $url;
            var_dump($data);
        }

        return ($data === false) ? array('exception'=>1) : json_decode($data, true);
    }

}
