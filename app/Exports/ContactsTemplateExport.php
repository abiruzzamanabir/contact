<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsTemplateExport implements FromArray, WithHeadings
{
    public function array(): array
    {
        return [
            // example row (can be left empty or provide one dummy row)
            [
                'John Doe',
                'john@example.com',
                '123456789',
                'NY',
                'Manager',
                'ABC Ltd',
                'Client,Lead'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'email',
            'phone',
            'address',
            'designation',
            'organization',
            'types',
        ];
    }
}
