<?php
/**
 *  @doc : http://documentup.com/Respect/Validation
 */
use Sow\Bug as Y;
use Respect\Validation\Validator as v;
class rule extends \Sow\Sys\Rule {


  public function page() {
    $validator = v::int()->min( 15 );
    return  $validator;
  }

}
