<?php

namespace App\Exports;

use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Exception;

trait SheetStyle
{
    /**
     * @param AfterSheet $event
     * @param string $coordinate
     * @param string $type
     * @param string $style
     * @throws Exception
     */
    private function setBorderStyle(AfterSheet $event, string $coordinate, string $type, string $style)
    {
        $event->getSheet()
            ->getDelegate()
            ->getStyle($coordinate)
            ->applyFromArray([
                'borders' => [
                    $type => [
                        'borderStyle' => $style,
                        'color' => ['rgb' => '000000'],
                    ],
                ]
            ]);
    }

    /**
     * @param AfterSheet $event
     * @param string $coordinate
     * @param string $color
     * @throws Exception
     */
    private function setFontColor(AfterSheet $event, string $coordinate, string $color)
    {
        $event->getSheet()
            ->getDelegate()
            ->getStyle($coordinate)
            ->applyFromArray([
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => $color],
                ]
            ]);
    }
}