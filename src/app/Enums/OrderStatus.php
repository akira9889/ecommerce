<?php

namespace App\Enums;

enum OrderStatus: string
{
    case Unpaid = 'unpaid';
    case Paid = 'paid';
    case Canceled = 'canceled';
    case Shipped = 'shipped';
    case Completed = 'completed';

    public static function getStatuses()
    {
        return [
            self::Unpaid->value => '未払い',
            self:: Paid->value => '支払い済み',
            self:: Canceled->value => 'キャンセル',
            self:: Shipped->value => '出荷済み',
            self:: Completed->value => '完了',
        ];
    }
}
