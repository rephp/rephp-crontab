<?php
namespace rephp\crontabManager;

use rephp\crontabManager\parse\parseTask;
use rephp\crontabManager\query\crontabRunner;

class crontabManager
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
     * @example
     *       [
     *          [
     *          'desc'       => '每分钟执行一次测试任务',
     *          'schedule'   => '* * * * *',
     *          'command'    => 'test/test2 444 154',
     *          'log_dir'    => '/var/logs/'
     *          'is_sys_log' => false,
     *          ],
     *          [
     *          'desc'     => '每小时的第5分钟执行一次任务',
     *          'schedule' => '5 * * * *',
     *          'command'=>'echo "demo"',
     *          'log_dir' => '/var/logs/'
     *          'is_sys_log' => true,
     *          ]
     *      ];
     */
    public function add($taskList)
    {
        if (empty($job)) {
            return $this;
        }
        //判断本次添加的是一个还是多个job
        (count($taskList) == count($taskList, 1)) && $taskList = [$taskList];
        foreach ($taskList as $task) {
            if(empty($task['schedule']) || empty($task['command'])){
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
            empty($taskList) && $taskList = $this->taskList;
            (count($taskList) == count($taskList, 1)) && $taskList = [$taskList];
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

}