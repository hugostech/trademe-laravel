<?php


namespace Hugostech\Trademe;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Psr7\Request;

//Error Handling
//200	The operation succeeded. Note that many operations return a standard type of response with Success and Description fields. If Success is false then this should be treated the same as if the response was a HTTP 400 error (with the Description field containing the error message).
//304	Used with caching to indicate that the cached copy is still valid.
//400	The request is believed to be invalid in some way. The response body will contain an error message. You should display the error message to the user.
//401	An OAuth authentication failure occurred. You should ask the user to log in again.
//429	Your rate limit has been exceeded. Your rate limit will reset at the start of the next hour. You should not attempt to make any more calls until then.
//500	A server error occurred. You should display a generic “whoops” error message to the user.

class Base
{
    protected $client;
    private $method;
    private $headers;
    private $query;
    private $json;

    public function __construct()
    {
        $this->client = new Client([
            // You can set any number of default request options.
            'base_uri' => config('trademe.tm_'.config('trademe.mode').'_url'),
            'timeout'  => config('trademe.tm_require_time_out', 5),
        ]);
        $this->method = 'GET';
        $this->setHeaders([]);
        $this->setJson([]);
        $this->query = [];
    }

    public function send($url){
        try{
            return $this->client->request($this->getMethod(), $url, [
                'headers'=>$this->getHeaders(),
                'query'=>$this->getQuery(),
                'json'=>$this->getJson(),
            ]);
        }catch (ClientException $e){
            $content = $e->getResponse()->getBody()->getContents();
            return \GuzzleHttp\json_decode($content, true);
        }

    }

    public function makeRequest($url){
        return new Request($this->getMethod(), $url, $this->getHeaders());
    }

    /**
     * add Authorzation to request header
     * @param array $headers
     * @param boolean $withToken
     * @return $this
     */
    private function signHeader($headers, $withToken=false){
        $oauth = [
            'oauth_consumer_key'=>config('trademe.tm_consumer_key'),
            'oauth_signature_method'=>'PLAINTEXT',
            'oauth_signature'=>config('trademe.tm_consumer_secret').'&',
        ];
        if ($withToken){
            $oauth['oauth_signature'] = config('trademe.tm_consumer_secret').'&'.config('trademe.oauth_token_secret');
            $oauth['oauth_token'] = config('trademe.oauth_token');
        }
        $signature = [];
        foreach ($oauth as $key=>$value){
            $value = urlencode($value);
            $signature[] = "$key=$value";
        }
        $signature = implode(',', $signature);

        $headers['Authorization'] = "OAuth {$signature}";
        return $headers;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @param string $method request mothed
     */
    public function setMethod(string $method)
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * @param array $headers
     * @param bool $withToken
     */
    public function setHeaders(array $headers, $withToken=false)
    {
        $this->headers = $this->signHeader($headers, $withToken);
        return $this;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param array $query
     */
    public function setQuery(array $query)
    {
        $this->query = $query;
        return $this;
    }

    /**
     * @param mixed $json
     */
    public function setJson($json)
    {
        $this->json = $json;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getJson()
    {
        return $this->json;
    }


}
