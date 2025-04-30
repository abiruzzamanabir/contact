<?php

namespace App\Imports;

use App\Models\Contact;
use App\Models\ContactTypes;
use Illuminate\Support\Str;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Row;

use Maatwebsite\Excel\Concerns\OnEachRow;


class ContactsImport implements
    OnEachRow,
    WithHeadingRow,
    WithBatchInserts,
    WithChunkReading,
    ShouldQueue

{
    /**
     * Remove unwanted columns like 'id' and return only allowed fields
     */
    public function map($row): array
    {
        unset($row['id']); // Ensure 'id' is removed to avoid insert errors

        return [
            'name'         => $row['name'] ?? null,
            'email'        => $row['email'] ?? null,
            'phone'        => $row['phone'] ?? null,
            'address'      => $row['address'] ?? null,
            'designation'  => $row['designation'] ?? null,
            'organization' => $row['organization'] ?? null,
            'types'        => $row['types'] ?? null,
        ];
    }

    /**
     * Create and return a Contact model
     */
    public function onRow(Row $row)
    {
        $row = $row->toArray();
        unset($row['id']); // Ensure 'id' is removed

        try {
            $contact = Contact::create([
                'name'         => $row['name'] ?? null,
                'email'        => $row['email'] ?? null,
                'phone'        => $row['phone'] ?? null,
                'address'      => $row['address'] ?? null,
                'designation'  => $row['designation'] ?? null,
                'organization' => $row['organization'] ?? null,
                'created_by'   => 'system',
                'status'       => true,
            ]);
        } catch (\Exception $e) {
            Log::error('Error importing contact', ['error' => $e->getMessage(), 'row' => $row]);
            return;
        }

        if (!empty($row['types'])) {
            $typeNames = array_map('trim', explode(',', $row['types']));
            $typeIds = [];

            foreach ($typeNames as $typeName) {
                $type = ContactTypes::firstOrCreate([
                    'name' => Str::title($typeName),
                ]);
                $typeIds[] = $type->id;
            }

            $contact->contactTypes()->sync($typeIds);
        }
    }


    /**
     * Set batch size for more efficient inserts
     */
    public function batchSize(): int
    {
        return 1000;
    }

    /**
     * Set chunk size for memory optimization
     */
    public function chunkSize(): int
    {
        return 1000;
    }
}
