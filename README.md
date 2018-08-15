# OpenUC core

[![GitHub version](https://badge.fury.io/gh/OpnUC%2FOpnUC-core.svg)](https://badge.fury.io/gh/OpnUC%2FOpnUC-core)
[![Build Status](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/badges/build.png?b=develop)](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/build-status/master)
[![Code Coverage](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/?branch=master)
[![Code Intelligence Status](https://scrutinizer-ci.com/g/OpnUC/OpnUC-core/badges/code-intelligence.svg?b=master)](https://scrutinizer-ci.com/code-intelligence)
[![MIT License](http://img.shields.io/badge/license-MIT-green.svg?style=flat)](LICENSE)

## 必要環境

- PHP >= 7.0.0
- 拡張モジュール
    - OpenSSL
    - MySQLi
    - PDO
    - POD MySQL
    - Mbstring
    - Tokenizer
    - XML
    - XML Writer
    - DOM
    - Mcrypt
    - Ctype
    - Session
    
## インストール

### MySQL の初期設定

1. mysql -u root -p
~~~
USE mysql;
UPDATE user SET password=PASSWORD('SuperSecretPassword')  WHERE host='localhost' AND user='root';
FLUSH PRIVILEGES;

CREATE DATABASE OpnUC;
CREATE USER 'OpnUC'@'localhost' IDENTIFIED BY 'SecretPassword';
GRANT ALL PRIVILEGES ON OpnUC.* TO 'OpnUC'@'localhost';
FLUSH PRIVILEGES;
~~~

### OpnUCの導入

1. git clone https://github.com/OpnUC/OpnUC-core.git
1. cd OpnUC-core
1. composer install
1. cp .env.example .env
1. php artisan key:generate
1. jwt:generate
1. php artisan migrate
1. php artisan db:seed
1. php artisan storage:link
1. chmod 777 ./storage
1. chmod 777 ./bootstrap/cache/

### Laravel Echo Serverの導入

1. setenv CXX c++
2. npm -g install laravel-echo-server --sqlite=/usr/local/bin

### lighttpdの設定(参考)

~~~
$SERVER["socket"] == "0.0.0.0:443" {
	ssl.engine = "enable"
	ssl.pemfile = "/usr/local/etc/letsencrypt/live/voip.s-lines.net/ssl.pem"
	ssl.ca-file = "/usr/local/etc/letsencrypt/live/voip.s-lines.net/chain.pem"

	server.document-root = "/usr/local/www/OpnUC-core/public/"

	url.rewrite-once = (
			"^/(css|img|js|fonts)/.*\.(jpg|jpeg|gif|png|swf|avi|mpg|mpeg|mp3|flv|ico|css|js|woff|woff2|ttf)" => "$0",
			"^/(favicon\.ico|robots\.txt|sitemap\.xml)$" => "$0",
			"^/[^\?]*(\?.*)?$" => "index.php/$1"
	)
}
~~~

## License
MIT
