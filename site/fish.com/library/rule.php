<?php
/**
 *  @doc : http://documentup.com/Respect/Validation
 */
use Sow\bug as Y;
use Respect\Validation\Validator as v;
class rule extends \Sow\sys\Rule {


  public function page() {
    $validator = v::int()->min( 15 );
    return  $validator;
  }

}
