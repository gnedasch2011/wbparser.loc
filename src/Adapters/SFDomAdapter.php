<?php


namespace WB\Adapters;

use Symfony\Component\DomCrawler\Crawler;
use WB\Pages\SearchPage;


class SFDomAdapter implements DomAdapter
{
    /** @var string $content */
    protected $content;

    /**
     * @inheritDoc
     */
    function fetchMaxPage()
    {
        $dom = new Crawler($this->content);
        $raw = $dom->filter(".goods-count")->text();
        $products = (int) preg_replace("/[^0-9]/", "", $raw);
        return ceil($products / SearchPage::PER_PAGE);
    }

    function fetchProductName()
    {
        $dom = new Crawler($this->content);
        $raw = $dom->filter(".brand-and-name")->text();
        return $this->normalizeName($raw);
    }

    function matchProductName($productName)
    {
        $key = $this->normalizeName($productName);
        $dom = new Crawler($this->content);
        $pos = 1;
        $found = false;
        $dom
            ->filter(".dtlist-inner-brand")
            ->each(function ($div) use ($key, &$pos, &$found) {
                // цена меняется слишком часто
                // $priceBlock = $this->normalizePrice($div->filter(".lower-price")->text());
                $nameBlock  = $this->normalizeName($div->filter(".dtlist-inner-brand-name")->text());
                $pair = sprintf("%s", $nameBlock);
                if ($pair === $key) {
                    $found = true;
                    return;
                }

                if (! $found) {
                    $pos++;
                }
            });
        return $found? $pos : $found;
    }

    function normalizeName($name)
    {
        return preg_replace( '/[\x00-\x1f]/u', '', $name);
    }

    function normalizePrice($price)
    {
        return preg_replace( '/[^0-9]/u', '', $price);
    }


    function setContent(&$content)
    {
        $this->content =& $content;
    }
}
