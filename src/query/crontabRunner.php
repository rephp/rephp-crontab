<?php

namespace rephp\crontab\query;

/**
 * 运行可执行任务
 */
class crontabRunner
{
    /**
     * 运行批量任务
     * @param array  $taskList      任务列表
     * @param string $baseRunScript 任务前缀，选填，如:/usr/bin/php /www/cmd.php
     * @return array
     */
    public static function runTask(array $taskList, $baseRunScript = '', $logDir = '')
    {
        try {
            //定时执行脚本
            foreach ($taskList as $job) {
                //执行命令
                empty($baseRunScript) || $job['command'] = $baseRunScript . ' ' . $job['command'];
                $isSystemLog = isset($job['is_sys_log']) ? $job['is_sys_log'] : true;
                $progressNum = empty($job['num']) ? 1 : (int)$job['num'];
                $progressNum < 1 && $progressNum = 1;
                $res = self::doShellCommand($job['id'], $job['command'], $logDir, $isSystemLog, $progressNum);
            }
        } catch (\Exception $e) {
            return ['code' => 444, 'msg' => $e->getMessage()];
        }

        return ['code' => 200, 'msg' => 'success'];
    }

    /**
     * 后端执行shell命令
     * @param int     $id          任务编号id
     * @param string  $command     待执行shell命令
     * @param string  $logDir      输出日志文件存放的路径
     * @param boolean $systemLog   是否开启系统运行日志
     * @param int     $progressNum 运行进程数,不足则补
     * @return string|false 命令执行失败则返回false，执行成功则返回最后一行命令执行输出内容
     */
    public static function doShellCommand($id, $command, $logDir = '', $systemLog = true, $progressNum = 1)
    {
        $logFile = self::getLogFile($logDir, $id);
        $systemLog && file_put_contents($logFile, '[' . date('Y-m-d H:i:s') . '] 执行: ' . $command . "\n", FILE_APPEND);
        $fullCommand = 'count=$(ps aux |grep -E "' . $command . '$" | wc -l); if [ $count -lt ' . $progressNum . ' ]; then for i in $(seq $(expr ' . $progressNum . ' - $count)); do ' . $command . ' >> ' . $logFile . ' 2>&1 & done ;fi';

        return system($fullCommand);
    }

    /**
     * 获取日志完整路径
     * @param string $logDir 日志目录
     * @param int    $id     任务编号id
     * @return string
     */
    final protected static function getLogFile($logDir, $id = 0)
    {
        if (empty($logDir)) {
            return '/dev/null';
        }
        $logDir = str_replace('\\', '/', $logDir);
        substr($logDir, -1) == '/' || $logDir .= '/';
        $logDir .= 'crontab_logs/' . (int)$id . '/';
        //自动创建目录
        self::createLogDir($logDir);

        return realpath($logDir) . '/' . date('Y_m_d', time()) . '.log';
    }

    /**
     * 自动创建日志目录
     * @param string $logFile 日志文件完整文件名
     * @return bool
     */
    final protected static function createLogDir($logDir)
    {
        return file_exists($logDir) ? true : mkdir($logDir, 0755, true);
    }
}
