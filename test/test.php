<?php
require '../vendor/autoload.php';

use ElasticTool\Operation;

$config['hosts'] = [
    '192.168.0.85:9200'
];

$config['index'] = 'sms_send_log';

$client = new Operation($config);

$res = $client->where('sid','1000202002205195092900');

var_dump($res);
