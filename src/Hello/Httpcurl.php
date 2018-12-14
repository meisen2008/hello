<?php
namespace Hello;
class Httpcurl{
	private $params = [];
	public function __get($property_name)
	{
		return $this->params[$property_name] ?? "no_value";
	}

	public function __set($property_name,$property_value)
	{

		$this->params[$property_name] = $property_value;

	}

	public function __call($name,$arguments)
	{
		$httpcurl = new Httpcurl();
		call_user_func_array([$httpcurl,$name], $arguments);

	}

	/**
	 * Make an HTTP request
	 * @param string $url: 请求url
	 * @param string $method: 请求方法，目前只有"GET", "POST"
	 * @param mix $data_fields: 查询数据
	 * @param array $option: 附加选项
	 * @return string HTTP请求的响应
	 */

    public function http($url, $method = 'GET', $data_fields = array(), $option = array(), $timeout = 500) {
    $_time_start = microtime(true);
    $ch          = curl_init();
    curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, $timeout);
    //curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, $timeout);
    //curl_setopt($ch, CURLOPT_TIMEOUT, 3);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_MAXREDIRS, 2);
    curl_setopt($ch, CURLOPT_NOSIGNAL, true);
    if (!empty($option['header'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $option['header']);
    }
    if (!empty($option['useragent'])) {
        curl_setopt($ch, CURLOPT_USERAGENT, $option['useragent']);
    }
    if (!empty($option['referer'])) {
        curl_setopt($ch, CURLOPT_REFERER, $option['referer']);
    }
    if (isset($option['cookiejar']) && file_exists($option['cookiejar'])) {
        curl_setopt($ch, CURLOPT_COOKIEJAR, $option['cookiejar']);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $option['cookiejar']);
    }
    if (!empty($option['cookie'])) {
        curl_setopt($ch, CURLOPT_COOKIE, $option['cookie']);
    }
    if (!empty($option['proxy'])) {
        curl_setopt($ch, CURLOPT_HTTPPROXYTUNNEL, true);
        curl_setopt($ch, CURLOPT_PROXY, $option['proxy']);
        curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5);
    }

    if (defined('CURLOPT_IPRESOLVE') && defined('CURL_IPRESOLVE_V4')) {
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    }

    // 设置查询数据
    switch ($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            if (!empty($data_fields)) {
                if (is_array($data_fields)) {
                    $data_fields = http_build_query($data_fields);
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $data_fields);
            }
            break;
        case 'GET':
        default:
            // 如果为GET方法，将$data_fields转换成查询字符串后附加到$url后面
            $join_char = strpos($url, '?') === false ? '?' : '&';
            $url .= $join_char . http_build_query($data_fields);
            break;
    }

    curl_setopt($ch, CURLOPT_URL, $url);

    if (parse_url($url, PHP_URL_SCHEME) == 'https') {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    }

    $_time_start = microtime(true);
    $response    = curl_exec($ch);

    if ($method == 'POST') {
        $action = "POST $url with " . json_encode($data_fields);
    } else {
        $action = "GET $url";
    }

    $http_info = curl_getinfo($ch);

    $log_attrs = array('total_time', 'namelookup_time', 'connect_time', 'pretransfer_time', 'starttransfer_time');
    $log_info  = array();
    foreach ($log_attrs as $key) {
        $log_info[$key] = $http_info[$key];
    }

    if ($errno = curl_errno($ch)) {
        $error   = curl_error($ch);
        $log_str = "Error at $action (errno:$errno, error:$error)";
        //Log::write($log_str);
        // return false;
    }


    $last_url  = $http_info['url'];
    $http_code = $http_info['http_code'];
    $url_log   = $url != $last_url ? ", url:$last_url" : '';
    if ($http_code != 200) {
        $log_str = "Error at HTTP_STATUS $url (http_code:$http_code {$url_log})";
        //Log::write($log_str);
    }

    curl_close($ch);
    unset($ch);

    return $response;
    }

}

