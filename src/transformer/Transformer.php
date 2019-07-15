<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 2019-06-16
 * Time: 00:30
 */

namespace Hugostech\Trademe;


use Illuminate\Database\Eloquent\Model;

abstract class Transformer
{
    abstract function transform(Model $model): array ;
}
