<?php

namespace rephp\crontab\parse\parseNode;

use rephp\crontab\interfaces\parseScheduleNodeInterface;

/**
 * 解析枚举节点
 * @package rephp\crontab\parse\parseNode
 */
class parseHash implements parseScheduleNodeInterface
{
    /**
     * 分析枚举节点是否可以执行, 不允许有频率出现
     * @param string $scheduleNodeStr      节点字符串
     * @param int    $currentTimeValue     节点对应当前时间
     * @param int    $every                频率
     * @param int    $scheduleNodeInterval 当前节点所经历的时间段间隔
     * @return bool
     * @example 1,5,7,9; 1-5,8-10,13-22;
     */
    public function isDo($scheduleNodeStr, $currentTimeValue, $every = 0, $scheduleNodeInterval = 0)
    {
        if (strpos($scheduleNodeStr, '-') === false) {
            $scheduleTimeArr = explode(',', $scheduleNodeStr);
        } else {//兼容范围
            $scheduleTimeArr = [];
            $tempArr         = explode(',', $scheduleNodeStr);
            foreach ($tempArr as $str) {
                if (strpos($str, '-') === false) {
                    $scheduleTimeArr[] = $str;
                    continue;
                }
                list($min, $max) = explode('-', $str, 2);
                $arr             = range((int)$min, (int)$max, 1);
                $scheduleTimeArr = array_merge($scheduleTimeArr, $arr);
            }
        }

        return in_array($currentTimeValue, $scheduleTimeArr);
    }
}
