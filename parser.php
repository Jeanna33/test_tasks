<?php
global $argc, $argv;

$info_log=['views'=>0,
		   'urls'=>[],
		   'traffic'=>0,
		   'crawlers'=>['Google'=> 0 ,
						'Bing'  => 0 ,
						'Baidu' => 0,
						'Yandex'=> 0 ],
		   'statusCodes'=>[],
	       ];

/* views = количество записей
 * url = подсчёт количество уникальных url[8]
 * traffic = траффик, позиция [11]
 * crawlers = столько, сколько указано в задаче [13]
 * statusCodes = по заполнению [10]
 * */

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
  try {
	if (!file_exists($argv[1])) {
        throw new exception('File not found.');
    }
	$file=fopen($argv[1],"r");
	if (!$file) {
        throw new exception('File open failed.');
    }  
	while(!feof($file)){
		$parsing_row=[];
        preg_match('/^(\S+) (\S+) (\S+) \[([^:]+):(\d+:\d+:\d+) ([^\]]+)\] \"(\S+) (.*?) (\S+)\" (\S+) (\S+) "([^"]*)" "([^"]*)"$/' ,
        fgets($file), $parsing_row);
        if (count($parsing_row)!=14){
			throw new exception('log format has been changed');
		}
		$info_log['views']++;
		addNewMembers($info_log['statusCodes'],$parsing_row[10]);
		if($parsing_row[10]=='200') {
			$info_log['traffic']+=(int)$parsing_row[11];
		}
		addNewMembers($info_log['urls'],$parsing_row[8]);
		findSearchSystem($info_log['crawlers'],$parsing_row[13]);
	}
	fclose($file);
	$info_log['urls']=count($info_log['urls']);
	echo json_encode($info_log);
	return json_encode($info_log);
  } catch (exception $e) {
	  echo 'Exception: ',  $e->getMessage(), "\n";
  }
}
else 
	echo 'Неверное число параметров';
	return 0;
