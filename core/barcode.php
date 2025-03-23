<?php
require_once 'vendor/autoload.php';
function generate_barcode($id)
{
    // Make Barcode object of Code128 encoding.
    $barcode = (new Picqer\Barcode\Types\TypeCode128())->getBarcode(str_pad($id, 11, '0', STR_PAD_LEFT));
    $renderer = new Picqer\Barcode\Renderers\HtmlRenderer(); // Output the barcode as HTML in the browser with a HTML Renderer
    return $renderer->render($barcode);
}
