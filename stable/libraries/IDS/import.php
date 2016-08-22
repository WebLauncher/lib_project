<?php
	global $page;
	$this->import('file',dirname(__FILE__).'/Init.php');
	try {

	/*
				 * It's pretty easy to get the PHPIDS running
				 * 1. Define what to scan
				 *
				 * Please keep in mind what array_merge does and how this might interfer
				 * with your variables_order settings
				 */
				$request = array(
			        'GET' => $_GET,
			        'POST' => $_POST
				);

				$init = IDS_Init::init(dirname(__FILE__).'/Config/Config.ini');

				/**
				 * You can also reset the whole configuration
				 * array or merge in own data
				 *
				 * This usage doesn't overwrite already existing values
				 * $config->setConfig(array('General' => array('filter_type' => 'xml')));
				 *
				 * This does (see 2nd parameter)
				 * $config->setConfig(array('General' => array('filter_type' => 'xml')), true);
				 *
				 * or you can access the config directly like here:
				 */
				// database configuration
				$init->config['Logging']['wrapper']='mysql:host='.$page->db_connections[0]['host'].';port=3306;dbname='.$page->db_connections[0]['dbname'];
				$init->config['Logging']['user']=$page->db_connections[0]['user'];
				$init->config['Logging']['password']=$page->db_connections[0]['password'];
				$init->config['Logging']['table']='intrusions';
				$init->config['General']['filter_path']=dirname(__FILE__).'/IDS/default_filter.xml';
				$init->config['General']['tmp_path']=$page->paths['root_cache'];
				$init->config['Caching']['path']=$page->paths['root_cache'].'default_filter.cache';

				// 2. Initiate the PHPIDS and fetch the results
				$ids = new IDS_Monitor($request, $init);
				$result = $ids->run();

				/*
				 * That's it - now you can analyze the results:
				 *
				 * In the result object you will find any suspicious
				 * fields of the passed array enriched with additional info
				 *
				 * Note: it is moreover possible to dump this information by
				 * simply echoing the result object, since IDS_Report implemented
				 * a __toString method.
				 */
				if (!$result->isEmpty()) {

					$impact=$result->getImpact();
					if($impact>10)
					{
						$page->import('file',dirname(__FILE__).'/IDS/Log/Composite.php');
						//require_once dirname(__FILE__).'/IDS/Log/Email.php';
						$page->import('file',dirname(__FILE__).'/IDS/Log/Database.php');
						$compositeLog = new IDS_Log_Composite();
						$compositeLog->addLogger(
						//IDS_Log_Email::getInstance($init),
						IDS_Log_Database::getInstance($init)
						);

						$compositeLog->execute($result);
					}
					if($impact>27 && $impact<81)
					{
						$page->restricted('Restricted access for security reasons.');
					}
					elseif($impact>81)
					{
						$page->restricted('Blocked access for security reasons.');
					}

				}
			} catch (Exception $e) {
				/*
				 * sth went terribly wrong - maybe the
				 * filter rules weren't found?
				 */
				printf(
			        'An error occured: %s',
				$e->getMessage()
				);
			}
?>