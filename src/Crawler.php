<?php


namespace WB;

use WB\Pages\Page;
use yii\db\Exception;


class Crawler
{
    // план обхода (может быть изменен внутри обработчиков страниц)
    protected $pages = [];

    // глобальное хранилище данных для страниц (напр. для хранения макс. страницы)
    protected $context = [];

    protected $stoped = false;
    protected $counter = 0;

    /**
     * добавить страницу для обработки
     * @param Page $page
     */
    public function addPage($page)
    {
        $this->pages[] = $page;
    }

    /**
     * обход страниц
     */
    public function start()
    {
        $n = count($this->pages);

        for ($i = 0; $i < $n && !$this->stoped; $i++)
        {
            /** @var Page $curPage */
            $curPage = $this->pages[$i];
            $curPage->execute();
            $n = count($this->pages);
        }
        if (! $this->stoped)
        {
            $this->stop();
        }
    }

    /**
     * @param $state, curl_error, dom_error, mem_error, product_found, product_not_found
     */
    public function stop()
    {
        $this->stoped = true;
        $this->pages = [];
        print_r($this->context);
    }

    public function setParam($name, $value)
    {
        $this->context[$name] = $value;
    }

    public function hasParam($name)
    {
        return key_exists($name, $this->context);
    }

    public function getParam($name)
    {
        return $this->context[$name];
    }

    public function incParam($name)
    {
        $this->context[$name] += 1;
    }
}