<?php
$str = "@abcd@asddfasf@asfdasfas@bitch@dfasdas.字母com";
$p = "/@[\x{4e00}-\x{9fa5}A-Za-z0-9_.]{2,16}[^@.]/u";
preg_match_all($p,$str,$m);
print_r($m);
var_dump(htmlentities($str, ENT_COMPAT));
