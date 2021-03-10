<?php

namespace App\Domain;

use Illuminate\Support\Str;
use LogicException;

/**
 * Class BaseEntity
 *
 * @package App\Domain
 */
abstract class BaseEntity
{
    /**
     * 宣言されていないメソッドを呼び出した場合
     *
     * @param string $name コールしようとしたメソッドの名称
     * @param array $arguments メソッドに渡そうとしたパラメータ
     * @return mixed
     */
    public function __call(string $name, array $arguments)
    {
        $name = Str::snake($name);
        return $this->{$name};
    }
    
    /**
     * 宣言されていないプロパティの値を取得した場合
     *
     * @param string $name 操作しようとしたプロパティの名称
     */
    public function __get(string $name)
    {
        throw new LogicException("宣言されていないプロパティの値を取得しようとしました name: {$name}");
    }
    
    /**
     * 宣言されていないプロパティへ値の代入をした場合
     *
     * @param string $name 操作しようとしたプロパティの名称
     * @param mixed $value
     */
    public function __set(string $name, $value)
    {
        throw new LogicException("宣言されていないプロパティへ値を代入しようとしました name: {$name}");
    }
    
    /**
     * 宣言されていないプロパティの値がセットされているかどうかの確認を行った場合
     *
     * @param string $name 操作しようとしたプロパティの名称
     */
    public function __isset(string $name)
    {
        throw new LogicException("宣言されていないプロパティの値をissetしようとしました name: {$name}");
    }
}
