<?php

namespace rephp\crontab\query;

/**
 * 运行可执行任务
 */
class crontabRunner
{
    /**
     * 运行批量任务
     * @param array   $taskList      任务列表
     * @param string  $baseRunScript 任务前缀，选填，如:/usr/bin/php /www/cmd.php
     * @return array
     */
    public static function runTask(array $taskList, $baseRunScript = '')
    {
        try {
            //定时执行脚本
            foreach ($taskList as $job) {
                //执行命令
                empty($baseRunScript) || $job['command'] = $baseRunScript . ' ' . $job['command'];
                $isSystemLog = isset($job['is_sys_log']) ? $job['is_sys_log'] : true;
                $res = self::doShellCommand($job['command'], $job['log_dir'], $isSystemLog);
            }
        } catch (\Exception $e) {
            return ['code' => 444, 'msg' => $e->getMessage()];
        }

        return ['code' => 200, 'msg' => 'success'];
    }

    /**
     * 后端执行shell命令
     * @param string  $command   待执行shell命令
     * @param string  $logDir    输出日志文件存放的路径
     * @param boolean $systemLog 是否开启系统运行日志
     * @return string|false 命令执行失败则返回false，执行成功则返回最后一行命令执行输出内容
     */
    public static function doShellCommand($command, $logDir = '', $systemLog = true)
    {
        $logFile = self::getLogFile($logDir);
        $systemLog && file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] 执行: ' . $command . "\n", FILE_APPEND);
        $command .= ' >> ' . $logFile . ' 2>&1';
        return system($command);
    }

    /**
     * 获取日志完整路径
     * @param $logDir
     * @return string
     */
    final static protected function getLogFile($logDir)
    {
        if (empty($logDir)) {
            return '/dev/null';
        }
        $logDir = str_replace('\\', '/', $logDir);
        substr($logDir, -1) == '/' || $logDir .= '/';
        $logDir .= date('Y/m/d/', time());
        //自动创建目录
        self::createLogDir($logDir);

        return realpath($logDir) . '/crontab.log';
    }

    /**
     * 自动创建日志目录
     * @param string $logFile 日志文件完整文件名
     * @return bool
     */
    final static protected function createLogDir($logDir)
    {
        return file_exists($logDir) ? true : mkdir($logDir, 0755, true);
    }

}