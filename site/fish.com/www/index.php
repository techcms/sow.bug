<?php
use Sow\Bug as Y;

date_default_timezone_set('Asia/Shanghai');

$yaf = new \Yaf\Application( dirname( __DIR__ ).'/config/fish.ini', 'dev' );

$response = Y::http(True);