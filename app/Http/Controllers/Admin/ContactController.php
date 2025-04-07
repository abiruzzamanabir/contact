<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContactController extends Controller
{
    // Display a listing of the contacts
    public function index()
    {
        $admin = Contact::orderBy("name", "asc")->where('trash', false)->get();
        $roles = Role::orderBy("id", "asc")->get();
        return view('admin.pages.contact.index', [
            'all_admin' => $admin,
            'form_type'  => 'create',
            'roles'  => $roles,
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
        ]);

        Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'organization' => $request->organization,
            'address' => $request->address,
            'created_by' => Auth::guard('admin')->user()->fast_name,
        ]);

        return redirect()->route('contact.index')->with('success', 'Contact added successfully!');
    }

    // Show the form for editing a contact
    public function edit($id)
    {
        $admin = Contact::orderBy("name", "asc")->get();
        $user = Contact::findOrFail($id);
        $role = Role::orderBy("name", "asc")->get();
        return view('admin.pages.contact.index', [
            'all_admin' => $admin,
            'form_type'  => 'edit',
            'edit'  => $user,
            'roles'  => $role,
        ]);
    }

    // Update a contact
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'designation' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
        ]);

        $update_date = Contact::findOrFail($id);

        $update_date->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'organization' => $request->organization,
            'address' => $request->address,
            'updated_by' => Auth::guard('admin')->user()->fast_name,
        ]);

        return back()->with('success', 'User updated successfully');

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
