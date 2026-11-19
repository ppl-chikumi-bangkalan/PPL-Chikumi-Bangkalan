<?php

namespace App\Support;

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Throwable;

class QrCode
{
    /**
     * Render QR string (dari Duitku) jadi SVG mentah.
     */
    public static function svg(?string $isi, int $ukuran = 440): ?string
    {
        if (! $isi) {
            return null;
        }

        try {
            $renderer = new ImageRenderer(new RendererStyle($ukuran, 2), new SvgImageBackEnd());

            return (new Writer($renderer))->writeString($isi);
        } catch (Throwable $e) {
            report($e);

            return null;
        }
    }
}
