<?php

namespace App\Http\Controllers\Api;

use App\Models\Contact;
use App\Models\ContactTypes;
use App\Models\Role;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ContactController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type');
        $contacts = Contact::query();

        // Filter by contact type
        if ($type) {
            $contacts = $contacts->whereHas('contactTypes', function ($query) use ($type) {
                $query->where('name', $type);
            });
        }

        // Search contacts by various fields
        $search = $request->get('search');
        if ($search) {
            $contacts = $contacts->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('designation', 'like', "%{$search}%")
                    ->orWhere('organization', 'like', "%{$search}%")
                    ->orWhere('address', 'like', "%{$search}%");
            });
        }

        // Paginate the results
        $contacts = $contacts->where('trash', false)->paginate(10);

        // Get contact types and roles (optional, if needed for frontend)
        $contactTypes = ContactTypes::orderBy("name", "asc")->get();
        $roles = Role::orderBy("id", "asc")->get();

        return response()->json([
            'contacts' => $contacts->items(),
            'pagination' => [
                'current_page' => $contacts->currentPage(),
                'total_pages' => $contacts->lastPage(),
                'total_items' => $contacts->total(),
            ],
            'contactTypes' => $contactTypes,
            'roles' => $roles,
        ]);
    }
}
