<?php


namespace app\dgut\service;


use think\facade\Cache;

class BaseService
{
    public function saveCookiesToCache($key,$jar) {
        Cache::set($key,$jar);
    }

    public function getCookiesFromCacheByKey($key) {
        return unserialize(Cache::get($key));
    }
}