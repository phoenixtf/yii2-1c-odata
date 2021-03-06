<?php
/**
 * Created by PhpStorm.
 * User: execut
 * Date: 11/8/17
 * Time: 5:10 PM
 */

namespace execut\oData;


use yii\base\Component;
use yii\caching\TagDependency;

class Client extends Component
{
    public $host = null;
    public $path = null;
    public $options = [];
    public $customColumnsTypes = [];
    protected $_client = null;
    protected $cache = [];

    public function init()
    {
        parent::init(); // TODO: Change the autogenerated stub
        $profiler = new Profiler();
        $this->_client = new \Kily\Tools1C\OData\Client(trim($this->host, '/') . '/' . $this->path, $this->options, $profiler);
    }

    public function __call($name, $params)
    {
        if (!empty($params[1]) && $params[1] === ActiveQuery::EMPTY_CONDITION_STUB) {
            $this->_client->reset();
            return [];
        }

        if ($name === 'get') {
            $cacheKey = $name . serialize($params);
            if ($result = $this->getCache($cacheKey)) {
                $this->_client->reset();

                return $result;
            }
        } else {
            $this->cache = [];
        }

        $result = call_user_func_array([$this->_client, $name], $params);
        if ($name === 'get') {
            $this->setCache($cacheKey, $result);
        }

        return $result;
    }

    public function getCache($key) {
        /*if (!empty($this->cache[$key])) {
            return $this->cache[$key];
        }*/
    }

    public function setCache($key, $value) {
        $this->cache[$key] = $value;

        return $this;
    }

    public function __get($name)
    {
        $this->_client->$name;

        return $this;
    }
}
