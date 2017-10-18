<?php
function xcopy($src, $dst)
{
	if(is_file($src)){
		copy($src, $dst);
	}else if(is_dir($src)){
		mkdir($dst);
		$files = scandir($src);
		foreach($files as $file){
			if($file != '.' && $file != '..'){
				xcopy("$src/$file", "$dst/$file");
			}
		}
	}else{
		echo "invalid src option\n";
	}
}

function xrm($dir)
{
	if(is_file($dir)){
		unlink($dir);
	}else if(is_dir($dir)){
		$files = scandir($dir);
		foreach($files as $file){
			if($file != '.' && $file != '..'){
				xrm("$dir/$file");
			}
		}
		rmdir($dir);
	}else{
		echo "invalid src option\n";
	}
}

function getFileVersion($file)
{
	$md5 = md5_file($file);
	$v = hexdec(substr($md5, 0, 2) . substr($md5, -2));
	return $v;
}


xcopy('../client/bin-release/web/v1/index.html', '../web/game/game.html');
xcopy('../client/bin-release/web/v1/main.min.js', '../web/game/main.min.js');

xrm('../web/game/libs');
xrm('../web/game/resource');
xcopy('../client/bin-release/web/v1/libs', '../web/game/libs');
xcopy('../client/bin-release/web/v1/resource', '../web/game/resource');

//资源版本处理
$res = json_decode(file_get_contents('../web/game/resource/default.res.json'), true);
foreach($res['resources'] as &$item){
	$v = getFileVersion('../web/game/resource/'.$item['url']);
	$item['url'] = $item['url'] . '?v=' . $v;
}
file_put_contents('../web/game/resource/default.res.json', json_encode($res, JSON_UNESCAPED_SLASHES));

//代码版本处理
$game = file_get_contents('../web/game/game.html');
$mainv = getFileVersion("../web/game/main.min.js");
$game = str_replace('main.min.js', "main.min.js?v=$mainv", $game);
if(preg_match_all('#script egret="lib" src="(.*?)"#', $game, $matches)){
	foreach($matches[1] as $file){
		$v = getFileVersion('../web/game/' . $file);
		$game = str_replace($file, "$file?v=$v", $game);
	}
}
file_put_contents('../web/game/game.html', $game);

//入口版本处理
$v = getFileVersion("../web/game/game.html");
file_put_contents("../web/game/version.js", "location.href='game.html?v=$v';");