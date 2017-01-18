<?php
/**
 * 定义函数
 * Created by PhpStorm.
 * User: jasper
 * Date: 17-1-18
 * Time: 下午10:37
 */

/**
 * 跟server酱说说话
 * @param string $url
 * @param string $title
 * @param string $content
 * @return string
 */
function talkToServerChan($url = '', $title = '', $content = '')
{
    $opts = array('http' =>
        array(
            'method'  => 'POST',
            'header'  => 'Content-type: application/x-www-form-urlencoded',
            'content' => http_build_query(
                array(
                    'text'=>$title,
                    'desp'=>$content
                )
            )
        )
    );

    $context  = stream_context_create($opts);
    return $result = file_get_contents($url, false, $context);
}
