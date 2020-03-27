<?php
require '../vendor/autoload.php';

use ElasticTool\Query;

$config['hosts'] = [
    '192.168.0.85:9200'
];

$config['index'] = 'sms_test_log';
$config['index_field'] = 'sid';

$client = new Query($config);



$res = $client->where($where)->orderByGeo('location',"34.485946,118.183074",'desc','km')->pageGet(2,1);


var_dump($res);
