<?php
use Sow\bug as Y;
class Index_Controller extends \Yaf\Controller_Abstract
{
    public function indexAction()
    {
       var_dump(Y::request());

    }
   
}
