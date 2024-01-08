<?php

namespace Fly321\QueueTask\Entity;

abstract class BaseEntity
{
    public function __construct(array $data = [])
    {
        $this->fill($data);
    }

    /**
     * 过滤数据并填充赋值
     * @param array $data
     * @return void
     */
    private function fill(array $data): void
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * 获取所有属性
     * @return array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
}