<?php

namespace Lib;


class Config
{
    private static $configPath = 'config';

    public static function get(string $key, $default = null)
    {
        $value = self::getEnv($key);
        if (null === $value) {
            $keys = explode('.', $key);
            $file = '';
            count($keys) >= 1 && $file = APP_PATH . DIRECTORY_SEPARATOR . self::$configPath . DIRECTORY_SEPARATOR . $keys[0] . '.php';
            if (
                is_file($file) &&
                ($configs = include $file) &&
                is_array($configs)
            ) {
                $parsedConfig = self::mergeArr('code', $configs);
                $parsedConfig[$keys[0]] = $configs;
                $value = $parsedConfig[$key] ?? $default;
                self::putEnv($parsedConfig);
            } else {
                $value = $default;
            }
        }
        return $value;
    }

    private static function mergeArr($preKey, $data)
    {
        $res = [];
        foreach ($data as $key => $value) {
            $res[$preKey . '.' . $key] = $value;
            if (is_array($value)) {
                $res += self::mergeArr($preKey . '.' . $key, $value);
            }
        }
        return $res;
    }

    private static function getEnv($key)
    {
        $value = getenv($key);
        if (null !== $value) {
            $value = json_decode($value, true);
        }
        return $value;
    }

    private static function putEnv(array $configs)
    {
        foreach ($configs as $key => $config) {
            putenv($key . '=' . json_encode($config));
        }
    }

}