<?php
/**
 * Created by PhpStorm.
 * User: hugo
 * Date: 2019-06-17
 * Time: 00:38
 */

namespace Hugostech\Trademe;


use Illuminate\Support\Facades\Facade;

class TrademeFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return new TradeMe();
    }
}
