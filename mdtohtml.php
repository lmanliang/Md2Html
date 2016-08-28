#!/usr/bin/php -q
<?php
#
#
# md2html
# https://github.com/lmanliang/parsedown
#
# For the full license information, view the LICENSE file that was distributed
# with this source code.
#
#

include('Parsedown.php');
$Parsedown = new Parsedown();
if (empty($_SERVER['argv'][1]))
{
	die("Need source Directory\n");
}
if (empty($_SERVER['argv'][2]))
{
	die("Need target Directory\n");
}else{
	if (!file_exists($_SERVER['argv'][2])) { if(!mkdir ($_SERVER['argv'][2]))  die("Can't create directory.\n"); }
	if (!is_dir($_SERVER['argv'][2])) { die("Must the directory , not file.\n"); }
}


$files = getDir($_SERVER['argv'][1],$_SERVER['argv'][2]);

function getDir($dir,$target)
{
	global $Parsedown;
	$htmlSample = fread(fopen('./sample.html','r'),filesize('./sample.html'));
	$tf = [];
	$nonDir = [ '','.' , '..','.git','tools' ];
	$fdir = opendir($dir);
	while( $file = readdir($fdir) )
	{
		if ( !in_array($file,$nonDir))
		{
			echo $target.'/'.$file."\n";
			if ( is_dir($dir.'/'.$file))
			{
				if ( !is_dir($target.'/'.$file)) mkdir($target.'/'.$file);
				getDir($dir.'/'.$file,$target.'/'.$file);
			}else{
				$fileInfo = pathinfo($file);
				if (isset($fileInfo['extension']) && $fileInfo['extension'] == 'md')
				{
					$fn = fopen($dir.'/'.$file,'r');
					$fileContent = fread($fn , filesize($dir.'/'.$file));
					fclose($fn);

					$md = $Parsedown->text($fileContent);
					$ofn = fopen($target.'/'.$fileInfo['filename'].'.html','w');
					$md = str_replace('{body}',$md, $htmlSample);
					fwrite($ofn,$md);
					fclose($ofn);
				}else{
					copy ($dir.'/'.$file ,  $target.'/'.$file);
				}
			}
		}
	}
}
