<?php

namespace rephp\crontab\parse\parseNode;

use rephp\crontab\interfaces\parseScheduleNodeInterface;

/**
 * 解析范围节点
 * @package rephp\crontab\parse\parseNode
 */
class parseScope implements parseScheduleNodeInterface
{
    /**
     * 分析范围节点是否可以执行
     * @param string $scheduleNodeStr  节点字符串
     * @param int    $currentTimeValue 节点对应当前时间
     * @param int    $every            频率
     * @param int    $scheduleNodeInterval  当前节点所经历的时间段间隔
     * @return bool
     */
    public function isDo($scheduleNodeStr, $currentTimeValue, $every = 0, $scheduleNodeInterval=0)
    {
        return empty($every) ? self::isDoWithNoEvery($scheduleNodeStr, $currentTimeValue) : self::isDoWithEvery($scheduleNodeStr, $currentTimeValue, $every, $scheduleNodeInterval);
    }

    /**
     * 无频率情况分析
     * @param string $scheduleNodeStr  节点字符串
     * @param int    $currentTimeValue 节点对应当前时间
     * @return bool
     */
    final private static function isDoWithNoEvery($scheduleNodeStr, $currentTimeValue)
    {
        $res      = false;
        $rangeArr = explode('-', $scheduleNodeStr);
        if ($currentTimeValue >= $rangeArr[0] && $currentTimeValue <= $rangeArr[1]) {
            $res = true;
        }

        return $res;
    }

    /**
     * 有频率情况分析
     * @param string $scheduleNodeStr  节点字符串
     * @param int    $currentTimeValue 节点对应当前时间
     * @param int    $every            频率
     * @param int    $scheduleNodeInterval  当前节点所经历的时间段间隔
     * @return bool
     */
    final private static function isDoWithEvery($scheduleNodeStr, $currentTimeValue, $every, $scheduleNodeInterval=0)
    {
        $res      = false;
        $rangeArr = explode('-', $scheduleNodeStr);
        if ($currentTimeValue >= $rangeArr[0] && $currentTimeValue <= $rangeArr[1]) {
            $res = ($scheduleNodeInterval % $every == 0);
        }

        return $res;
    }

}