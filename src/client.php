<?php
namespace rephp\crontab;

use rephp\crontab\parse\parseTask;
use rephp\crontab\query\crontabRunner;

/**
 * 计划任务执行客户端
 * $taskList = [
 *  [
 *      'desc'       => '每分钟执行一次测试任务',//任务说明,选填
 *      'schedule'   => '* * * * *',//执行时间计划,参数说明同linux crontab，必填
 *      'command'    => 'test/test2 444 154',//执行命令,必填
 *      'log_dir'    => 'e:/logs/',//日志存放目录，选填
 *      'is_sys_log' => false,//是否开启系统运行日志，选填
 *      'start_time' => '2021-12-08 12:00:00',//必填
 *      'num'        => 4,//运行进程数，选填，默认为1
 *      'status'     => true,//任务状态，选填，默认为真。真=正常状态；假=暂停状态；
 *  ],
 *  [
 *      'desc'     => '每小时的第5分钟执行一次任务',//任务说明,选填
 *      'schedule' => '5 * * * *',//执行时间计划,参数说明同linux crontab，必填
 *      'command'=>'echo "demo"',//执行命令,必填
 *      'log_dir' => 'e:/logs/',//日志存放目录，选填
 *      'is_sys_log' => true,//是否开启系统运行日志，选填
 *      'start_time' => '2021-12-08 12:00:00',//必填
 *      'num'        => 2,//运行进程数，选填，默认为1
 *      'status'     => true,//任务状态，选填，默认为真。真=正常状态；假=暂停状态；
 *  ]
 * ];
 * $test = new \rephp\crontab\client('/usr/bin/php index.php');
 * $res = $test->add($taskList)->run();
 * var_dump($res);exit;
 *
 * @package rephp\crontab
 */
class client
{
    /**
     * @var array $taskList 任务列表
     */
    protected $taskList;

    /**
     * @var string $baseRunScript 基本运行脚本，如/usr/local/php cli
     */
    protected $baseRunScript;

    /**
     * 实例化对象
     * @param string $baseRunScript
     * @return void
     */
    public function __construct($baseRunScript='')
    {
        empty($baseRunScript) || $this->baseRunScript = $baseRunScript;
    }

    /**
     * 添加任务到任务列表,可以添加一个或者多个，可以反复调用本接口进行追加任务
     * @param array $taskList 待添加的任务列表
     * @return $this
     */
    public function add($taskList)
    {
        if (empty($taskList)) {
            return $this;
        }
        //判断本次添加的是一个还是多个job
        (count($taskList) == count($taskList, 1)) && $taskList = [$taskList];
        foreach ($taskList as $task) {
            if(empty($task['schedule']) || empty($task['command']) || empty($cron['start_time'])){
                continue;
            }
            $task['schedule'] = preg_replace('/\s(?=\s)/', '\\1', $task['schedule']);
            $this->taskList[] = $task;
        }

        return $this;
    }

    /**
     * 执行全部jobs或者指定jobs
     * @param  array $taskList 任务列表
     * @return array
     */
    public function run($taskList = [])
    {
        try{
            //汇总任务列表
            $taskList = empty($taskList) ? $this->taskList : $this->filterTaskList($taskList);
            if(empty($taskList)){
                throw new \Exception('current no task', 200);
            }
            //分析任务
            $doTaskList = parseTask::getDoTaskList($taskList);
            if(empty($doTaskList)){
                throw new \Exception('current no task to do', 200);
            }
            //执行任务
            $result = crontabRunner::runTask($doTaskList, $this->baseRunScript);
        }catch (\Exception $e){
            $result = ['code'=>$e->getCode(), 'msg'=>$e->getMessage()];
        }

        return $result;
    }

    /**
     * 过滤无效任务,获得初步有效任务
     * @param array $taskList  任务列表，支持单个任务和批量任务
     * @return array
     */
    public function filterTaskList(array $taskList)
    {
        (count($taskList) == count($taskList, 1)) && $taskList = [$taskList];
        $result = [];
        $currentTime = time();
        foreach ($taskList as $task) {
            if( (isset($task['status']) && empty($task['status'])) || empty($task['schedule']) || empty($task['command']) || empty($cron['start_time'])){
                continue;
            }
            //时间范围之内
            $startTime = strtotime($cron['start_time']);
            if($currentTime<$startTime){
                continue;
            }
            $task['schedule'] = preg_replace('/\s(?=\s)/', '\\1', $task['schedule']);
            $result[] = $task;
        }

        return $result;
    }

}