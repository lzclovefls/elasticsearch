<?php

namespace ElasticTool;

class Query extends Base
{
    protected $size = '';  //每页显示个数
    protected $from = 0;   //偏移位置

    protected $type = 1; //获取类型：1 获取全部数据 2 获取分页数组

    protected $max_page = 10000;//最大条数

    protected $distance = [];//设置距离


    /***********筛选条件**************/
    protected $must = array(); //必须匹配
    protected $must_not = array(); //都不能匹配
    protected $should = array(); //至少匹配一个

    protected $sort = array(); //排序


    public function __construct($config)
    {
        parent::__construct($config);


    }


    /**
     * 设置条件
     * @param $data
     * @param $type 条件类型
     */
    public function where($data, $type = 1)
    {

        $range = array(); //查询范围设置
        $where = array(); //条件数组

        //遍历获取条件数组
        foreach ($data as $item) {

            switch ($item[1]) {
                case '=':
                    $where[]['term'] = [$item[0] => $item[2]];
                    break;
                case 'in':
                    $where[]['terms'] = [$item[0] => $item[2]];
                    break;
                case 'like':
                    $where[]['match_phrase'] = [$item[0] => ['query' => $item[2], 'slop' => 2]];
                    break;
                case '>':
                    $range[$item[0]] = ['gt' => $item[2]];
                    break;
                case '<':
                    $range[$item[0]] = ['lt' => $item[2]];
                    break;
                case '>=':
                    $range[$item[0]] = ['gte' => $item[2]];
                    break;
                case '<=':
                    $range[$item[0]] = ['lte' => $item[2]];
                    break;
                case "geo_dist": //距离过滤
                    $dist['distance'] = $item[2][0];
                    $dist[$item[0]] = $item[2][1];

                    $where[]["geo_distance"] = $dist;
                    break;
                case "geo_dist_range": //距离范围过滤
                    $dist['gte'] = $item[2][0][0];
                    if (isset($item[2][0][1])) {
                        $dist['lt'] = $item[2][0][1];
                    }
                    $dist[$item[0]] = $item[2][1];

                    $where[]["geo_distance_range"] = $dist;
                    break;

            }
        }

        if (!empty($range)) {
            $where[]['range'] = $range;
        }

        //根据类型设置条件全局变量
        switch ($type) {
            case 1;
                $this->must = $where;
                break;
            case 2;
                $this->must_not = $where;
                break;
            case 3;
                $this->should = $where;
                break;
        }


        return $this;

    }

    /**
     * 设置都不能匹配条件
     * @param $data
     * @return $this
     */
    public function whereNot($data)
    {

        $this->where($data, 2);

        return $this;
    }


    /**
     * 设置都匹配条件
     * @param $data
     * @return $this
     */
    public function whereOr($data)
    {

        $this->where($data, 3);

        return $this;
    }


    /**
     * 根据字段进行排序
     * @param $field
     * @param string $sort_type
     * @return $this
     */
    public function orderBy($field, $sort_type = 'asc')
    {

        $this->sort = [$field => ["order" => $sort_type == 'desc' ? 'desc' : 'asc']];

        return $this;

    }


    /**
     * 按距离排序
     * @param $field
     * @param $location
     * @param string $sort_type
     * @param string $unit
     */
    public function orderByGeo($field, $location, $sort_type = 'asc', $unit = 'm')
    {

        $sort[$field] = $location;
        $sort['order'] = $sort_type == 'desc' ? 'desc' : 'asc';
        $sort['unit'] = $unit;

        $this->sort['_geo_distance'] = $sort;

        return $this;
    }


    /**
     * 设置获取距离
     * @param $field
     * @param $location
     * @param string $unit
     */
    public function setDistance($field, $location)
    {

        $this->distance['_source'] = $field;
        $this->distance['script_fields'] = ["geo_distance" => [
            "script" => [
                "params" => $location,
                "inline" => "doc['location'].arcDistance(params.lat, params.lon)"
            ]]
        ];

        return $this;
    }


    /**
     * 获取数据
     * @return mixed
     */
    public function get()
    {

        //设置参数信息
        $params = $this->setParams();

        $res = $this->client->search($params);


        //获取数据和总数
        $data['data'] = array_column($res['hits']['hits'], '_source');
        $fields = array_column($res['hits']['hits'], 'fields');

        if (isset($this->sort['_geo_distance'])) {

            $sort = array_column($res['hits']['hits'], 'sort');
        }

        //遍历获取距离
        foreach ($data['data'] as $k => &$v) {

            $v['distance'] = (int)$fields[$k]['geo_distance'][0];
        }


        if ($this->type == 2) {

            $data['total'] = $res['aggregations']['total']['value'] > $this->max_page ? $this->max_page : $res['aggregations']['total']['value'];
        }


        if ($this->type == 1) {
            return $data['data'];
        } else {
            return $data;
        }

    }


    /**
     * 分页获取数据
     * @param $size
     * @param int $page
     */
    public function pageGet($size, $page = 1)
    {

        $this->size = $size;
        $this->from = ($page - 1) * $size;

        $this->type = 2;

        $res = $this->get();

        $data['data'] = $res['data'];
        $data['page'] = $page;
        $data['size'] = $size;
        $data['total'] = $res['total'];

        return $data;

    }

    /****************私有方法***********************/

    /**
     * 设置查询参数
     */
    private function setParams()
    {

        //设置查询信息
        $params = [
            'index' => $this->index,
            'type' => '_doc',
            'body' => [
                'query' => [
                    "constant_score" => [
                        "filter" => [
                            "bool" => [
                                "must" => $this->must,
                                "must_not" => $this->must_not,
                                "should" => $this->should
                            ]
                        ]
                    ]
                ],
            ]
        ];

        //判断是否有排序
        if ($this->sort) {
            $params['body']['sort'] = $this->sort;
        }

        //判断是否要获取距离
        if ($this->distance) {
            $params['body']['_source'] = $this->distance['_source'];
            $params['body']['script_fields'] = $this->distance['script_fields'];

        }

        //判断是否分页获取
        if ($this->type == 2) {
            $params['body']['size'] = $this->size;
            $params['body']['from'] = $this->from;
            $params['body']['aggs'] = ["total" => ["value_count" => ["field" => $this->index_field]]];
        }

        return $params;
    }

}