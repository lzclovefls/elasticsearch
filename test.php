<?php

require_once __DIR__.'/src/Elasticsearch/Operation.php';

use Elasticsearch\Operation;

$config['hosts'] = [
    '192.168.0.85:9200'
];
$config['index'] = 'sms_send_log';

$client = new Operation($config);

$res = $client->where('sid','1000202002205195092900')->page(20)->select();

var_dump($res);die;
