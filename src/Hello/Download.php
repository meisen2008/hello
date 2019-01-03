<?php
namespace Hello;

class Download
{
    public static function downLoadFilepath($file_name)
    {
        //$file_name = "test.csv";
        $contents = file_get_contents($file_name);
        $file_size = filesize($file_name);
        header("Content-type: application/octet-stream;charset=utf-8");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=" . $file_name);
        exit($contents);
    }

    public static function downLoadFileurl($url, $file_name)
    {
        $contents = file_get_contents($url);
        $file_size = strlen($contents);
        header("Content-type: application/octet-stream;charset=utf-8");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=" . $file_name);
        exit($contents);
    }

    public static function downLoadFilelink($url)
    {
        $re = new Httpcurl();
        //$url = 'http://test.thinkphpdemo.com/phptest/download.php';
        $contents = $re->http($url);
        $file_name = 'hehe.csv';
        $file_size = strlen($contents);
        header("Content-type: application/octet-stream;charset=utf-8");
        header("Accept-Ranges: bytes");
        header("Accept-Length: $file_size");
        header("Content-Disposition: attachment; filename=" . $file_name);
        exit($contents);
    }
}
