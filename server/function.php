<?php

// 随机分配名称
function randName(){
	$name = '';
	$list = ['xing','ying','zing'];

	$xing = ['赵','钱','孙','李','周','吴','郑','王','杨','毛','贾','余'];
	$ying = ['泽','国','梦','思','志','光','海','颖','家','红','强','利'];
	$zing = ['涛','星','友','璐','瑶','光','坤','宇','琪','晨','强','达'];

	shuffle($xing);
	shuffle($zing);
	shuffle($ming);

	$len = rand(2,3);
	$index = rand(0,11);

	for ($i=0; $i < $len; $i++) { 
		$a = $list[$i];
		$b = $$a;
		$c = $b[$index];
		$name .= $c;
	}
	
	return $name;
}


