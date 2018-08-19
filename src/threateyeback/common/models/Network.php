<?php

namespace common\models;

use Yii;

class Network {

    /**
     * NetWork model
     *
     */
    const BESE_PATH = '/etc/sysconfig/network-scripts/';

    public static function getNetWork() {
        $data = [];
        foreach (scandir(self::BESE_PATH) as $filePath) {
            if ($filePath != 'ifcfg-lo' && preg_match('/^ifcfg-*?/', $filePath, $matches)) {
                $data[$filePath] = parse_ini_file(self::BESE_PATH . $filePath);
            }
        }
        return $data;
    }

    public static function setNetWork($post) {
        $basePath = self::BESE_PATH;
        $command_data['shell'] = '';
        $command_data['back_shell'] = '';
        // $post = self::getNetwork();
        foreach ($post as $filePath => $params) {
            $fileContent = '';
            foreach ($params as $key => $value) {
                $fileContent .= "{$key}=\"{$value}\"\n";
            }
            $command_data['shell'] .= "cp -f {$basePath}{$filePath} {$basePath}back_{$filePath}\n";
            $command_data['back_shell'] .= "cp -f {$basePath}back_{$filePath} {$basePath}{$filePath}\n";
            $command_data['shell'] .= "cat > {$basePath}{$filePath}<<-EOF\n";
            $command_data['shell'] .= "{$fileContent}";
            $command_data['shell'] .= "EOF\n\n";
        }
        $command_data['shell'] .= "systemctl restart network\n";
        $command_data['back_shell'] .= "systemctl restart network\n";
        $command_data['back_shell'] .= "systemctl start network\n";
        $command = new Command;
        $command->data = $command_data;
        $command->Type = Command::MSG_SHELL_SET_SYSTEM_IP;
        $command->status = Command::STATUS_UNSENT;
        $command->save();
        return $command;
    }

    public static function warning($msg, $path = 'warning') {
        self::put_contents($msg, $path, 'warning');
    }

}
