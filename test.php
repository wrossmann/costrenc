<?php
require('vendor/autoload.php');
use wrossmann\Costrenc\Costrenc;

class UTF16LEtoUTF8 extends Costrenc {
	protected $in_charset = 'UTF-16LE';
	protected $out_charset = 'UTF-8';
}

function fake_file_handle($content) {
	$fh = fopen('php://memory', 'rwb+');
	fwrite($fh, $content);
	rewind($fh);
	return $fh;
}

function test($data) {
	$fh = fake_file_handle($data);
	stream_filter_register('UTF16LEtoUTF8', 'UTF16LEtoUTF8');
	stream_filter_append($fh, 'UTF16LEtoUTF8', STREAM_FILTER_READ);

	while( $line = fgetcsv($fh) ) {
		var_dump($line);
	}
}

$test_data = <<<_E_
a,b,c,d
e,f,g,h
i,j,k,l

_E_;

$good = mb_convert_encoding($test_data, 'UTF-16LE', 'UTF-8');
$bad = $good . 'x'; // trailing single byte

test($good);
echo "===\n";
test($bad);
