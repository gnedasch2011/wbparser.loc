<?php
require_once './vendor/autoload.php';
require_once './vendor/yiisoft/yii2/Yii.php';

use WB\Adapters\CurlAdapter;
use WB\Adapters\SFDomAdapter;
use WB\Pages\DetailsPage;
use WB\Crawler;

$curl = new CurlAdapter();
$dom = new SFDomAdapter();

$searchQuery = "Вечная пупырка";
$detailsUrl  = "https://gwww.wildberries.ru/catalog/21275926/detail.aspx?targetUrl=SP";

$crawler = new Crawler();
// $crawler->setParam("productName", $productName);
$crawler->setParam("searchQuery", $searchQuery);
$crawler->setParam("detailsUrl", $detailsUrl);


$pages = [];

$pages[] = new DetailsPage([
    'crawler' => $crawler,
    'http'    => $curl,
    'dom'     => $dom
]);


$crawler->addPage($pages[0]);
$crawler->start();
