<?php

namespace demo;

require 'vendor/autoload.php';

$taskList = [
    [
        'desc'       => '每2分钟执行一次测试任务',//任务说明,选填
        'schedule'   => '*/2 * * * *',//执行时间计划,参数说明同linux crontab，必填
        'command'    => 'test/test2 444 154',//执行命令,必填
        'log_dir'    => 'e:/logs/',//日志存放目录，选填
        'is_sys_log' => false,//是否开启系统运行日志，选填
        'start_time' => '2021-12-08 12:00:00',//任务开始生效时间，必填
    ],
    [
        'desc'       => '每小时的第5分钟执行一次任务',//任务说明,选填
        'schedule'   => '15-59/1 * * * *',//执行时间计划,参数说明同linux crontab，必填
        'command'    => 'echo "demo"',//执行命令,必填
        'log_dir'    => 'e:/logs/',//日志存放目录，选填
        'is_sys_log' => true,//是否开启系统运行日志，选填
        'start_time' => '2021-12-08 12:00:00',//任务开始生效时间，必填
    ],
];
$test     = new \rephp\crontab\client('php cmd');
$res      = $test->add($taskList)->run();
var_dump($res);
exit('执行完毕');