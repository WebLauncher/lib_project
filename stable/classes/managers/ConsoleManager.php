<?php
/**
 * Console Manager Class
 */
/**
 * Console Manager Class 
 *
 * @package WebLauncher\Managers
 */
class ConsoleManager {
	/**
	 * @var /System $system System object
	 */
	public static $system = null;
	/**
	 * @var string $date Current date
	 */
	private static $date = '';
	/**
	 * @var bool $is_cronjob Flag if cronjob
	 */
	public static $is_cronjob = false;
	/** 
	 * @var string $root_dir The root directory
	 */
	private static $root_dir = '';

	/**
	 * Init function
	 */
	static function init() {
		if (!isset($_SERVER['argv'][1]))
			die('Please provide other parameters to execute your command!');
		else {
			switch($_SERVER['argv'][1]) {
				case '--cron' :					
				break;
				case '--email' :					
				break;
				default :
					self::$system -> query = $_SERVER['argv'][1];
			}
		}
	}

	/**
	 * Execute console call
	 */
	static function execute() {
		switch($_SERVER['argv'][1]) {
			case '--cron' :
				$included = get_included_files();
				self::$root_dir = dirname($included[0]) . DS;
				self::line("Executing cronjobs");
				if (isset(self::$system -> cronjobs))
					foreach (self::$system->cronjobs as $cron)
						self::cronjob($cron);
				if(self::$system->console_cronjobs_db_enabled)
				{
					$model=new Base();
					$model->table=self::$system->console_cronjobs_db_table['name'];
					$jobs=$model->get_all('','','order','','is_active = 1');
					foreach($jobs as $k=>$j)
					{
						if(self::check_cronjob(array('time'=>implode(' ',array($j['min'],$j['hour'],$j['day_m'],$j['month'],$j['day_w'])))))
						{
							$model->update_field($j['id'],"last_execution_start",nowfull());
							$cron=array(
								'path'=>$j['program'],
								'external'=>isset($j['external'])?$j['external']:true,
            					'time'=>implode(' ',array($j['min'],$j['hour'],$j['day_m'],$j['month'],$j['day_w'])),
            					'name'=>isset($j['description'])?$j['description']:'',
            					'root'=>self::$root_dir
							);
							self::cronjob($cron);
							$model->update_field($j['id'],"last_execution_end",nowfull());
						}
					}
				}
				die ;
			break;
			case '--email' :
				self::line("Executing e-mail queue");
				die ;
			break;			
		}
	}
	
	/**
	 * Execute a cronjob
	 * @param array $cron
	 */
	private static function cronjob($cron) {
		if (self::check_cronjob($cron)) {
			self::line("Starting cronjob: " . isset_or($cron['description']));
			self::line("Executing: " . $cron['path']);
			if (isset_or($cron['external'])) {
				self::$system -> load_file((isset($cron['root'])?$cron['root']:'').$cron['path']);
			} else {
				self::line('Processing: ' . 'php ' . self::$root_dir . 'index.php ' . $cron['path']);
				self::line(exec('php ' . self::$root_dir . 'index.php ' . $cron['path']));
			}
		}
	}
	
	/**
	 * Display a message line
	 * @param string $message
	 */
	private static function line($message) {
		echo $message . "\n";
	}

	/**
	 * Check if cronjob is defined well
	 * @param array $cron
	 */
	private static function check_cronjob($cron) {
		$ok = true;
		$time = isset_or($cron['time']);
		if (!$time)
			$time = '* * * * *';
		$params = explode(' ', $time);
		if (count($params) != 5)
			trigger_error('Cronjob ' . print_r($cron, true) . ' time not well defined!');
		if (!self::$date)
			self::$date = getdate();

		//check minutes
		if ($params[0] != "*") {
			$mins = explode(",", $params[0]);
			$ok = false;
			foreach ($mins as $v) {
				if (trim($v) == self::$date['minutes'])
					$ok = true;
			}
		}

		// check hours
		if ($params[1] != "*" && $ok) {
			$hrs = explode(",", $params[1]);
			$ok = false;
			foreach ($hrs as $v) {
				if (trim($v) == self::$date['hours'])
					$ok = true;
			}
		}

		// check month day
		if ($params[2] != "*" && $ok) {
			$ds = explode(",", $params[2]);
			$ok = false;
			foreach ($ds as $v) {
				if (trim($v) == self::$date['mday'])
					$ok = true;
			}
		}

		// check month
		if ($params[3] != "*" && $ok) {
			$mn = explode(",", $params[3]);
			$ok = false;
			foreach ($mn as $v) {
				if (trim($v) == self::$date['mon'])
					$ok = true;
			}
		}

		// check week day
		if ($params[4] != "*" && $ok) {
			$ds = explode(",", $params[4]);
			$ok = false;
			foreach ($ds as $v) {
				if (trim($v) == self::$date['wday'] - 1)
					$ok = true;
			}
		}
		return $ok;
	}
}
?>
