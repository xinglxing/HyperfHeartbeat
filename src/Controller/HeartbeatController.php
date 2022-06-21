<?php

declare(strict_types=1);

namespace Hyperf\Heartbeat\Controller;

use Hyperf\DbConnection\Db;
use Hyperf\HttpServer\Annotation\Controller;
use Hyperf\HttpServer\Annotation\RequestMapping;
use Hyperf\HttpServer\Contract\RequestInterface;
use Hyperf\HttpServer\Contract\ResponseInterface;
use Hyperf\Redis\RedisFactory;
use Hyperf\Utils\ApplicationContext;

/**
 * @Controller(prefix="/custom/heartbeat")
 */
class HeartbeatController
{
    /**
     * @RequestMapping(path="index", methods="get,post")
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return mixed
     */
    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $container = ApplicationContext::getContainer();

        // 检查Redis链接
        $redisList = config('redis', []);
        foreach ($redisList as $key => $redis) {
            $value = uniqid();
            $redisClient = $container->get(RedisFactory::class)->get($key);
            $redisClient->setex('check_heartbeat_redis_' . $key, 30, $value);
        }

        // 检查数据库链接
//        $dbList = config('databases', []);
//        foreach ($dbList as $connect => $db) {
//            Db::connection($connect)->select('SHOW DATABASES;');
//        }

        $connect = 'default';
        Db::connection($connect)->select('SHOW DATABASES;');

        return 'ok';
    }
}
