<?php
namespace Elasticsearch;

require __DIR__.'/../../vendor/autoload.php';


class Operation
{
    protected $size = 20;
    protected $from = 0;
    protected $where = array();
    protected $index = '';
    protected $client = '';
    protected $hosts = array();

    public function __construct($config)
    {
        $this->hosts = $config['hosts'];
        $this->index = $config['index'];


        //初始化elk客户端实例
        $this->client = ClientBuilder::create()           // Instantiate a new ClientBuilder
        ->setHosts($this->hosts)      // Set the hosts
        ->build();
    }

    /**
     * 分页处理
     * @param $size
     * @param $form
     */
    public function page($size,$from=0){

        $this->size = $size;
        $this->from = $from;

        return $this;
    }


    public function where($column,$value){

        $this->where = array(
            'term'=>[$column=>$value]
        );

        return $this;
    }

    public function select(){

        //设置查询信息
        $params = [
            'index' => $this->index,
            'type'  => '_doc',
            'size'  => $this->size,
            'from' => $this->from,
            'body'  => [
                'query' => [
                    "constant_score" => [
                        "filter" => [
                            "bool" => [
                                "must" => $this->where
                            ],
                        ]
                    ]
                ],
                "sort"=> ["created_at"=> ["order"=>"desc"]],
                "aggs" => ["total" => [ "value_count" => [ "field" => "sid" ]]]
            ]
        ];

        $res = $this->client->search($params);

        //获取数据和总数
        $data['data'] = array_column($res['hits']['hits'],'_source');
        $data['total'] = $res['hits']['total']['value'];

        return $data;
    }

}