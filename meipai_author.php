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
function meipai_get($url,$refer='') {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $headers = array();
    $headers[] = 'Cache-Control: no-cache, must-revalidate';
    $headers[] = 'Connection: keep-alive';
    $headers[] = 'Content-Encoding: gzip';
    $headers[] = 'Pragma: no-cache';
    $headers[] = 'Server: Tengine';

    $headers[] = 'ATransfer-Encoding:chunked';
    $headers[] = 'X-Via:1.1 yc6:8111 (Cdn Cache Server V2.0), 1.1 wenzhoudianxin10:5 (Cdn Cache Server V2.0)';


    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 
    curl_setopt ($ch,CURLOPT_REFERER,$refer);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    $output = curl_exec($ch);
    curl_close($ch);
    return $output;
}

function meipai_author($author_url) {
    echo $author_url."\n";
    preg_match('|/user/([0-9]+)|si',$author_url, $tmp);

    $uid = $tmp[1];
    $i = 0;
    while(1>0)
    {
        $i++;
        $newurl = 'http://www.meipai.com/users/user_timeline?page='.$i.'&count=30&uid='.$uid;
        $c = meipai_get($newurl);
        $arr = json_decode($c, true);
        if(empty($arr['medias'])) continue;
        foreach($arr['medias'] as $k =>$v) {
            $fdata = array();
            //$fdata['author'] = $data['author'];
            $fdata['source_url'] = $v['url'];

            $fdata['description'] = $v['caption_origin'];
            $fdata['video_url'] = $v['video'];

            $xdd = trim($v['created_at']);
            if(stristr($xdd,'今天'))
            {
                $xdd = date('Y-m-d').' '.$xdd;
                $fdata['publish_time'] = date('Y-m-d',strtotime($xdd));
            } else {
                if(substr_count($xdd,'-') == 1) {
                    $xdd = date('Y').'-'.$xdd;
                }
                $fdata['publish_time'] = trim($xdd);
            }
            $fdata['fetch_time'] = date('Y-m-d H:i:s');
            $fdata['author'] = $v['user']['screen_name_origin'];
            $c = mobile_get($v['url']);
            preg_match('|<i class="mp-iconfont">&#xe61c;</i>([^<]+)播放|si',$c,$tmp);
            $views = isset($tmp[1]) ? trim($tmp[1]) : 0;
            $views = str_replace(",","",$views);
            if(stristr($views,'万')){
                $views = str_replace("万","",$views);
                $views = $views*10000;
            }  
            $fdata['views'] = $views;

            print_r($fdata);
        }   
    }     
}

$author_url = 'http://www.meipai.com/user/17117548';
meipai_author($author_url);
/*Array
(
    [source_url] => http://www.meipai.com/media/531950208
    [description] => 服装拍摄花絮～
    [video_url] => http://mvvideo1.meitudata.com/575bb0a453c616545.mp4
    [publish_time] => 2016-06-10 15:11
    [fetch_time] => 2016-08-05 00:49:46
    [author] => 大素儿baby
    [views] => 21315
    [filename] => 575bb0a453c616545.mp4
)
Array
(
    [source_url] => http://www.meipai.com/media/526649358
    [description] => 刚刚剪了个刘海～ 😭😭好像又失败了
    [video_url] => http://mvvideo2.meitudata.com/574fe2873e5fa3481.mp4
    [publish_time] => 2016-05-31 15:23
    [fetch_time] => 2016-08-05 00:49:47
    [author] => 大素儿baby
    [views] => 28215
    [filename] => 574fe2873e5fa3481.mp4
)*/

