<?php
require_once( 'functions.php' );
require_once( 'forge_fdf.php' );

$fdf_data_strings= array();
$fdf_data_names= array();
$vals = array();
for($i=1;$i<=16;$i++)
{
	$vals = process_questions($_POST);
}


$fields_hidden= array();
$fields_readonly= array();
$fdf= forge_fdf( '',
		 $fdf_data_strings,
		 $fdf_data_names,
		 $fields_hidden,
		 $fields_readonly );

$fdf_fn= tempnam( '.', 'fdf' );
$fp= fopen( $fdf_fn, 'w' );
if( $fp ) {
  fwrite( $fp, $fdf );
  fclose( $fp );

  header( 'Content-type: application/pdf' );
  header( 'Content-Type: application/octet-stream');
  header( 'Content-disposition: attachment; filename=smart_trust_report.pdf' ); // prompt to save to disk

passthru('pdftk smart_trust.pdf fill_form '. $fdf_fn. ' output - flatten');

  
  unlink( $fdf_fn ); // delete temp file
}
else { // error
  echo 'Error: unable to open temp file for writing fdf data: '. $fdf_fn;
}

?>