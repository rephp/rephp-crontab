<?php

namespace rephp\crontab\parse;

/**
 * 任务解析
 */
class parseTask extends baseParse
{
    /**
     * 分析获取可执行任务列表
     * @param array $taskList 任务列表
     * @return array
     */
    public static function getDoTaskList(array $taskList)
    {
        $taskListNew = [];
        foreach ($taskList as $cron) {
            $isDo = self::getScheduleIsDo($cron['schedule'], strtotime($cron['start_time']));
            //汇总统计结果
            $isDo && $taskListNew[] = $cron;
        }

        return $taskListNew;
    }

    /**
     * 判断整个schedule是否可运行
     * @param string $schedule  linux运行时间计划字符串
     * @param int    $startTime 开始执行时间时间戳
     * @return bool
     */
    protected static function getScheduleIsDo($schedule = '', $startTime = 0)
    {
        $time            = time();
        $interval        = $time - $startTime;
        $schedule        = preg_replace('/\s(?=\s)/', '\\1', $schedule);
        $scheduleNodeArr = explode(' ', $schedule);
        $currentTimeArr  = explode('|', date('i|G|j|n|w', time()));
        //循环处理数据
        $result                  = true;
        $indexTimeIntervalConfig = [
            0 => 60,
            1 => 3600,
            2 => 86400,
            3 => 86400 * 30,
            4 => 86400 * 7,
        ];
        foreach ($currentTimeArr as $index => $currentTimeNode) {
            //根据时间所在索引，计算间隔值舍弃取整
            $tempInterval = floor($interval / $indexTimeIntervalConfig[$index]);
            $result       = empty($scheduleNodeArr[$index]) ? false : self::getScheduleNodeIsDo($scheduleNodeArr[$index], $currentTimeNode, $tempInterval);
            if (!$result) {
                break;
            }
        }

        return $result;
    }

    /**
     * 解析计划定时单节点参数
     * @param string $scheduleNodeStr  定时单节点参数字符串，有多种内容形式: *;单数字如3;范围如1-20;散列如1,2,3;混合如：1-21,2-22/10等
     * @param int    $currentTimeValue 当前时间对应的schedule值
     * @param int    $interval         当前节点所代表的时间间隔
     * @return boolean
     */
    final protected static function getScheduleNodeIsDo($scheduleNodeStr = '', $currentTimeValue = 0, $interval = 0)
    {
        $every = 0;
        strpos($scheduleNodeStr, '/') && list($scheduleNodeStr, $every) = explode('/', $scheduleNodeStr, 2);
        //查找第一个不是数字的符号
        $scheduleNodeMode = '\\rephp\\crontab\\parse\\parseNode\\' . self::getScheduleNodeMode($scheduleNodeStr);
        $logic            = new $scheduleNodeMode();

        return $logic->isDo($scheduleNodeStr, $currentTimeValue, $every, $interval);
    }
}
