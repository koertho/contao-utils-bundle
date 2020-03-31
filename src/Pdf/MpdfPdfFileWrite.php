<?php

/*
 * Copyright (c) 2020 Heimrich & Hannot GmbH
 *
 * @license LGPL-3.0-or-later
 */

namespace HeimrichHannot\UtilsBundle\Pdf;

class MpdfPdfFileWrite
{
    /**
     * MpdfPdfFileWrite constructor.
     */
    public function __construct()
    {
        if (!class_exists('Mpdf\Mpdf')) {
            throw new \Exception('The mPDF library could not be found and is required by this service. Please install it via "composer require mpdf/mpdf ^7.0".');
        }
    }
}
