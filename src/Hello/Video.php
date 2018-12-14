<?php
namespace Hello;
class Video{
	private $params = [];
	public function __get($property_name)
	{
		return $this->params[$property_name] ?? "no_value";
	}

	public function __set($property_name,$property_value)
	{

		$this->params[$property_name] = $property_value;

	}

	public static function __callStatic($name,$arguments)
	{
		$video = new static();
		call_user_func_array([$video,$name], $arguments);

	}

	public static function GetMp4File($file) { 
	    $size = filesize($file); 
	    header("Content-type: video/mp4"); 
	    header("Accept-Ranges: bytes"); 
	    if(isset($_SERVER['HTTP_RANGE'])){ 
	        header("HTTP/1.1 206 Partial Content"); 
	        list($name, $range) = explode("=", $_SERVER['HTTP_RANGE']); 
	        list($begin, $end) =explode("-", $range); 
	        if($end == 0){ 
	            $end = $size - 1; 
	        } 
	    }else { 
	        $begin = 0; $end = $size - 1; 
	    } 
	    header("Content-Length: " . ($end - $begin + 1)); 
	    header("Content-Disposition: filename=".basename($file)); 
	    header("Content-Range: bytes ".$begin."-".$end."/".$size); 
	    $fp = fopen($file, 'rb'); 
	    fseek($fp, $begin); 
	    while(!feof($fp)) { 
	        $p = min(1024, $end - $begin + 1); 
	        $begin += $p; 
	        echo fread($fp, $p); 
	    } 
	    fclose($fp);
	} 

}

