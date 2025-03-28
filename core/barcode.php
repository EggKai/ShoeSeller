<?php //version 2.4
require_once __DIR__ . '/../vendor/autoload.php';

function generate_barcode($id)
{
    $generator = new Picqer\Barcode\BarcodeGeneratorHTML(); // Instantiate the BarcodeGeneratorHTML from version 2.4
    return $generator->getBarcode(str_pad($id, 11, '0', STR_PAD_LEFT), $generator::TYPE_CODE_128); // Generate the barcode using the TYPE_CODE_128 constant
}