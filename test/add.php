<?php
require '../vendor/autoload.php';

use ElasticTool\Query;

$config['hosts'] = [
    '192.168.0.85:9200'
];

$config['index'] = 'sms_task_log';
$config['index_field'] = 'tid';

$client = new \ElasticTool\Operation($config);

$where = array();

//设置插入信息
$insert[] = [
    //'index_id'=>'10000251411111111',
    'tid' => '10000251411111111',
    'sms_client_id' => '10000',
    'type' => '2',
    'send_type' => '',
    'send_time' => date('Y-m-d H:i:s'),
    'content' => 'hahaha1',
    'count' => '100',
    'total' => 10,
    'success' => 0,
    'failed' => 0,
    'status' => 2,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];
$insert[] = [
    //'index_id'=>'10000251411111112',
    'tid' => '10000251411111112',
    'sms_client_id' => '10000',
    'type' => '2',
    'send_type' => '',
    'send_time' => date('Y-m-d H:i:s'),
    'content' => 'hahaha1',
    'count' => '100',
    'total' => 10,
    'success' => 0,
    'failed' => 0,
    'status' => 2,
    'created_at' => date('Y-m-d H:i:s'),
    'updated_at' => date('Y-m-d H:i:s')
];

$res = $client->delete($insert);

var_export($res);
