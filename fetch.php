<?php
$fileExt = 'html';
$site = 'http://www.saafashion.com';

$saved = array();
$todo = array();
$result = array();

fetch($site);

function fetch($url){
    global $fileExt;
    global $site;
    global $saved;
    global $result;

    if(isset($saved[$url])){
        return false;
    }

    $res = _curlHostRequest($url);
    if(!empty($res)){
        if(preg_match('/<title>(.*)<\/title>/', $res, $match)){
            $title = $match[1];
            $result[] = array(
                'title' => $title,
                'url' => $url
            );
            $saved[$url] = 1;
        }
        if(preg_match_all("/href=\"(.*?\.({$fileExt}))/", $res, $match)){
            foreach($match[1] as $d){
                if(strpos($d, 'http://') !== 0){
                    $d = strpos($d, '/') === 0 ? "{$site}{$d}" : "{$site}/{$d}";
                }
                fetch($d);
            }
        }
    }
}

/**
 *  @brief 根据HOST获取内容 by BenjaminLeung 2014.03.28
 *
 *  @param [in] $posturl  请求链接
 *  @param [in] $postvars post data
 *  @param [in] $timeout  请求超时，单位秒
 *  @param [in] $host     host
 *  @return 抓取内容
 *
 */
function _curlHostRequest($posturl, $postvars = null, $timeout = 5, $host = '') {
    $ch = curl_init($posturl);
    if (!empty($postvars)){
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postvars);
    }
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_DNS_USE_GLOBAL_CACHE, false);

    $cookie = '';
    if(!empty($_COOKIE)){
        foreach($_COOKIE as $k => $v){
            $cookie .= "{$k}={$v}; ";
        }
        $cookie = substr($cookie, 0, -2);
    }

    curl_setopt($ch, CURLOPT_COOKIE, $cookie);

    if(!empty($host)){
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Host: {$host}"));
    }

    $Rec_Data = curl_exec($ch);
    curl_close($ch);
    return $Rec_Data;
}
