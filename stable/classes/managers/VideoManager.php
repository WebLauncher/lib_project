<?php
/**
 * Video Processing
 */
/**
 * Video Processing Class
 * @package WebLauncher\Managers
 */
class VideoManager
{
	/**
	 * @var string $ffmpeg_path_win FFMPEG path on windows
	 */
	public $ffmpeg_path_win='e:/xampp/ffmpeg/ffmpeg.exe';
	/**
	 * @var string $mp4box_path_win MP4 BOX path on windows
	 */
	public $mp4box_path_win='e:/xampp/ffmpeg/MP4box.exe';
	/**
	 * @var string $ffmpeg_path_linux FFMPEG path on linux
	 */
	public $ffmpeg_path_linux='ffmpeg'; 
	/**
	 * @var string $mp4box_path_linux MP4 Box path on linux
	 */
	public $mp4box_path_linux='mp4box';
	
	/**
	 * Process video conversion
	 * @param string $file_path
	 */	
	function process_video($file_path)
	{
		set_time_limit(0);
	
		$source_file=$file_path;
		$srcFile = escapeshellarg($source_file);
		$destFile = escapeshellarg($source_file.'.mp4');	
		$log_file = escapeshellarg($source_file.'.log.txt');	
		$log_file_mux = escapeshellarg($source_file.'.log_mux.txt');
		
		// unlink log files
		if(is_file($source_file.'.log.txt'))unlink($source_file.'.log.txt');
		if(is_file($source_file.'.log_mux.txt'))unlink($source_file.'.log_mux.txt');
			
		$query_mux=$this->mp4box_path_linux.' -hint '.$destFile.' 1> '.$log_file_mux.' 2>&1';
		$query=$this->ffmpeg_path_linux.' -i '.$srcFile.' -vcodec libx264 -sameq -y '.$destFile.' 1> '.$log_file.' 2>&1 ; '.$query_mux;
		if(strtolower(php_uname('s'))=="windows nt")
		{
			$command=dirname(__FILE__).'\run_video_processes.bat';			
			// converting video to mp4		
			$query='start /b '.$command.' ';
			// ffmpeg
			$query.=escapeshellarg($this->ffmpeg_path_win);
			$query.=' '.$srcFile.' '.$destFile.' '.$log_file.' ';
			// muxing
			$query.=escapeshellarg($this->mp4box_path_win).' '.$log_file_mux;
		}
		pclose(popen($query, "r"));
	}
	
	/**
	 * Get percent completed
	 * @param string $file_path
	 * @param int $duration
	 */
	function get_percent_completed($file_path,$duration=0)
	{
		set_time_limit(0);	
		$source_file=$file_path;
		if(!$duration)			
			$duration=	$this->get_duration($file_path);
		
		$log_file = $source_file.'.log.txt';
		$log_file_mux = $source_file.'.log_mux.txt';
		$percent_ffmpeg=0;
		$percent_mux=0;
		if(is_file($log_file))
		{
			$content=file_get_contents($log_file);
			$last_time_pos=strrpos($content,'time=');
			$content=substr($content,$last_time_pos+5,strlen($content)-$last_time_pos-4);
			$current=substr($content,0,strpos($content, ' '));
			$percent_ffmpeg=round($current*100/$duration);
		}
		if(is_file($log_file_mux))
		{
			$content=file_get_contents($log_file_mux);
			$last_hint=strrpos($content, 'ISO File Writing: |');		
			$content=substr($content,$last_hint+42,strlen($content)-$last_hint-41);
			$current=substr($content,0,strpos($content, '/'));
			$percent_mux=$current;
		}
		return round(($percent_ffmpeg*95+$percent_mux*5)/100);
	}
	
	/**
	 * Get duration of video
	 * @param string $file_path
	 */
	function get_duration($file_path)
	{
		global $page;
		$page->import('library','video');		
		//$movie = new ffmpeg_movie($file_path);
		$toolkit = new PHPVideoToolkit($page->paths['root_dir'].'tmp_files/');
		$toolkit->on_error_die = FALSE;
		$toolkit->setInputFile($file_path);
		$data = $toolkit->getFileInfo();
		return	$data['duration']['seconds'];		
	}
}

?>