<?php
require_once("lib/FileSystemCache.php");

// ��ά�����url
$qring = 'http://qring.org/decode?url=';

// FreeSS �Ķ�ά��ڵ�
$freess_url = array(
    'jp01' => 'https://freess.cx/images/servers/jp01.png',
    'jp02' => 'https://freess.cx/images/servers/jp02.png',
    'jp03' => 'https://freess.cx/images/servers/jp03.png',
    'us01' => 'https://freess.cx/images/servers/us01.png',
    'us02' => 'https://freess.cx/images/servers/us02.png',
    'us03' => 'https://freess.cx/images/servers/us03.png',
);

// ���û���KEY
$cache_key = FileSystemCache::generateCacheKey('subscribe');

// ��ȡ����
$subscribe = FileSystemCache::retrieve($cache_key);

if($subscribe === false){// ���治���������»�ȡ�ڵ�
	foreach($freess_url as $key=>$url){
		$jsontext = get_url_content($qring . $url, "http://qring.org");     // ƴװurl���� qring.org �����ά��
		$json = json_decode($jsontext, true);
		if($json && $json['errCode'] == 0){
			$freess[$key] = $json['data']['text'];
			$subscribe .= $freess[$key] . '#FreeSS - ' . $key . chr(10); // ƴ�ӳɶ��ĵ�ԭʼ���ݸ�ʽ �ĵ��� https://github.com/ssrbackup/shadowsocks-rss/wiki/Subscribe-%E6%9C%8D%E5%8A%A1%E5%99%A8%E8%AE%A2%E9%98%85%E6%8E%A5%E5%8F%A3%E6%96%87%E6%A1%A3
		}
	}
    $subscribe = substr($subscribe, 0, -1); // ���һ��ɾ�����з�
	FileSystemCache::store($cache_key, $subscribe, 1800);   // ��������(��Ч�ڰ�Сʱ)
}


if(!empty($subscribe)){
	echo base64_encode($subscribe);     // ������ĵ�����
}

/*
* ץȡҳ������
*/
function get_url_content($url, $referer="") {
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $ch = curl_init();
    $timeout = 5;
    $urlarr     = parse_url($url);
    if($urlarr["scheme"] == "https") {	//�ж�URL�Ƿ�Ϊhttps����
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    }
    curl_setopt ($ch, CURLOPT_URL, $url);
    if($referer!="") curl_setopt($ch, CURLOPT_REFERER, $referer);
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_USERAGENT, $user_agent);
    $file_contents = curl_exec($ch);
    curl_close($ch);
	return $file_contents;
}