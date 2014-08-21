<?php
/**
 * Example usage of the KrakenAPIClient library. 
 *
 * See https://www.kraken.com/help/api for more info.
 *
 */

require_once 'KrakenAPIClient.php'; 

// your api credentials
$key = 'YOUR API KEY';
$secret = 'YOUR API SECRET';

// set which platform to use (currently only beta is operational, live available soon)
$beta = true; 
$url = $beta ? 'https://api.beta.kraken.com' : 'https://api.kraken.com';
$sslverify = $beta ? false : true;
$version = 0;

$kraken = new KrakenAPI($key, $secret, $url, $version, $sslverify);

// Query a public list of active assets and their properties: 
$res = $kraken->QueryPublic('Assets');
print_r($res);

/** 
 * Returned assets are keyed by their ISO-4217-A3-X names, example output:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [XBTC] => Array
 *                 (
 *                     [aclass] => currency
 *                     [altname] => BTC
 *                     [decimals] => 10
 *                     [display_decimals] => 5
 *                 )
 * 
 *             [XLTC] => Array
 *                 (
 *                     [aclass] => currency
 *                     [altname] => LTC
 *                     [decimals] => 10
 *                     [display_decimals] => 5
 *                 )
 * 
 *             [XXRP] => Array
 *                 (
 *                     [aclass] => currency
 *                     [altname] => XRP
 *                     [decimals] => 8
 *                     [display_decimals] => 5
 *                 )
 * 
 *             [ZEUR] => Array
 *                 (
 *                     [aclass] => currency
 *                     [altname] => EUR
 *                     [decimals] => 4
 *                     [display_decimals] => 2
 *                 )
 *             ...
 * )
 */

// Query public ticker info for BTC/USD pair:
$res = $kraken->QueryPublic('Ticker', array('pair' => 'XBTCZUSD'));
print_r($res);

/**
 * Example output:
 *
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [XBTCZUSD] => Array
 *                 (
 *                     [a] => Array
 *                         (
 *                             [0] => 106.09583
 *                             [1] => 111
 *                         )
 * 
 *                     [b] => Array
 *                         (
 *                             [0] => 105.53966
 *                             [1] => 4
 *                         )
 * 
 *                     [c] => Array
 *                         (
 *                             [0] => 105.98984
 *                             [1] => 0.13910102
 *                         )
 * 
 *                     ...
 *         )
 * )
 */

/**
 * Query public recent trades for BTC/EUR pair since 2013-08-07T18:20:42+00:00. 
 *
 * NOTE: the 'since' parameter is subject to change in the future: it's precision may be modified,
 *       and it may no longer be representative of a timestamp. The best practice is to base it
 *       on the 'last' value returned in the result set. 
 */
$res = $kraken->QueryPublic('Trades', array('pair' => 'XBTCZEUR', 'since' => '137589964200000000'));
print_r($res);

/**
 * Example output:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [XBTCZEUR] => Array
 *                 (
 *                     [0] => Array
 *                         (
 *                             [0] => 78.60500
 *                             [1] => 2.03990000
 *                             [2] => 1375897934.1176
 *                             [3] => s
 *                             [4] => m
 *                             [5] => 
 *                         )
 * 
 *                     [1] => Array
 *                         (
 *                             [0] => 79.41809
 *                             [1] => 2.02203000
 *                             [2] => 1375898123.0771
 *                             [3] => b
 *                             [4] => m
 *                             [5] => 
 *                         )
 * 
 *                     [2] => Array
 *                         (
 *                             [0] => 79.86999
 *                             [1] => 7.00000000
 *                             [2] => 1375898123.2587
 *                             [3] => b
 *                             [4] => m
 *                             [5] => 
 *                         )
 *                     ...
 *             [last] => 137589925237491170
 * 
 */


// Query private asset balances
$res = $kraken->QueryPrivate('Balance');
print_r($res);

