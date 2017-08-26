<?php

declare(strict_types = 1);

namespace Payward;

use Payward\Exception\InvalidArgumentException;
use Payward\Exception\RuntimeException;

/**
 * Reference implementation for Kraken's REST API.
 *
 * See https://www.kraken.com/help/api for more info.
 *
 *
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 Payward, Inc
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
class KrakenClient
{
    protected $key;     // API key
    protected $secret;  // API secret
    protected $url;     // API base URL
    protected $version; // API version
    protected $curl;    // curl handle

    /**
     * Constructor for KrakenAPI.
     *
     * @param string $key       API key
     * @param string $secret    API secret
     * @param string $url       base URL for Kraken API
     * @param string $version   API version
     * @param bool   $sslverify enable/disable SSL peer verification.  disable if using beta.api.kraken.com
     */
    public function __construct($key, $secret, $url = 'https://api.kraken.com', $version = '0', $sslverify = true)
    {
        /* check we have curl */
        if (!\function_exists('curl_init')) {
            echo "[ERROR] The Kraken API client requires that PHP is compiled with 'curl' support.\n";
            exit(1);
        }

        $this->key = $key;
        $secret = \base64_decode($secret, true);
        if (false === $secret) {
            throw new InvalidArgumentException('Invalid API secret given');
        }

        $this->secret = $secret;
        $this->url = $url;
        $this->version = $version;
        $this->curl = \curl_init();

        \curl_setopt_array(
            $this->curl,
            [
                CURLOPT_SSL_VERIFYPEER => $sslverify,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_USERAGENT => 'Kraken PHP API Agent',
                CURLOPT_POST => true,
                CURLOPT_RETURNTRANSFER => true,
            ]
        );
    }

    public function __destruct()
    {
        if (\function_exists('curl_close')) {
            \curl_close($this->curl);
        }
    }

    /**
     * Query public methods.
     *
     * @param string $method  method name
     * @param array  $request request parameters
     *
     * @throws RuntimeException
     *
     * @return array request result on success
     */
    public function QueryPublic($method, array $request = [])
    {
        // build the POST data string
        $postdata = \http_build_query($request, '', '&');

        // make request
        \curl_setopt($this->curl, CURLOPT_URL, $this->url.'/'.$this->version.'/public/'.$method);
        \curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postdata);
        \curl_setopt($this->curl, CURLOPT_HTTPHEADER, []);
        $result = \curl_exec($this->curl);
        if ($result === false) {
            throw new RuntimeException('CURL error: '.\curl_error($this->curl));
        }

        // decode results
        $result = \json_decode($result, true);
        if (!\is_array($result)) {
            throw new RuntimeException('JSON decode error');
        }

        return $result;
    }

    /**
     * Query private methods.
     *
     * @param string $method  method path
     * @param array  $request request parameters
     *
     * @throws RuntimeException
     *
     * @return array request result on success
     */
    public function QueryPrivate($method, array $request = [])
    {
        if (!isset($request['nonce'])) {
            // generate a 64 bit nonce using a timestamp at microsecond resolution
            // string functions are used to avoid problems on 32 bit systems
            $nonce = \explode(' ', \microtime());
            $request['nonce'] = $nonce[1].\str_pad(\mb_substr($nonce[0], 2, 6), 6, '0');
        }

        // build the POST data string
        $postdata = \http_build_query($request, '', '&');

        // set API key and sign the message
        $path = '/'.$this->version.'/private/'.$method;
        $sign = \hash_hmac(
            'sha512',
            $path.\hash('sha256', $request['nonce'].$postdata, true),
            $this->secret,
            true
        );
        $headers = [
            'API-Key: '.$this->key,
            'API-Sign: '.\base64_encode($sign),
        ];

        // make request
        \curl_setopt($this->curl, CURLOPT_URL, $this->url.$path);
        \curl_setopt($this->curl, CURLOPT_POSTFIELDS, $postdata);
        \curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        $result = \curl_exec($this->curl);
        if ($result === false) {
            throw new RuntimeException('CURL error: '.\curl_error($this->curl));
        }

        // decode results
        $result = \json_decode($result, true);
        if (!\is_array($result)) {
            throw new RuntimeException('JSON decode error');
        }

        return $result;
    }
}
