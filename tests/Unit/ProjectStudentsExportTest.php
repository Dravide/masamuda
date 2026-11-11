<?php

use App\Exports\ProjectStudentsExport;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Style;

test('export binds cells as text and sets text format', function () {
    $export = new ProjectStudentsExport(1);

    expect($export->defaultStyles(new Style()))->toBe([
        'numberFormat' => [
            'formatCode' => NumberFormat::FORMAT_TEXT,
        ],
    ]);

    expect($export->columnFormats()['A'])->toBe(NumberFormat::FORMAT_TEXT)
        ->and($export->columnFormats()['C'])->toBe(NumberFormat::FORMAT_TEXT);

    $spreadsheet = new Spreadsheet();
    $cell = $spreadsheet->getActiveSheet()->getCell('A1');
    $export->bindValue($cell, '08123456789');

    expect($cell->getDataType())->toBe(DataType::TYPE_STRING)
        ->and($cell->getValue())->toBe('08123456789');
});
