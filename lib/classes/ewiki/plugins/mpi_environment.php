<?php
 # Hans B Pufal (http://www.aconit.org/hbp/CCC/Ewiki/index.php)

function ewiki_mpi_environment ($action, $args=array())
{
   if ($action == "desc")
      return "Shows eWiki, PHP and server environment";

   if ($action == "doc")
      return "
      <p>The <b>environment</b> plugin provides an interface to the PHPinfo function.
      By default the PHPinfo function is called with parameter 45, this may be specified as
      an integer by setting parameter 'info'.
      ";

   if ($action != 'html')
      return ' [ <b>environment</b> cannot do "' . $action . '". ] ';

   !isset ($args['info']) && $args['info'] = 45;

   // start the output buffer, this means nothing will be displayed until the buffer is closed
   ob_start();

   // call the phpinfo function to display the php info
   phpinfo($args['info']);

   // get contents of output buffer, which is everything that would have been printed from phpinfo();
   $val_phpinfo .= ob_get_contents();

   // flush the output buffer and delete the contents
   ob_end_clean();

   // get a substring of the php info to get rid of the html, head, title, etc.
   $val_phpinfo = substr( $val_phpinfo, 554, -19 );

   // change the width of the table to 450
// $val_phpinfo = str_replace( 'width="600"', 'width="450"', $val_phpinfo );

   $val_phpinfo = str_replace( 'align="center"', '', $val_phpinfo );
   return $val_phpinfo;
}

?>