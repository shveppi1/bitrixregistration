<?php

use Bitrix\Main;
Main\EventManager::getInstance()->addEventHandler(
    'sale',
    'OnSaleOrderBeforeSaved',
    'myFunction'
);
//в обработчике получаем сумму, с которой планируются некоторые действия в дальнейшем:
function myFunction(Main\Event $event)
{

    $order = $event->getParameter("ENTITY");

    if(!\Bitrix\Main\Loader::includeModule('iblock'))

        return;

    foreach ($order->getBasket() as $item) {

        if (!$offerId = intval($item->getProductId()))
            continue;
         $product = CCatalogSku::GetProductInfo($offerId);

        $element = \CIBlockElement::GetList(
            array(),
            array("=ID"=>$product['ID']), false,
            array("nTopCount"=>1),
            array("ID", "IBLOCK_ID", "PROPERTY_INT_BUY")
        );

        if(!$obElement=$element->fetch())
            continue;

        $int_buy = (int)$obElement['PROPERTY_INT_BUY_VALUE'] + 1;

        CIBlockElement::SetPropertyValuesEx($obElement['ID'], false, array('INT_BUY' => $int_buy));
    }
}