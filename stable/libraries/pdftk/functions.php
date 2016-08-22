<?php
function get_bar_length($score,$count)
{
	$bar_size = '';
	for($i=$score;$i>0;$i--)
	{
		if($i%$count==0)
		{
			$bar_size.=' ';		
		}
	}
	return $bar_size;

}

function process_questions($my_array)
{
	$assignments = array(
	'Q1' => array(1,4,11,13),
	'Q2' => array(3,6,10,15),
	'Q3' => array(2,5,9,12),
	'Q4' => array(7,8,14,16));
	
	for($i=1;$i<=16;$i++)
	{
		foreach ($assignments as $q => $num)
		{
			if(in_array($i,$num))
			{
				$temp_array[$q] += get_score($my_array['Q'.$i]); 
				$temp_array['totals'] += get_score($my_array['Q'.$i]); 
			}
		}
	}
	return $temp_array;
}

function get_score($text)
{
	if ($text == 'SD')
	{
		return 0;
	}
	if ($text == 'D')
	{
		return 20;
	}
	if ($text == 'SLD')
	{
		return 40;
	}
	if ($text == 'SLA')
	{
		return 60;
	}
	if ($text == 'A')
	{
		return 80;
	}
	if ($text == 'SA')
	{
		return 100;
	}
	
	return 0;
}

?>