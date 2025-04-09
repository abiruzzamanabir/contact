<?php

namespace App\Exports;

use App\Models\Contact;
use App\Models\ContactTypes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ContactsExport implements FromCollection, WithHeadings
{
    protected $contactTypeId;
    protected $searchQuery;

    // Constructor to accept contact type ID and search query
    public function __construct($contactTypeId = null, $searchQuery = null)
    {
        $this->contactTypeId = $contactTypeId;
        $this->searchQuery = $searchQuery;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $query = Contact::query();

        // If a contact type ID is provided, filter the contacts by that type
        if ($this->contactTypeId) {
            $query->whereHas('contactTypes', function ($query) {
                $query->where('contact_type_id', $this->contactTypeId);
            });
        }

        // If a search query is provided, filter contacts based on it
        if ($this->searchQuery) {
            $query->where('name', 'like', "%{$this->searchQuery}%")
                ->orWhere('email', 'like', "%{$this->searchQuery}%")
                ->orWhere('phone', 'like', "%{$this->searchQuery}%")
                ->orWhere('designation', 'like', "%{$this->searchQuery}%")
                ->orWhere('organization', 'like', "%{$this->searchQuery}%");
        }

        // Select only the specific columns needed for export
        return $query->get(['name', 'email', 'phone', 'designation', 'organization', 'address']);
    }

    /**
     * Define the headings for the export file.
     */
    public function headings(): array
    {
        return [
            'Name',
            'Email',
            'Phone',
            'Designation',
            'Organization',
            'Address',
        ];
    }

    /**
     * Customize the file name to include the contact type name.
     */
    public function fileName()
    {
        // Check if a contact type ID is provided
        if ($this->contactTypeId) {
            // Fetch the contact type name from the contact_types table
            $contactType = ContactTypes::find($this->contactTypeId);

            // If the contact type exists, use its name; otherwise, fallback to 'all'
            $typeName = $contactType ? $contactType->name : 'all';
        } else {
            // If no contact type ID, use 'all'
            $typeName = 'all';
        }

        // Convert the name to uppercase and replace spaces with underscores
        $typeName = strtoupper(str_replace(' ', '_', $typeName));

        return 'contacts_type_' . $typeName . '.xlsx';
    }
}
