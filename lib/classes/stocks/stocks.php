<?php
// ***************************************************************
//       Author: Alexander Kabanov (shurikk@mail.ru)
//      Version: $Id$
//  Description: This class allows you to get stock quotes
//               from yahoo server.
//
// This program is free software. You can redistribute it and/or
// modify it under the terms of the GNU General Public License as
// published by the Free Software Foundation; either version 2 
// of the License.
//
// ***************************************************************
// modified by Jim Applegate for use with hcgPublic framework
// ***************************************************************

class stocks {
   var $_URL  = 'http://download.finance.yahoo.com/d/quotes.csv?f=sl1d1t1c1ohgv&e=.csv&s=';

    function stocks() 
    {
        // do nothing
    }

    function get_quotes($stocks_list)
    {
        if (!$stocks_list) return array();
        
        $this->stocks_list = $stocks_list;
        
        $symbols = '';
        foreach($this->stocks_list as $symbol => $name) {
            $symbol = rawurldecode($symbol);
            $symbols .= $symbols == '' ? $symbol : '+'.$symbol;
        }
        
        $lines  = $this->get_data($symbols);
        $this->last_quotes = $this->calculate($lines);
        
        return $this->last_quotes;
    }

    function get_data(&$symbols) 
    {
       global $_HCG_GLOBAL;
       
        $url = $this->_URL.$symbols;
//        $fp = fopen($url, "r");
//        $result = '';

//        while(!feof($fp)) {
//            $result .= fread($fp, 1024);
//        }

        // using PEAR HTTP_Request
        require_once 'HTTP/Request.php';

        $r =& new HTTP_Request($url);
        if ($_HCG_GLOBAL['proxy'] != "") {
           $r->setProxy($_HCG_GLOBAL['proxy'], $_HCG_GLOBAL['proxy_port']);
        }
        $response = $r->sendRequest();

        if (!PEAR::isError($response)) {
           $page = $r->getResponseBody();
           $lines = split("\n", $page, count($this->stocks_list));
        } else {
           $lines = "<br>Error Message: ".$response->getMessage();
        }

        
        
        return $lines;
    }

    function calculate(&$lines) 
    {
        $quotes = array();
        
        foreach($lines as $line) {
            $data = $this->parse($line);
            
            if ($data[4] > 0) {
                //$pchange = '+'.$pchange;
                $direction = 'up';
                $dchangeu = str_replace("+", "", "$data[4]");
            } elseif ($data[4] == 0) {
               $pchange = 0;
               $dchangeu = $data[4];
               $direction = 'unchanged';
            } else {
                $direction = 'down';
                $dchangeu = str_replace("-", "", $data[4]);
            }
            
            // calculate percent change
            if ($data[1] > 0 && $data[4] != 0) {
                $pchange = round((10000*$data[1]/($data[1]-$dchangeu))/100-100, 2);
            } else {
                $pchange = 0;
            }
            
            // calculate previous close
//            if ($data[1] > 0 && $data[4] > 0) {
//                $previous = $data[1] - $dchangeu;
//            } elseif ($data[1] > 0 && $data[4] < 0) {
//                $previous = $data[1] + $dchangeu;
//            } else {
//                $previous = $data[1];
//            }
            
            // change time to 24 system
            $timestamp = strtotime($data[2]." ".$data[3]);
            $time = strftime("%H:%M", $timestamp);
            
            // determine if market (NASDAQ) is still open
            if (($time > "09:30") && ($time < "16:00")) {
               $status = "Market Open";
            } else {
               $status = "Market Closed";
            }
             
            $name = isset($this->stocks_list[$data[0]]) ? $this->stocks_list[$data[0]] : $data[0];
            $name = $name != '' ? $name : $data[0];
            
            $quotes[] = array(
                    'symbol'     => $data[0],
                    'price_last' => $data[1],
                    'date'       => $data[2],
                    'time'       => $time,
                    'status'     => $status,
                    'dchange'    => $data[4],
                    'dchangeu'   => $dchangeu,
                    'price_min'  => $data[7],
                    'price_max'  => $data[6],
                    'previous'   => $data[5],
                    'pchange'    => $pchange,
                    'direction'  => $direction,
                    'name'       => $name,
                    'volume'     => number_format($data[8])
            );
        }
        
        return $quotes;
    }

    function parse(&$line) 
    {
        $line = ereg_replace('"','',$line);
        
        // [0] symbol, [1] price_last, [2] date, [3] time, [4] dchange, 
        // [5] open price, [6] price_max, [7] price_min, [8] volume
        return split(',', $line);
    }

    function get_last() 
    {
        return $this->last_quotes;
    }
}
?>