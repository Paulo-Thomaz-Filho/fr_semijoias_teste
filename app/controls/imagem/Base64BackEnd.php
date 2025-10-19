<?php 

declare(strict_types=1);


$imgData    = $_POST['imgData'] ?? '';
$imgMime    = $_POST['imgMime'] ?? '';

$tz 		= 'America/Sao_Paulo';
$timezone 	= new DateTimeZone($tz);
$now      	= new DateTimeImmutable('now', $timezone);
$filename 	=  "uploads/file_".$now->format('YmdHisv').".".(str_replace("/", ".", $imgMime)).".base64";
file_put_contents( $filename, $imgData );

$saida = file_get_contents($filename);

echo "Arquivo $filename criado com sucesso! \n". $saida ;


?>