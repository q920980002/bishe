<?php
/**
 * Created by PhpStorm.
 * User: weiyaheng
 * Date: 16/3/22
 * Time: 上午11:15
 */
namespace Robotx\Lib;
class GetConfig{

    /**
     * @param string $com_id
     * @return mixed
     * @throws \Exception
     */
    public static function getConfig($com_id)
    {
        if(!$com_id){
            throw new \Exception("企业号不能为空");
        }

        $file = YOUKE_DEFAULT_CONFIG_PATH.'/'.sprintf("db_%s.conf",$com_id);
        if (!file_exists($file)) {
            throw new \Exception('配置文件不存在，无法加载');
        }

        $config = json_decode(file_get_contents($file), true);
        if (!$config) {
            throw new \Exception('配置文件不是json格式，无法解析');
        }

        return $config;
    }

}