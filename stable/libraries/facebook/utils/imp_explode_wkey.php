<?php



   //to implode associative arrays, such as the cookie
 	function implode_with_key($assoc, $inglue = '=>', $outglue = ', ')
    {
        $return = '';
        foreach ($assoc as $tk => $tv)
        {
            $return .= $outglue . $tk . $inglue . $tv;
        }
        return substr($return,strlen($outglue));
    }

   //just implde the values
 	function implode_values($assoc, $outglue = ', ')
    {
        $return = '';
        foreach ($assoc as $tk => $tv)
        {
            $return .= $outglue . $tv;
        }
        return substr($return,strlen($outglue));
    }
	
   //just implde the keys
 	function implode_keys($assoc, $outglue = ', ')
    {
        $return = '';
        foreach ($assoc as $tk => $tv)
        {
            $return .= $outglue . $tk;
        }
        return substr($return,strlen($outglue));
    }
	
	function explode_with_key($str, $inglue = ">", $outglue = ',')
    {
        $hash = array();
        foreach (explode($outglue, $str) as $pair)
        {            
            $k2v = explode($inglue, $pair);            
            $hash[$k2v[0]] = $k2v[1];            
        }
        return $hash;
    }
	
?>
