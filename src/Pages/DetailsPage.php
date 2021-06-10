<?php


namespace WB\Pages;


use WB\Adapters\DomAdapter;
use WB\Adapters\HttpAdapter;
use WB\Crawler;
use yii\base\BaseObject;

class DetailsPage extends BaseObject implements Page
{
    public $detailsUrl;

    /** Зависимости */
    /** @var Crawler $crawler */
    protected $crawler;

    /** @var DomAdapter $dom */
    protected $dom;

    /** @var HttpAdapter $http */
    protected $http;

    public function init()
    {
        if (! $this->crawler->hasParam("detailsUrl"))
        {
            $this->crawler->stop();
            return;
        }

        if (! isset($this->detailsUrl))
        {
            $this->detailsUrl = $this->crawler->getParam("detailsUrl");
        }
    }

    function execute()
    {
        $html = $this->http->getContent($this->detailsUrl);

        $this->dom->setContent($html);
        $productName = $this->dom->fetchProductName();
        $this->crawler->setParam("productName", $productName);
        $this->crawler->addPage(new SearchPage([
            'crawler' => $this->crawler,
            'http'    => $this->http,
            'dom'     => $this->dom
        ]));
    }

    /**
     * @return Crawler
     */
    public function getCrawler(): Crawler
    {
        return $this->crawler;
    }

    /**
     * @param Crawler $crawler
     */
    public function setCrawler(Crawler $crawler): void
    {
        $this->crawler = $crawler;
    }

    /**
     * @return DomAdapter
     */
    public function getDom(): DomAdapter
    {
        return $this->dom;
    }

    /**
     * @param DomAdapter $dom
     */
    public function setDom(DomAdapter $dom): void
    {
        $this->dom = $dom;
    }

    /**
     * @return HttpAdapter
     */
    public function getHttp(): HttpAdapter
    {
        return $this->http;
    }

    /**
     * @param HttpAdapter $http
     */
    public function setHttp(HttpAdapter $http): void
    {
        $this->http = $http;
    }
}
