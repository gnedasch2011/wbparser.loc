<?php


namespace WB\Pages;


interface Page
{
    function execute();
    function getCrawler();
}
