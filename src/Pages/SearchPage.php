<?php


namespace WB\Pages;


use WB\Adapters\CurlAdapter;
use WB\Adapters\DomAdapter;
use WB\Adapters\HttpAdapter;
use WB\Adapters\SFDomAdapter;
use WB\Crawler;
use yii\base\BaseObject;

class SearchPage extends BaseObject implements Page
{
    const URL_TEMPLATE = "https://www.wildberries.ru/catalog/0/search.aspx?%s";
    const PER_PAGE = 100;

    /** @var string $url */
    public $url;

    /** @var string $productName */
    public $productName;

    /** @var string $searchQuery */
    public $searchQuery;


    /** Зависимости */
    /** @var Crawler $crawler */
    protected $crawler;

    /** @var DomAdapter $dom */
    protected $dom;

    /** @var HttpAdapter $http */
    protected $http;

    /**
     * устанавливаем параметры в глобальный контекст
     */
    public function init()
    {   


        if (! $this->crawler->hasParam("productName")) {
            $this->crawler->stop();
            return;
        }

        if (! $this->crawler->hasParam("searchQuery")) {
            $this->crawler->stop();
            return;
        }

        if (! $this->crawler->hasParam("curPage"))
        {
            $this->crawler->setParam("curPage", 1);
        }
       
        $this->productName = $this->crawler->getParam("productName");
        $this->searchQuery = $this->crawler->getParam("searchQuery");

        if (! isset($this->url))
        {
            $curPage = $this->crawler->getParam('curPage');
            if ( $curPage > 1) {
                $this->url = $this->createUrl($this->searchQuery, $curPage);
            } else {
                $this->url = $this->createUrl($this->searchQuery);
            }
        }
    }

    public function execute()
    {
        var_dump($this->url);
        $html = $this->http->getContent($this->url);
        $this->dom->setContent($html);

        if (! $this->crawler->hasParam("maxPage"))
        {
            $maxPage = $this->dom->fetchMaxPage();
            $this->crawler->setParam("maxPage", $maxPage);
            $this->appendPages($maxPage, $this->crawler->getParam("curPage") + 1, $this->searchQuery);
        }

        if (($pos = $this->dom->matchProductName($this->productName)) !== false)
        {
            $oldPos = ($this->crawler->getParam("curPage")-1) * self::PER_PAGE;
            $this->crawler->setParam("pos", $oldPos + $pos);
            $this->crawler->stop();
        }

        $this->crawler->incParam("curPage");
    }

    /**
     * Добавляем страницы к плану обхода краулера
     * @param $maxPage
     * @param $curPage
     * @param $searchQuery
     */
    public function appendPages($maxPage, $curPage, $searchQuery)
    {
        for($i = $curPage; $i <= $maxPage; $i++)
        {
            $page = new SearchPage([
                "crawler"   => $this->crawler,
                "dom"       => new SFDomAdapter(),
                "http"      => new CurlAdapter(),
                "url"       => $this->createUrl($searchQuery, $i)
            ]);
            $this->crawler->addPage($page);
        }
    }

    public static function createUrl($productName, $pageNum = null)
    {
        $data = [];
        if (isset($pageNum)) {
            $data['page'] = $pageNum;
        }
        $data['search'] = $productName;
        $data['sort'] = 'popular';
        $data['fromSearchInput'] = 'true';
        return sprintf(self::URL_TEMPLATE, http_build_query($data));
    }

    /**
     * @param Crawler $crawler
     */
    public function setCrawler($crawler): void
    {
        $this->crawler = $crawler;
    }

    /**
     * @return Crawler
     */
    function getCrawler()
    {
        return $this->crawler;
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
