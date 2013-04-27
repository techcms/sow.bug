<?php
use Sow\Bug as Y;
class IndexController extends \Yaf\Controller_Abstract
{
    public function indexAction()
    {
       var_dump(Y::request());

    }
   
}
