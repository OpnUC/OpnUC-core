<?php

namespace App\Libs;

use Illuminate\Support\Manager;

/**
 * Class PbxLinkerManager
 * @package App\Libs
 */
class PbxLinkerManager extends Manager
{

    /**
     * 有効な接続を保持する配列
     * @var array
     */
    protected $connections = [];

    /**
     * 標準のドライバーを取得する
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['opnuc.pbx_linker.type'];
    }

    /**
     * 標準のドライバーを設定する
     * @param  string $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['opnuc.pbx_linker.type'] = $name;
    }

    /**
     * 切断・コネクションの廃棄
     *
     * @param  string $name
     * @return void
     */
    public function purge($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        //$this->disconnect($name);

        unset($this->connections[$name]);
    }

    /**
     * PBXとの接続を行う
     * @param  string $name
     * @return mixed
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if(isset($this->customCreators[$name])){
            return $this->callCustomCreator($name);
        }

        if (!isset($this->connections[$name])) {
            $driverMethod = 'create'.ucfirst($name).'Driver';

            if (method_exists($this, $driverMethod)) {
                $this->connections[$name] = $this->{$driverMethod}();
            } else {
                throw new \InvalidArgumentException("Driver [$name] is not supported.");
            }
        }

        return $this->connections[$name];
    }

    /**
     * Return all of the created connections.
     *
     * @return array
     */
    public function getConnections()
    {
        return $this->connections;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string $method
     * @param  array $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->connection()->{$method}(...$parameters);
    }

}