<?php
require '../vendor/autoload.php';

use ElasticTool\Operation;

$config['hosts'] = [
    '192.168.0.85:9200'
];

$config['index'] = 'sms_send_log';
$config['index_field'] = 'sid';

$client = new Operation($config);

$where = array();
$where[] = array('sid','in',array('1000202002205195092900'));

$res = $client->where($where)->pageGet(10);

var_dump($res);
