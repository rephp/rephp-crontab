<?php
namespace rephp\crontabManager\parse;

/**
 * 解析基类
 */
abstract class baseParse
{
    /**
     * 检查计划节点字符串模式
     * @param $scheduleNodeStr
     * @return string
     */
    final protected static function getScheduleNodeMode($scheduleNodeStr)
    {
        $scheduleNodeStr = str_replace(' ', '', $scheduleNodeStr);
        $config          = ['*'=>'parseAll', ','=>'parseHash', '-'=>'parseScope'];
        $checkArr = array_keys($config);
        foreach ($checkArr as $checkStr) {
            if (strpos($scheduleNodeStr, $checkStr) !== false) {
                return $config[$checkStr];
            }
        }

        return 'parseNumber';
    }
}