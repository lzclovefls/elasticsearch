<?php
namespace ElasticTool;

use Elasticsearch\ClientBuilder;

class Base{

    protected $index = '';  //索引
    protected $client = '';  //客户端
    protected $hosts = array(); //主机位置
    protected $index_field = ''; //索引字段

    public function __construct($config)
    {
        $this->index_field = $config['index_field'];
        $this->hosts = $config['hosts'];
        $this->index = $config['index'];

        //初始化elk客户端实例
        $this->client = ClientBuilder::create()->setHosts($this->hosts)->build();

    }

    /**
     * 返回错误信息
     * @param $error_name
     */
    public function returnError($error_name){

        $config = self::config('message.'.$error_name);

        exit($config);

    }


    /**
     * 返回配置信息
     * @param $str
     * @return array|false|string
     */
    public static function config($str){

        $keys = explode('.',$str);

        $configs = file_get_contents('../'.$keys[0].'.php');

        $config = array();
        switch (count($keys)){

            case 1:
                $config = $configs;
                break;
            case 2:
                $config = $configs[$keys[1]];
                break;
            case 3:
                $config = $configs[$keys[1]][$keys[2]];
                break;
        }

        return $config;
    }
}