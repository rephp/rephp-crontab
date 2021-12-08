<?php

namespace rephp\crontab\parse\parseNode;

use rephp\crontab\interfaces\parseScheduleNodeInterface;

/**
 * 解析通配符节点
 * @package rephp\crontab\parse\parseNode
 */
class parseAll implements parseScheduleNodeInterface
{
    /**
     * 分析含有执行频率的任务节点是否可以运行
     * @param string $scheduleNodeStr  节点字符串
     * @param int    $currentTimeValue 节点对应当前时间
     * @param int    $every            频率
     * @param int  $scheduleNodeInterval  当前节点所经历的时间段间隔
     * @return bool
     */
    public function isDo($scheduleNodeStr, $currentTimeValue, $every = 0, $scheduleNodeInterval=0)
    {
        return empty($every) ? true : ($scheduleNodeInterval % $every == 0);
    }

}