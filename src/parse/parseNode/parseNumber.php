<?php

namespace rephp\crontab\parse\parseNode;

use rephp\crontab\interfaces\parseScheduleNodeInterface;

/**
 * 解析数字节点, 不允许有频率出现
 * @package rephp\crontab\parse\parseNode
 */
class parseNumber implements parseScheduleNodeInterface
{
    /**
     * 分析普通数字节点是否可以执行
     * @param string $scheduleNodeStr      节点字符串
     * @param int    $currentTimeValue     节点对应当前时间
     * @param int    $every                频率
     * @param int    $scheduleNodeInterval 当前节点所经历的时间段间隔
     * @return bool
     */
    public function isDo($scheduleNodeStr, $currentTimeValue, $every = 0, $scheduleNodeInterval = 0)
    {
        return ($currentTimeValue == $scheduleNodeStr);
    }
}
