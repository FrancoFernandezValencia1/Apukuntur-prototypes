<?php

namespace App\Support;

use Picqer\Barcode\BarcodeGeneratorSVG;

class ImagenCodigoBarras
{
    /** SVG Code 128 del código indicado. */
    public static function svg(string $codigo): string
    {
        $generator = new BarcodeGeneratorSVG();

        return $generator->getBarcode($codigo, $generator::TYPE_CODE_128, 2, 64);
    }

    /** Code 128 como data URI SVG listo para <img src="...">. */
    public static function dataUri(string $codigo): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(self::svg($codigo));
    }
}
