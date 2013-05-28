<?php
/* this class is designed to build up an object that matches a template - or the default and then output itself */

class mpPage
{
    // data to be held by page.
    
    
    
   function __construct() {
       print "In constructor\n";
       $this->name = "MyDestructableClass";
   }

   function __destruct() {
       print "Destroying " . $this->name . "\n";
   }





}


// END OF CLASS mpPage




?>
