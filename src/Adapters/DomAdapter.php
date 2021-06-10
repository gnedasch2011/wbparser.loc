<?php


namespace WB\Adapters;


interface DomAdapter
{
    /**
     * Расчет максимальной страницы
     * @return integer
     */
    function fetchMaxPage();

    /**
     * Парсинг имени товара
     * @return string
     */
    function fetchProductName();

    /**
     * Поиск имени товара в резульатах поиска
     * @param $productName
     * @return integer, позиция товара на странице
     */
    function matchProductName($productName);


    /**
     * Установка ссылки на html
     * @param $content
     * @return null
     */
    function setContent(&$content);
}
