<?php


namespace ElasticTool;


class Operation extends Base
{
    //解决高并发冲突问题
    protected $retry_on_conflict=15;

    public function __construct($config)
    {
        parent::__construct($config);
    }


    public function add($data){

        $params = $this->setAddParams($data);

        $response = $this->client->bulk($params);

        return $response;
    }


    public function update($data){

        $params = $this->setUpdateParams($data);

        $response = $this->client->bulk($params);

        return $response;
    }

    public function del($data){

        $params = $this->setDelParams($data);

    }

    private function setDelParams($data){

        //判断是否为多维数组
        if(count($data) == count($data,1)){

            $index = array();
            $index['_index'] = $this->index;

            //判断是否有添加
            if($data['index_id']){
                $index['_id'] = $data['index_id'];
                unset($data['index_id']);
            }else{
                return self::NOT_INDEX_ID;
            }

            $params['body'][] = [
                'delete' => $index
            ];


        }else {

            foreach ($data as $v){

                $index = array();
                $index['_index'] = $this->index;

                //判断是否有添加索引id
                if($data['index_id']){
                    $index['_id'] = $data['index_id'];
                    unset($data['index_id']);
                }else{
                    return self::NOT_INDEX_ID;
                }

                $params['body'][] = [
                    'delete' => $index
                ];

            }
        }

        return $params;

    }


    private function setUpdateParams($data){


        //判断是否为多维数组
        if(count($data) == count($data,1)){

            $index = array();
            $index['_index'] = $this->index;

            //判断是否有添加
            if($data['index_id']){
                $index['_id'] = $data['index_id'];
                unset($data['index_id']);
            }else{
                return self::NOT_INDEX_ID;
            }

            $params['body'][] = [
                'update' => $index
            ];
            $params['body'][] = $data;

        }else {

            foreach ($data as $v){

                $index = array();
                $index['_index'] = $this->index;

                //判断是否有添加索引id
                if($data['index_id']){
                    $index['_id'] = $data['index_id'];
                    unset($data['index_id']);
                }else{
                    return self::NOT_INDEX_ID;
                }

                $params['body'][] = [
                    'update' => $index
                ];
                $params['body'][] = $data;

            }
        }

        return $params;

    }


    private function setAddParams($data){

        //判断是否为多维数组
        if(count($data) == count($data,1)){

            $index = array();
            $index['_index'] = $this->index;

            //判断是否有添加
            if($data['index_id']){
                $index['_id'] = $data['index_id'];
                unset($data['index_id']);
            }

            $params['body'][] = [
                'index' => $index
            ];
            $params['body'][] = $data;

        }else {

            foreach ($data as $v){

                $index = array();
                $index['_index'] = $this->index;

                //判断是否有添加索引id
                if($data['index_id']){
                    $index['_id'] = $data['index_id'];
                    unset($data['index_id']);
                }

                $params['body'][] = [
                    'index' => $index
                ];
                $params['body'][] = $data;

            }
        }


        return $params;
    }


}