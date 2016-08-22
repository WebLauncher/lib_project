<?php
	function geo_cmpd_distances($a, $b)
    {
		if($a['distance'] > $b['distance']){
		    return 1;
		}else{return 0;}
    }

	class CheckDb{
		public $db_server='';
		public $db_name='';
		public $db_user='';
		public $db_password='';
		public $db_table='x_conf_zips2';
		public $db_type='mysql';

		protected $connection;

		public function __construct()
		{

		}

		public function connect()
		{
			$dsn = $this->db_type.':dbname='.$this->db_name.';host='.$this->db_server;
			try {
			    $this->connection = new PDO($dsn, $this->db_user, $this->db_password);
			} catch (PDOException $e) {
			    echo 'Connection failed: ' . $e->getMessage();
			}
		}

		public function getZip($latitude, $longitude)
		{
			$zip=$this->radiusSearch($latitude.','.$longitude,10);
			return $zip['zip_code'];
		}

		private function radiusSearch($zip_or_coordonates, $radius=1)
		{
				$latlon = @explode(',', $zip_or_coordonates);

				$glat=1;
				$glon=1;
				if(count($latlon) == 2)
				{
					$glat = trim($latlon[0]);
				    $glon = trim($latlon[1]);
				}
				else
				{
				    $zip_info = $this->connection->query("SELECT * FROM ".$this->db_table." where zip_code = '".$zip_or_coordonates."' LIMIT 1")->fetch(PDO::FETCH_ASSOC);
				    return $zip_info;
				}
				if(!$glat && !$glon)
				{
					return array('zip_code'=>'');
				}

				$pi = 3.14159265358979323846;
				$latrange = $radius / ((6076 / 5280) * 60);
			    $longrange = $radius / (((cos(floatval($glat * $pi / 180)) * 6076) / 5280) * 60);

				$lat1 = $glat - $latrange;
				$lat2 = $glat + $latrange;
				$long1 = $glon - $longrange;
				$long2 = $glon + $longrange;

				$locations_new = array();
				$locations = $this->connection->query("SELECT * FROM ".$this->db_table." WHERE	(POW( ( 69.1 * ( zip_lon - ".$glon." ) * cos( ".$glat." / 57.3 ) ) , 2 ) + POW( ( 69.1 * ( zip_lat - ".$glat." ) ) , 2 )) < ( POW(".$radius.",2) )")->fetchAll(PDO::FETCH_ASSOC);

				if(count($locations)>0)
				{
				    $i = 0;
				    foreach ($locations as $id => $loc)
				    {
				    	if($glat != '' && $glon != '' && $loc['zip_lat'] != '' && $loc['zip_lon']!= '')
				    	{
				  	        $distance = $this->getDistanceBetweenPointsNew($glat, $glon, $loc['zip_lat'], $loc['zip_lon'], $unit = 'Mi') ;
					        $distance = ($distance == '')? 0:$distance;

					        if($distance <= $radius)
					        {
							    $locations_new[$i] = $loc;
						        $locations_new[$i]['distance'] = $distance;
	                            if($locations_new[$i]['distance'] == '') {$locations[$id]['distance'] = '0';}
					            $i++;
							}
				    	}
				    }
				}

				@usort($locations_new, "geo_cmpd_distances");


				return $locations_new[0];

		}

		private function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2, $unit = 'Mi')
		{
	            $theta = $longitude1 - $longitude2;
	            $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
	            $distance = acos($distance);
	            $distance = rad2deg($distance);
	            $distance = $distance * 60 * 1.1515;
	            switch($unit) {
	                case 'Mi': break;
	                case 'Km' : $distance = $distance * 1.609344;
	            }
				return (round($distance,2));
	     }
	}
?>