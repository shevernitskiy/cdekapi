<?php

namespace shevernitskiy\CdekApi;

class Data
{
    public static function orderStatus($code = 0)
    {
        $codeStatus = [
            0 => false,
            1 => 'Создан',
            2 => 'Удален',
            3 => 'Принят на склад отправителя',
            4 => 'Вручен',
            5 => 'Не вручен',
            6 => 'Выдан на отправку в г. отправителе',
            7 => 'Сдан перевозчику в г. отправителе',
            8 => 'Отправлен в г. получатель',
            9 => 'Встречен в г. получателе',
            10 => 'Принят на склад доставки',
            11 => 'Выдан на доставку',
            12 => 'Принят на склад до востребования',
            13 => 'Принят на склад транзита',
            16 => 'Возвращен на склад отправителя',
            17 => 'Возвращен на склад транзита',
            18 => 'Возвращен на склад доставки',
            19 => 'Выдан на отправку в г. транзите',
            20 => 'Сдан перевозчику в г. транзите',
            21 => 'Отправлен в г. транзит',
            22 => 'Встречен в г. транзите',
        ];
        return $codeStatus[$code];
    }

    public static function orderStatusExt($code = 0)
    {
        $codeStatus = [
            0 => false,
            1 => 'Возврат, неверный адрес',
            2 => 'Возврат, не дозвонились',
            3 => 'Возврат, адресат не проживает',
            4 => 'Возврат, не должен выполняться: вес отличается от заявленного более, чем на X г.',
            5 => 'Возврат, не должен выполняться: фактически нет отправления (на бумаге есть)',
            6 => 'Возврат, не должен выполняться: дубль номера заказа в одном акте приема-передачи',
            7 => 'Возврат, не должен выполняться: не доставляем в данный город/регион',
            8 => 'Возврат, повреждение упаковки, при приемке от отправителя',
            9 => 'Возврат, повреждение упаковки, у перевозчика',
            10 => 'Возврат, повреждение упаковки, на нашем складе/доставке у курьера',
            11 => 'Возврат, отказ от получения: Без объяснения',
            12 => 'Возврат, отказ от получения: Претензия к качеству товара',
            13 => 'Возврат, отказ от получения: Недовложение',
            14 => 'Возврат, отказ от получения: Пересорт',
            15 => 'Возврат, отказ от получения: Не устроили сроки',
            16 => 'Возврат, отказ от получения: Уже купил',
            17 => 'Возврат, отказ от получения: Передумал',
            18 => 'Возврат, отказ от получения: Ошибка оформления',
            19 => 'Возврат, отказ от получения: Повреждение упаковки, у получателя',
            20 => 'Частичная доставка',
            21 => 'Возврат, отказ от получения: Нет денег',
            22 => 'Возврат, отказ от получения: Товар не подошел/не понравился',
            23 => 'Возврат, истек срок хранения',
            24 => 'Возврат, не прошел таможню',
            25 => 'Возврат, не должен выполняться: является коммерческим грузом',
            26 => 'Утерян',
            27 => 'Не востребован, утилизация',
        ];
        return $codeStatus[$code];
    }
}
