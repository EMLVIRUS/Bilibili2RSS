<?php
date_default_timezone_set("Asia/Shanghai");
require './class/function.php';
UA_JSON::Create();
UA_JSON::Del();
$htmlRead = "";
if (!UA_JSON::Add()) {
  require './HyperDown/Parser.php';
  $parser = new HyperDown\Parser;
  $mdRead   = file_get_contents("./README.md");
  $htmlRead = $parser->makeHtml($mdRead);
  $htmlRead = "<h2><b>---尽量使用固定IP并邀请足够多的人使用本服务即可移除下述内容---</b></h2><p>群号：189574683</p><p>点击加入：<a target=\"_blank\" href=\"//shang.qq.com/wpa/qunwpa?idkey=f2701214fb5c70ce08107e7206a282927e13ab91ec0780af640c2ad6bd9895c8\"><img border=\"0\" src=\"//pub.idqqimg.com/wpa/images/group.png\" alt=\"576k5ZCN5LuA5LmI6ay8\" title=\"576k5ZCN5LuA5LmI6ay8\"></a></p>$htmlRead";
  
}
$seasonid  = GetVars("anime", "GET") != null ? GetVars("anime", "GET") : 5800;
$http_post = Network::Create();
$http_post->open('GET', "http://bangumi.bilibili.com/jsonp/seasoninfo/$seasonid.ver");
$http_post->send();
$responseText = str_replace("seasonListCallback(", "", $http_post->responseText);
$responseText = str_replace("});", "}", $responseText);
$obj_json     = json_decode($responseText);
if (!isset($obj_json->message) || $obj_json->message !== "success") {
  echo $responseText;
  die();
}
$rss2 = new Rss2($obj_json->result->bangumi_title . "_番剧_bilibili_哔哩哔哩弹幕视频网", "http://bangumi.bilibili.com/anime/" . $seasonid, $obj_json->result->brief);
foreach ($obj_json->result->episodes as $item) {
  $created = strtotime($item->update_time);
  $title   = "第" . $item->index . "话 - " . $item->index_title;
  $url     = $item->webplay_url;
  $img     = str_replace("http://","//",$item->cover);
  $body    = "<p><img src=\"" . $img . "\" alt=\"$title\" /></p><p>$title</p>" . $htmlRead;
  $rss2->addItem($title, $url, $body, $created);
}
header("Content-type:text/xml; Charset=utf-8");
echo $rss2->saveXML();
?>