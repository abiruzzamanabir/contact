<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Contact;
use App\Models\ContactTypes;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use OpenAI\Laravel\Facades\OpenAI;


class ContactController extends Controller
{

    public function generateContactSummary($contact)
    {
        $prompt = "Summarize the following contact info in 2-3 short, clear sentences:\n" .
            "Name: {$contact->name}\n" .
            "Designation: {$contact->designation}\n" .
            "Organization: {$contact->organization}\n" .
            "Email: {$contact->email}\n" .
            "Phone: {$contact->phone}\n" .
            "Address: {$contact->address}";

        $openai = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [['role' => 'user', 'content' => $prompt]],
        ]);

        return $result->choices[0]->message->content ?? '';
    }


    public function index(Request $request)
    {
        $type = $request->get('type');
        $contacts = Contact::query();

        if ($type) {
            $contacts = $contacts->whereHas('contactTypes', function ($query) use ($type) {
                $query->where('name', $type);
            });
        }

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

        $contacts = $contacts->paginate(10);
        $contactTypes = ContactTypes::all();
        $roles = Role::orderBy("id", "asc")->get();

        return view('admin.pages.contact.index', [
            'all_admin' => $contacts,
            'form_type' => 'create',
            'roles' => $roles,
            'contactTypes' => $contactTypes,
        ]);
    }

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

        $contact->contactTypes()->attach($request->contact_type_id);

        return redirect()->route('contact.index')->with('success', 'Contact added successfully!');
    }

    public function edit($id)
    {
        $admin = Contact::orderBy("name", "asc")->get();
        $user = Contact::with('contactTypes')->findOrFail($id);
        $contactTypes = ContactTypes::orderBy('name', 'asc')->get();
        $role = Role::orderBy("name", "asc")->get();

        return view('admin.pages.contact.index', [
            'all_admin' => $admin,
            'form_type' => 'edit',
            'edit' => $user,
            'roles' => $role,
            'contactTypes' => $contactTypes,
        ]);
    }

    public function update(Request $request, $id)
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

        $contact = Contact::findOrFail($id);
        $originalData = $contact->toArray();
        $originalContactTypes = $contact->contactTypes->pluck('id')->toArray();

        $contact->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'organization' => $request->organization,
            'address' => $request->address,
            'updated_by' => Auth::guard('admin')->user()->fast_name,
        ]);

        $contact->contactTypes()->sync($request->contact_type_id);
        $updatedContactTypes = $contact->contactTypes->pluck('id')->toArray();

        $updatedData = $contact->fresh()->toArray();
        $changes = [];

        foreach ($updatedData as $key => $newValue) {
            if (isset($originalData[$key]) && $originalData[$key] != $newValue) {
                $changes[$key] = [
                    'old' => $originalData[$key],
                    'new' => $newValue,
                ];
            }
        }

        if ($originalContactTypes !== $updatedContactTypes) {
            $changes['contact_types'] = [
                'old' => $originalContactTypes,
                'new' => $updatedContactTypes,
            ];
        }

        if (!empty($changes)) {
            ActivityLog::create([
                'action' => 'update',
                'model_type' => 'Contact',
                'model_id' => $contact->id,
                'changed_data' => json_encode($changes),
                'performed_by' => Auth::guard('admin')->user()->fast_name,
                'performed_at' => now(),
            ]);
        }

        return redirect()->route('contact.index')->with('success', 'Contact updated successfully!');
    }

    public function destroy($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return back()->with('success-main', 'Contact Deleted successfully');
    }

    public function updateStatus($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update(['status' => !$contact->status]);

        return back()->with('success-main', 'Status updated successfully');
    }

    public function updateTrash($id)
    {
        $contact = Contact::findOrFail($id);
        $contact->update([
            'status' => !$contact->trash,
            'trash' => !$contact->trash,
        ]);

        return back()->with('success-main', 'Trash updated successfully');
    }

    public function trashUsers()
    {
        $admin = Contact::orderBy("name", "asc")->where('trash', true)->get();
        return view('admin.pages.contact.trash', [
            'all_admin' => $admin,
            'form_type' => 'trash',
        ]);
    }

    public function logs($id)
    {
        $contact = Contact::findOrFail($id);
        $logs = ActivityLog::where('model_type', 'Contact')->where('model_id', $id)->latest()->get();

        return view('admin.pages.contact.logs', compact('contact', 'logs'));
    }

    public function printContact($id)
    {
        $contact = Contact::with('contactTypes')->findOrFail($id);
        // $summary = $this->generateContactSummary($contact);

        // return view('admin.pages.contact.print', compact('contact', 'summary'));
        return view('admin.pages.contact.print', compact('contact'));
    }
}
