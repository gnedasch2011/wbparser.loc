<?php


namespace WB\Adapters;


interface HttpAdapter
{
    function getContent($url);
    function getCookieFile();
    function isFailed();
    function setTimeout($ms);
}
