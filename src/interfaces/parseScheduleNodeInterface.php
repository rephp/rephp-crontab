<?php
namespace rephp\crontab\interfaces;
/**
 * 解析linux任务执行时间计划节点的接口
 * @package rephp\crontab\interfaces
 */
interface parseScheduleNodeInterface
{
    /**
     * 分析任务节点是否可以运行
     * @param string $scheduleNodeStr 节点字符串
     * @param int  $currentTimeValue 节点对应当前时间
     * @param int  $every  频率
     * @param int  $scheduleNodeInterval  当前节点所经历的时间段间隔
     * @return bool
     */
    public function isDo($scheduleNodeStr, $currentTimeValue, $every, $scheduleNodeInterval);

}