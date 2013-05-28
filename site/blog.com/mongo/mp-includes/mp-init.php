<?php

/* Fire up the mongopress objects */

$mp = mongopress_load_mp();
$mongo = mongopress_load_perma();
$perma = $mongo->current();
$options = $mp->options();
