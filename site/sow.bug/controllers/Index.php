<?php
use Sow\bug as Y;
class IndexController extends \Yaf\Controller_Abstract
{
    public function indexAction()
    {
       var_dump(Y::request());

    }
   
}
