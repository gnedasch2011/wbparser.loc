<?php


namespace WB\Adapters;


class CurlAdapter implements HttpAdapter
{
    protected $cookieFile = "./cookies.txt";
    protected $timeout = 5000;
    protected $failed = false;

    function getContent($url)
    {
        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_COOKIESESSION, true );
        curl_setopt( $ch, CURLOPT_COOKIEJAR,  $this->getCookieFile());
        curl_setopt( $ch, CURLOPT_COOKIEFILE, $this->getCookieFile() );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    public function getCookieFile()
    {
        return $this->cookieFile;
    }

    public function isFailed()
    {
        return $this->failed;
    }

    public function setTimeout($ms)
    {
        $this->timeout = $ms;
    }
}
