# PHP QRCode Library

To install this library please follow the next steps:

## Install the library using `composer`:

Add the required module to your `composer.json` file:

    {
        "require": {
            ...
            "aferrandini/phpqrcode": "1.0.1"
            ...
        }
    }

Then run the command `composer update`.


## Usage

Sample code:

    \PHPQRCode\QRcode::png("Test", "/tmp/qrcode.png", 'L', 4, 2);

This code will generate a PNG file on '/tmp/qrcode.png' with a QRCode that contains the word 'Test'.

## Acknowledgements

This library is an import of PHP QR Code by Dominik Dzienia that you can find at http://phpqrcode.sourceforge.net

Based on C libqrencode library (ver. 3.1.1), Copyright (C) 2006-2010 by Kentaro Fukuchi
http://megaui.net/fukuchi/works/qrencode/index.en.html

QR Code is registered trademarks of DENSO WAVE INCORPORATED in JAPAN and other countries.

Reed-Solomon code encoder is written by Phil Karn, KA9Q. Copyright (C) 2002, 2003, 2004, 2006 Phil Karn, KA9Q

Data表示要记录的数据，如果是存储utf-8编码的中文，最多984个。
ECC表示纠错级别， 纠错级别越高，生成图片会越大。

L水平    7%的字码可被修正
M水平    15%的字码可被修正
Q水平    25%的字码可被修正
H水平    30%的字码可被修正
Size表示图片每个黑点的像素。
QRcode::png($data, $filename, $errorCorrectionLevel, $matrixPointSize, $margin);
/*
    $data 数据
    $filename 保存的图片名称
    $errorCorrectionLevel 错误处理级别
    $matrixPointSize 每个黑点的像素
    $margin 图片外围的白色边框像素
*/
第一个参数是二维码写入的数据，
第二个参数$outfile表示是否输出二维码图片文件
第三个参数H是ECC纠错级别，
第四个参数是每个黑点的像素，
第五个参数4是margin边缘空白的大小，
第六个参数false $saveandprint表示是否保存二维码并显示。