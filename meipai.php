<?php
function mobile_get($url) {
    $ch = curl_init($url); //初始化
    curl_setopt($ch, CURLOPT_HEADER, 0); // 不返回header部分
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); // 返回字符串，而非直接输出
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (iPhone; CPU iPhone OS 7_1_2 like Mac OS X) AppleWebKit/537.51.2 (KHTML, like Gecko) Mobile/11D257 MicroMessenger/6.0.1 NetType/WIFI");
    curl_setopt($ch, CURLOPT_REFERER, "-");
    $ret = curl_exec($ch);
    curl_close($ch);

    return $ret;
}
function meipai($url) {
    $fdata = array();
    $c = file_get_contents($url);

    $arr = array();
    preg_match('|<img src="([^"]+)" width="74" height="74" class="avatar pa detail-avatar"|si',$c,$arr);
    $fdata['avatar'] = trim($arr[1]);

    $arr = array();
    preg_match('|<meta content="([^"]+)" property="og:image">|si',$c,$arr);
    $fdata['screenshot'] = trim($arr[1]);

    $arr = array();
    preg_match('|data-video="([^"]+)|si',$c,$arr);
    $fdata['video_url'] = trim($arr[1]);
    $arr = array();
    preg_match('|<meta property="og:video:director" content="([^"]+)" />|si',$c,$arr);
    $fdata['author'] = trim(strip_tags($arr[1]));

    $arr = array();
    preg_match('|<meta content="([^"]+)" property="og:description"|si',$c,$arr);
    $fdata['description'] = !empty($arr[1]) ? trim(strip_tags($arr[1])) : '';

    $arr = array();
    preg_match('|<meta property="og:video:release_date" content="([^"]+)" />|si',$c,$arr);
    $ptime = date('Y-m-d H:i:s',strtotime($arr[1]));
    $fdata['publish_time'] = $ptime;

    $c = mobile_get($url);
    preg_match('|<i class="mp-iconfont">&#xe61c;</i>([^<]+)播放|si',$c,$tmp);
    $views = isset($tmp[1]) ? trim($tmp[1]) : 0;
    if(stristr($views,'万')){
        $views = str_replace("万","",$views);
        $views = $views*10000;
    }

    $fdata['meipai_views'] = $views;
    $fdata['meipai_url'] = $url;

    return $fdata;  
}

$meipai_data = meipai('http://www.meipai.com/media/531950208');
print_r($meipai_data);

/*
Array
(
    [avatar] => http://mvavatar2.meitudata.com/536c81e3795703756.jpg!thumb160
    [screenshot] => http://mvimg1.meitudata.com/575a680df0bef7339.jpg
    [video_url] => http://mvvideo1.meitudata.com/575bb0a453c616545.mp4
    [author] => 大素儿baby
    [description] => 服装拍摄花絮～
    [publish_time] => 2016-06-10 15:11:19
    [meipai_views] => 21315
    [meipai_url] => http://www.meipai.com/media/531950208
)
*/