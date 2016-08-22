<?php
	/**
	 * Trace Manager Class
	 */
	/**
	 * System Trace Manager
	 * @package WebLauncher\Managers
	 */
	class TraceManager
	{
		/**
		 * @var string $trace Generated Trace
		 */
		public static $trace='';
		
		/**
		 * Generate trace
		 */
		public static function generate()
		{
			global $page;
			$file_name=sha1($page->query).'_'.sha1(echopre_r($_REQUEST)).'_'.sha1(microtime()).'.html';
			$page->session['__current_trace']=$file_name;
			$page -> import('library', 'debug');
			ob_start();
			new dBug($page -> get_page());
			$db = ob_get_contents();
			ob_end_clean();

			ob_start();
			new dBug($page -> session);
			$session = ob_get_contents();
			ob_end_clean();

			ob_start();
			new dBug($page -> paths);
			$paths = ob_get_contents();
			ob_end_clean();

			ob_start();
			new dBug($page -> user);
			$user = ob_get_contents();
			ob_end_clean();

			$random = base64_encode(microtime());
			$times = $page -> time -> get_list();
			$memory = $page -> memory -> get_list();
			$db_conn = array();
			if(is_a($page -> db_conn -> tables, 'TablesManager'))
			{
				$db_conn['dns'] = $page -> db_conn -> get_dns();
				$db_conn['tables'] = $page -> db_conn -> tables;
				$db_conn['db_no_valid_queries'] = $page -> db_conn -> num_valid_queries;
				$db_conn['db_no_invalid_queries'] = $page -> db_conn -> num_invalid_queries;
				$db_conn['db_queries'] = $page -> db_conn -> queries;
				$db_conn['db_slowest_query']=$page -> db_conn ->get_slowest_query();
			}
			ob_start();
			new dBug($db_conn);
			$db_conn = ob_get_contents();
			ob_end_clean();
			$btn_style="border:1px solid #ccc; color:#000; background:#efefef;margin-right:4px; border-top:0;height:auto;padding:auto;margin:auto; clear:none; float:left; width:auto;";
			$page -> trace_page = '<div style="clear:both; position:fixed;bottom:0px; z-index:20000000000;"><button id="btn_page_trace_' . $random . '" onclick="window.open(\''.$page->paths['root'].'?a=__sys_trace&page='.$page->session['__current_trace'].'\');" style="'.$btn_style.'">&raquo;</button>';
			if($page -> debug)
				$page -> trace_page .= '';
			if($page -> logger -> active && $page -> logger -> no)
				$page -> trace_page .= '<button onclick="jQuery(\'#page_log_' . $random . '\').toggle();" style="'.$btn_style.'">log (' . $page -> logger -> no . ')</button>';
			$page -> trace_page .= '</div>';
			$page -> trace_page .= '<div id="page_trace_' . $random . '" style="background:#fff;display:none;clear:both; border:1px solid #000; height:400px; overflow:scroll;"><br/> ';

			self::$trace .= '</div><table width="100%" cellspacing="0" cellpadding="0" id="trace_table">
				<tr><td style="width:10%; border-right:1px solid #ccc;">
					<div class="wrapper">
					<ul id="nav_menu">
					<li><a href="#" onclick="show_panel(\'#div_statistics\');">Statistics</a></li>
					<li><a href="#" onclick="show_panel(\'#div_page\');">Page</a></li>
					<li><a href="#" onclick="show_panel(\'#div_session\');">Session</a></li>
					<li><a href="#" onclick="show_panel(\'#div_paths\');">Paths</a></li>
					<li><a href="#" onclick="show_panel(\'#div_db\');">Database</a></li>
					<li><a href="#" onclick="show_panel(\'#div_user\');">Logged User</a></li>
					</ul>
					</td>
					</div>
					<td style="max-width:90%;width:90%;">
						<div class="container">
							<div class="wrapper">
						<div id="div_statistics" class="panel"><h2>Statistics</h2>';
			self::$trace.= '<div style="border-top:1px dotted #000;"><strong>Time</strong><br/>Total execution: <strong>' . $times['system'] . ' s</strong>&nbsp;&nbsp;';
			self::$trace .= 'Scripts execution: <strong>' . $times['render_scripts'] . ' s</strong>&nbsp;&nbsp;';
			self::$trace .= 'Templates render: <strong>' . $times['render_templates'] . ' s</strong>&nbsp;&nbsp;';
			self::$trace .= 'DB queries: <strong>' . $page -> db_conn -> total_execution_time() . ' s</strong>&nbsp;&nbsp;</div>';
			self::$trace .= '<div style="border-top:1px dotted #000;">';

			self::$trace .= '<strong>Memory</strong><br/> Maximum allowed: <strong>' . $memory['max'] . ' </strong>&nbsp;&nbsp;';
			self::$trace .= 'Before init: <strong>' . $memory['system_before_init'] . ' </strong>&nbsp;&nbsp;';
			self::$trace .= 'After init: <strong>' . $memory['system_after_init'] . ' </strong>&nbsp;&nbsp;';
			self::$trace .= 'Before render: <strong>' . $memory['system_before_render'] . ' </strong>&nbsp;&nbsp;';
			self::$trace .= 'After render: <strong>' . $memory['system_after_render'] . ' </strong>&nbsp;&nbsp;';
			self::$trace.='</div></div>						
						<div id="div_page" class="panel" style="display:none;"><h2>Page</h2>' . $db . '</div>
						<div id="div_session" class="panel" style="display:none;"><h2>Session</h2>' . $session . '</div>
						<div id="div_paths" class="panel" style="display:none;"><h2>Paths</h2>' . $paths . '</div>
						<div id="div_db" class="panel" style="display:none;"><h2>Database</h2>' . $db_conn . '</div>
						<div id="div_user" class="panel" style="display:none;"><h2>Logged User</h2>' . $user . '</div>
						</div>
						</div>
					</tr>
					</table>
					';
			$page->trace_page.='</div>';
			if($page -> debug)
				$page -> trace_page .= '<div id="page_template_0101" style="background:#fff;display:none;clear:both; border:1px solid #000; height:400px;"><br/><iframe id="page_template_0101_frame" frameborder="0"  vspace="0"  hspace="0"  marginwidth="0"  marginheight="0" width="100%" height="100%"></iframe></div>';

			if($page -> logger -> active && $page -> logger -> no)
				$page -> trace_page .= '<div id="page_log_' . $random . '" style="background:#fff;display:none;clear:both; border:1px solid #000; height:400px; overflow:scroll;">
						' . $page -> logger -> get() . '</div>';
			self::save();
		}
		
		/**
		 * Save trace
		 */
		public static function save(){
			global $page;
			$html='
				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
			<html  xmlns="http://www.w3.org/1999/xhtml">
				<head><title>Page Trace</title>
				<style>
					body {margin:0;padding:0;}
					* {font-size:12px;}
					td, th {vertical-align:top;}
					.container {overflow:scroll;}
					.wrapper {padding:10px;}
					h2 {font-size:14px; color:#fff; background:blue; padding:5px;border-bottom:2px solid #333;margin:0;margin-bottom:10px;}
					#nav_menu {list-style:none;margin:0;padding:0;}
					#nav_menu a {font-size:13px; font-weight:bold;display:block;border:1px solid #ccc; padding:4px; color:#333; text-decoration:none;}
					#nav_menu a:hover {background:#ccc;}
					#nav_menu a.current {background:#333; color:#fff;}
				</style>
				<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
				<script>
				function show_panel(panel_id){
					$(".container .panel").hide();
					$(panel_id).show();
				}
				$(function(){
					$("#trace_table").height($(window).height());
					$("#trace_table .container").height($(window).height()).width($("#trace_table .container").parent().width());
				});
				</script>
			</head>
			<body>';
			$html.=self::$trace;
			$html.='</body></html>';
			$html=preg_replace('!\s+!', ' ', $html);
			if($trace_dir=self::check_dir())
				file_put_contents($trace_dir.$page->session['__current_trace'], $html);
			self::clean_dir();
		} 
		
		/**
		 * Check directory
		 */
		public static function check_dir(){
			global $page;
			$trace_dir=sys_get_temp_dir().'/wbl_sys_trace/';
			if(!is_dir($trace_dir))
			{
				if(!mkdir($trace_dir, 0777, true))
				{
					trigger_error('Cache_Write_Error', 'Can not create dir "' . $trace_dir . '" to cache folder!');
					return false;
				}
			}
			return $trace_dir;
		}
		
		/**
		 * Clean directory
		 */
		public static function clean_dir(){
			global $page;
			$trace_dir=sys_get_temp_dir().'/wbl_sys_trace/';
			if ($handle = opendir($trace_dir)) {
			
			    /* This is the correct way to loop over the directory. */
			    while (false !== ($file = readdir($handle)) && $file!='.' && $file!='..') {
			        if ( filemtime($trace_dir.$file) <= time()-60*15 ) {
			           unlink($trace_dir.$file);
			        }
			    }
			
			    closedir($handle);
			}
		}
		
		/**
		 * Init trace
		 */
		public static function init(){
			global $page;
			if(isset_or($page->actions[0])=='__sys_trace' && $trace_dir=self::check_dir()){
				echo file_get_contents($trace_dir.$_REQUEST['page']);
				die;
			}
		}
	}
?>