/**
 * Example output:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [ZUSD] => 3415.8014
 *             [ZEUR] => 155.5649
 *             [XBTC] => 149.9688412800
 *             [XXRP] => 499889.51600000
 *         )
 * 
 * )
 */

// Query private open orders and included related trades
$res = $kraken->QueryPrivate('OpenOrders', array('trades' => true));
print_r($res);

/**
 *  Example result:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [open] => Array
 *                 (
 *                     [O7ICPO-F4CLJ-MVBLHC] => Array
 *                         (
 *                             [refid] => 
 *                             [userref] => 
 *                             [status] => open
 *                             [opentm] => 1373750306.9819
 *                             [starttm] => 0
 *                             [expiretm] => 0
 *                             [descr] => Array
 *                                 (
 *                                     [order] => sell 3.00000000 BTCUSD @ limit 500.00000
 *                                 )
 * 
 *                             [vol] => 3.00000000
 *                             [vol_exec] => 0.00000000
 *                             [cost] => 0.00000
 *                             [fee] => 0.00000
 *                             [price] => 0.00000
 *                             [misc] => 
 *                             [oflags] => 
 *                         )
 *                     ...
 *                 )
 *         )
 * )
 * 
 */

// Add a standard order: sell 1.123 BTC/USD @ limit $120 
$res = $kraken->QueryPrivate('AddOrder', array(
    'pair' => 'XBTCZUSD', 
    'type' => 'sell', 
    'ordertype' => 'limit', 
    'price' => '120', 
    'volume' => '1.123'
));
print_r($res);

/**
 * Example output:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [descr] => Array
 *                 (
 *                     [order] => sell 1.12300000 BTCUSD @ limit 120.00000
 *                 )
 * 
 *             [txid] => Array
 *                 (
 *                     [0] => OAVY7T-MV5VK-KHDF5X
 *                 )
 * 
 *         )
 * 
 * )
 * 
*/

// Add a standard order: buy €300 worth of BTC at market at 2013-08-12T09:27:22+0000 
$res = $kraken->QueryPrivate('AddOrder', array(
    'pair' => 'XBTCZEUR', 
    'type' => 'buy', 
    'ordertype' => 'market', 
    'oflags' => 'viqc',
    'volume' => '300', 
    'starttm' => '1376299642' 
));
print_r($res);

/**
 * Example output:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [descr] => Array
 *                 (
 *                     [order] => buy 300.00000000 BTCEUR @ market
 *                 )
 * 
 *             [txid] => Array
 *                 (
 *                     [0] => ONQN65-L2GNR-HWJLF5
 *                 )
 * 
 *         )
 * 
 * )
 * 
 */

/**
 Add a standard order: buy 2.12345678 BTCUSD @ limit $101.9901 with 2:1 leverage, with
 a follow up stop loss, take profit sell order: stop at -5% loss, take profit at 
 +$10 price increase (signed stop/loss prices determined automatically using # notation): 
*/
$res = $kraken->QueryPrivate('AddOrder', array(
    'pair' => 'XBTCZUSD',
    'type' => 'buy',
    'ordertype' => 'limit',
    'price' => '101.9901', 
    'volume' => '2.12345678',
    'leverage' => '2:1', 
    'close' => array(
        'ordertype' => 'stop-loss-profit',
        'price' => '#5%',  // stop loss price (relative percentage delta)
        'price2' => '#10'  // take profit price (relative delta)
    )
));
print_r($res);

/*
 * Example output:
 * 
 * Array
 * (
 *     [error] => Array
 *         (
 *         )
 * 
 *     [result] => Array
 *         (
 *             [descr] => Array
 *                 (
 *                     [order] => buy 2.12345678 BTCUSD @ limit 101.99010 with 2:1 leverage
 *                     [close] => close position @ stop loss -5.0000%, take profit +10.00000
 *                 )
 * 
 *             [txid] => Array
 *                 (
 *                     [0] => OFMYYE-POAPQ-63IMWL
 *                 )
 * 
 *         )
 * 
 * )
 * 
*/
