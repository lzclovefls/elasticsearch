<?php
require '../vendor/autoload.php';

use ElasticTool\Query;

$config['hosts'] = [
    '192.168.0.85:9200'
];

$config['index'] = 'sms_test_log';
$config['index_field'] = 'tid';

$client = new \ElasticTool\Operation($config);

$where = array();

//设置插入信息
$insert[] = [
    'index_id'=>2,
    'name'=>'abc',
    'location'=>"24.485946,118.183074"
];


$res = $client->add($insert);

var_export($res);
