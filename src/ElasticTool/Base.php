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
     */
    public function returnError($error_name){

        $config = self::config('message.'.$error_name);

        exit();

    }


    /**
     *
     */
    public function config(){

    }
}