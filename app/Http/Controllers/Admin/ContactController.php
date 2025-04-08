<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactTypes;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Display a listing of the contacts
    public function index(Request $request)
    {
        // Get the contact type name from the URL query string
        $type = $request->get('type');

        // Query for contacts
        $contacts = Contact::query();

        // If a contact type name is selected, filter the contacts by that contact type name
        if ($type) {
            $contacts = $contacts->whereHas('contactTypes', function ($query) use ($type) {
                $query->where('name', $type); // Filter by contact type name instead of ID
            });
        }

        // Optionally, handle search functionality if needed
        $search = $request->get('search');
        if ($search) {
            $contacts = $contacts->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->orWhere('designation', 'like', "%{$search}%")
                ->orWhere('organization', 'like', "%{$search}%")
                ->orWhere('address', 'like', "%{$search}%");
        }

        // Paginate the results
        $contacts = $contacts->paginate(10);  // Adjust pagination as needed

        $admin = $contacts;
        $contactTypes = ContactTypes::all();  // Fetch all contact types
        $roles = Role::orderBy("id", "asc")->get();

        return view('admin.pages.contact.index', [
            'all_admin' => $admin,
            'form_type'  => 'create',
            'roles'  => $roles,
            'contactTypes' => $contactTypes,
        ]);
    }


    // Store a newly created contact
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'designation' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'contact_type_id' => 'required|array',
            'contact_type_id.*' => 'exists:contact_types,id',
        ]);

        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'organization' => $request->organization,
            'address' => $request->address,
            'created_by' => Auth::guard('admin')->user()->fast_name,
        ]);

        // Attach selected contact types to pivot table
        $contact->contactTypes()->attach($request->contact_type_id);

        return redirect()->route('contact.index')->with('success', 'Contact added successfully!');
    }

    // Show the form for editing a contact
    public function edit($id)
    {
        // Fetch all contacts in alphabetical order (if necessary for listing)
        $admin = Contact::orderBy("name", "asc")->get();

        // Find the contact by id and eager load the contactTypes relationship
        $user = Contact::with('contactTypes')->findOrFail($id);

        // Fetch all contact types
        $contactTypes = ContactTypes::orderBy('name', 'asc')->get(); // fetch all types

        // Fetch any other necessary data (like roles if needed)
        $role = Role::orderBy("name", "asc")->get();

        // Pass the contact, contact types, and roles to the view
        return view('admin.pages.contact.index', [
            'all_admin' => $admin,
            'form_type' => 'edit',
            'edit' => $user,
            'roles' => $role,
            'contactTypes' => $contactTypes, // pass all types
        ]);
    }



    // Update a contact
    public function update(Request $request, $id)
    {
        // Validate the input fields
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'designation' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'contact_type_id' => 'required|array', // Ensure at least one contact type is selected
            'contact_type_id.*' => 'exists:contact_types,id', // Ensure selected types are valid
        ]);

        // Find the contact to update
        $update_date = Contact::findOrFail($id);

        // Update the contact fields
        $update_date->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'organization' => $request->organization,
            'address' => $request->address,
            'updated_by' => Auth::guard('admin')->user()->fast_name,
        ]);

        // Sync the selected contact types (this will overwrite the existing relationships)
        $update_date->contactTypes()->sync($request->contact_type_id);

        // Redirect back with success message
        return redirect()->route('contact.index')->with('success', 'Contact updated successfully!');
    }


    // Soft delete the specified contact
    public function destroy($id)
    {
        $delete_id = Contact::findOrFail($id);
        $delete_id->delete();
        // unlink('storage/admins/' . $delete_id->photo);
        return back()->with('success-main', 'Contact Deleted successfully');
    }
    public function updateStatus($id)
    {
        $data = Contact::findOrFail($id);


        if ($data->status) {
            $data->update([
                'status' => false,
            ]);
        } else {
            $data->update([
                'status' => true,
            ]);
        }
        return back()->with('success-main', 'Status updated successfully');
    }
    public function updateTrash($id)
    {
        $data = Contact::findOrFail($id);


        if ($data->trash) {
            $data->update([
                'status' => true,
                'trash' => false,
            ]);
        } else {
            $data->update([

                'status' => false,
                'trash' => true,
            ]);
        }
        return back()->with('success-main', 'Trash updated successfully');
    }

    public function trashUsers()
    {
        $admin = Contact::orderBy("name", "asc")->where('trash', true)->get();
        return view('admin.pages.contact.trash', [
            'all_admin' => $admin,
            'form_type'  => 'trash',
        ]);
    }
}
