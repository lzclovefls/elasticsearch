<?php
require '../vendor/autoload.php';

use ElasticTool\Query;

$config['hosts'] = [
    '192.168.0.85:9200'
];

$config['index'] = 'sms_send_log';
$config['index_field'] = 'sid';

$client = new Query($config);

$where = array();
$where[] = array('sid','in',array('1000003060002818300','1100003080470345200'));
$where[] = array('created_at','<=','2020-03-09 08:46:28');

$res = $client->where($where)->orderBy('created_at','desc')->pageGet(2,1);

var_dump($res);
