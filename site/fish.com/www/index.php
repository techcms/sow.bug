<?php
use Sow\bug as Y;

date_default_timezone_set('Asia/Shanghai');


$yaf = new \Yaf\Application( dirname( __DIR__ ).'/config/fish.ini', 'dev' );

Y::http()->response();
