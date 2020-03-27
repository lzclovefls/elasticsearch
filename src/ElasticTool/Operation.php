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

        $params = $this->getParams($data,'create');

        $response = $this->client->bulk($params);

        return $response;
    }

    public function addOrUpdate($data){

        $params = $this->getParams($data,'index');

        $response = $this->client->bulk($params);

        return $response;
    }


    public function update($data){

        $params = $this->getParams($data,'update');

        $response = $this->client->bulk($params);

        return $response;
    }

    public function delete($data){

        $params = $this->getParams($data,'delete');

        $response = $this->client->bulk($params);

        return $response;

    }



    /**
     * 获取参数信息
     * @param $data
     * @param string $type
     */
    public function getParams($data,$type='index'){

        $params = array();
        //判断是否为多维数组
        if(count($data) == count($data,1)){

            $this->setParams($data,$type,$params);

        }else {


            foreach ($data as $v){

                $this->setParams($v,$type,$params);
            }
        }

        return $params;
    }


    /**
     * 设置参数信息
     * @param $data
     * @param $type
     * @param $params
     * @return mixed
     */
    public function setParams($data,$type,&$params){

        $index = array();
        $index['_index'] = $this->index;

        //判断是否有添加索引id
        if($data['index_id']){
            $index['_id'] = $data['index_id'];
            unset($data['index_id']);
        }else{
            $this->returnError('NOT_FOUND_INDEX');
        }

        //根据类型进行处理
        switch($type){
            case 'index':
            case 'create':
                $params['body'][] = [
                    $type => $index
                ];
                $params['body'][] = $data;
                break;
            case 'update':
                $params['body'][] = [
                    $type => $index
                ];
                $params['body'][] = ['doc'=>$data];
                break;
            case 'delete':
                $params['body'][] = [
                    $type => $index
                ];
                break;
        }

        return $params;
    }




}