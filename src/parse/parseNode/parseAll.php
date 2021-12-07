<?php

namespace rephp\crontabManager\parse\parseNode;

use rephp\crontabManager\interfaces\parseScheduleNodeInterface;

/**
 * 解析通配符节点
 * @package rephp\crontabManager\parse\parseNode
 */
class parseAll implements parseScheduleNodeInterface
{
    /**
     * 分析含有执行频率的任务节点是否可以运行
     * @param string $scheduleNodeStr  节点字符串
     * @param int    $currentTimeValue 节点对应当前时间
     * @param int    $every            频率
     * @return bool
     */
    public function isDo($scheduleNodeStr, $currentTimeValue, $every = 0)
    {
        return empty($every) ? true : ($currentTimeValue % $every == 0);
    }

}