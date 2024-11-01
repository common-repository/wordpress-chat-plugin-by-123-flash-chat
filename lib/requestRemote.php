<?php
/*
 * get Info Mode :
 *          1: body;
 *          0: header;
 *          2: validate server
 * topcmmRequestRemote::requestRemote($url, $mode)
 *
*/

class topcmmRequestRemote
{
    static  $timeout = 3;

    static function requestRemote($url,$mode=1)
    {
        /*
         * get Info Mode :
         *          1: body;
         *          0: header;
        */
        if(!empty($url))
        {
            if(function_exists("curl_init"))
            {
                return self::curlRequestRemote($url,$mode);
            }
            else if(ini_get("allow_url_fopen"))
            {
                if($mode == "1")
                {
                    return self::filegetContentsRequestRemote($url);
                }else
                {
                    return self::fsockopenRequestRemote($url);
                }

            }
            else
            {
                return false;
            }
        }
        else
        {
            return false;
        }
    }

    static function curlRequestRemote($url,$mode)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$timeout);
        //curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);
        //curl_setopt($ch, CURLOPT_REFERER,_REFERER_);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if($mode == "0")
        {
            curl_setopt($ch, CURLOPT_HEADER, true);
        }

        $r = curl_exec($ch);
        curl_close($ch);
        if($mode == "0")
        {
            return substr($r,0,17);
        }
        return $r;
    }

    static function filegetContentsRequestRemote($url)
    {
        $context = stream_context_create(array(
                'http' => array(
                        'timeout' => self::$timeout      // Timeout in seconds
                )
        ));
        return @file_get_contents($url,0,$context);
    }

    static function fsockopenRequestRemote($url)
    {
        $url = @parse_url($url);
        if($fp = @fsockopen($url['host'],empty($url['port'])?80:$url['port'],$errorno,$error,self::$timeout))
        {

            fputs($fp,"GET ".(empty($url['path'])?'/':$url['path'])." HTTP/1.1\r\n");
            fputs($fp,"Host:$url[host]\r\n\r\n");
            while(!feof($fp))
            {
                $tmp = fgets($fp);

                if(trim($tmp) == '')
                {
                    break;
                }
                else if(preg_match_all('|HTTP(.*)|U',$tmp,$arr))
                {
                    return $tmp;

                }
            }
            return false;
        }
        else
        {
            return false;
        }
    }


}

?>