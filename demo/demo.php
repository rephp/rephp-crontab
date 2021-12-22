<?php

namespace demo;

require 'vendor/autoload.php';

$taskList = [
    [
        'id'         => 3, //任务编号，必填，用于管理日志
        'desc'       => '每2分钟执行一次测试任务',//任务说明,选填
        'schedule'   => '*/2 * * * *',//执行时间计划,参数说明同linux crontab，必填
        'command'    => 'test/test2 444 154',//执行命令,必填
        'is_sys_log' => false,//是否开启系统运行日志，选填
        'start_time' => '2021-12-08 12:00:00',//任务开始生效时间，必填
        'num'        => 2,//运行进程数,选填，默认为1
    ],
    [
        'id'         => 4, //任务编号，必填，用于管理日志
        'desc'       => '每小时的第5分钟执行一次任务',//任务说明,选填
        'schedule'   => '15-59/1 * * * *',//执行时间计划,参数说明同linux crontab，必填
        'command'    => 'echo "demo"',//执行命令,必填
        'is_sys_log' => true,//是否开启系统运行日志，选填
        'start_time' => '2021-12-08 12:00:00',//任务开始生效时间，必填
        'num'        => 3,//运行进程数,选填，默认为1
    ],
];
$test     = new \rephp\crontab\client('/usr/bin/php /www/test/cmd', '/var/logs/');
$res      = $test->add($taskList)->run();
var_dump($res);
exit('执行完毕');
