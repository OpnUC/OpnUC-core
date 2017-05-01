<?php

namespace App\Libs;

use Illuminate\Support\Manager;

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
        return 'Asterisk';
//        return $this->app['config']['auth.defaults.guard'];
    }

    /**
     * 標準のドライバーを設定する
     * @param  string $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
//        $this->app['config']['auth.defaults.guard'] = $name;
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
     * @return \Illuminate\Database\Connection
     */
    public function connection($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (!isset($this->connections[$name])) {
            $instance = new AsteriskLinker();
            $this->connections[$name] = $instance;
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