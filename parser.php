<?php
global $argc, $argv;

$views=0;
$urls=[];
$traffic=0;
$crawlers=['Google'=> 0 ,
		   'Bing'=> 0 ,
		   'Baidu'=> 0,
		   'Yandex'=> 0 ];
$statusCodes=[];

/* views = количество записей
 * url = подсчёт количество уникальных url[12]
 * traffic = траффик, позиция [11]
 * crawlers = столько, сколько указано в задаче [13]
 * statusCodes = по заполнению [10]
 * */


    function parsingRow($log) {
        $matches=[];
        preg_match('/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) "([^"]*)" "([^"]*)"$/' ,
        $log, $matches);
        return $matches;
    }
    
    function addNewMembers(&$array,$newMember){
		if(isset($newMember)){
			if(array_key_exists($newMember,$array)) {
					$array[$newMember]++;
			}
			else{
					$array+=[$newMember=>1];
			}
		}
	}
	
	function findSearchSystem(&$crawlers,$params){
		foreach ($crawlers as $c=>$i) { 
			if(stristr($params,$c)) { 
				$crawlers[$c]++;
			}
		}
	}
	
if(count($argv)==2){
	$file=fopen($argv[1],"r");
	while(!feof($file)){
		$row = parsingRow(fgets($file));
		$views++;
		addNewMembers($statusCodes,$row[10]);
		if($row[10]=='200')
		$traffic+=(int)$row[11];
		addNewMembers($urls,$row[8]);
		findSearchSystem($crawlers,$row[13]);
	}
	fclose($file);
	return json_encode(['views'=>$views,
						'urls'=>count($urls),
						'traffic'=>$traffic,
						'crawlers'=>$crawlers,
						'statusCodes'=>$statusCodes,
	]);
}
else echo 'Ошибка, неверное число аргументов';
