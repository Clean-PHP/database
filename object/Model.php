<?php
/*
 * Copyright (c) 2023. Ankio. All Rights Reserved.
 */

/**
 * Package: cleanphp\base
 * Class Model
 * Created By ankio.
 * Date : 2022/11/14
 * Time : 23:35
 * Description :
 */

namespace library\database\object;

use cleanphp\base\ArgObject;

abstract class Model extends ArgObject
{
    public int $id = 0;
    private bool $fromDb = false;

    public function __construct(array $item = [], $fromDb = false)
    {
        $this->fromDb = $fromDb;
        parent::__construct($item);
    }

    public function onParseType(string $key, mixed &$val, mixed $demo): bool
    {
        if ($this->fromDb && is_string($val) && (is_array($demo) || is_object($demo))) {
            $val = __unserialize($val);
        }

        if ($this->fromDb && is_string($demo) && !$this->inNofilter($key)) {
            if (empty($val)) {
                $val = $demo;
            }
            $val = htmlspecialchars($val);
        }

        if (!$this->fromDb && (is_array($demo) || is_object($demo)) && is_string($val)) {
            $val = json_decode($val, true);
        }

        return parent::onParseType($key, $val, $demo);
    }

    /**
     * 是否为不不要过滤的字段
     * @param $key
     * @return bool
     */
    private function inNofilter($key): bool
    {
        return in_array($key, $this->getNofilter());
    }

    /**
     * 获取不需要过滤的字段
     * @return array
     */
    public function getNofilter(): array
    {
        return [];
    }

    /**
     * 获取唯一字段
     * @return array
     */
    public function getUnique(): array
    {
        return [];
    }

    /**
     * @return bool
     */
    public function isFromDb(): bool
    {
        return $this->fromDb;
    }

    /**
     * 获取主键
     * @return SqlKey
     */
    public function getPrimaryKey(): SqlKey
    {
        return new SqlKey('id', 0, true);
    }


    public function onToArray(string $key, mixed &$value, &$ret): void
    {
        parent::onToArray($key, $value, $ret);
        if (is_array($value) || is_object($value)) {
            $value = __serialize($value);
        }
    }

    public function getDisableKeys(): array
    {
        return ["id"];
    }

    public function getFullTextKeys(): array
    {
        return [];
    }
}