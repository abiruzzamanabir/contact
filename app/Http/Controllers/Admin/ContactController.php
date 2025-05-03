<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Contact;
use App\Models\ActivityLog;
use App\Models\ContactTypes;
use Illuminate\Http\Request;
use App\Imports\ContactsImport;
use OpenAI\Laravel\Facades\OpenAI;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Jobs\SendImportCompletedEmail;
use App\Exports\ContactsTemplateExport;

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

        $contacts = $contacts->where('trash', false)->paginate(10);
        $contactTypes = ContactTypes::orderBy("name", "asc")->get();
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
            'email' => 'required|email|max:255|unique:contacts,email',
            'phone' => 'required|string|max:20',
            'designation' => 'nullable|string|max:255',
            'organization' => 'nullable|string|max:255',
            'contact_type_id' => 'required|array',
            'contact_type_id.*' => 'exists:contact_types,id',
        ]);

        $user = Auth::guard('admin')->user();

        $contact = Contact::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'designation' => $request->designation,
            'organization' => $request->organization,
            'address' => $request->address,
            'created_by' => $user->fast_name . ' ' . $user->last_name,
        ]);

        $contact->contactTypes()->attach($request->contact_type_id);

        return response()->json([
            'success' => true,
            'message' => 'Contact added successfully!',
            'data' => [
                'id' => $contact->id,
                'name' => $contact->name,
                'email' => $contact->email,
                'phone' => $contact->phone,
                'designation' => $contact->designation,
                'organization' => $contact->organization,
                'address' => $contact->address,
                'created_by' => $contact->created_by,
                'updated_by' => $contact->updated_by,
                'created_at_human' => $contact->created_at->diffForHumans(),
                'types' => $contact->contactTypes()->pluck('name')->toArray(),
            ]
        ]);
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
            'updated_by' => Auth::guard('admin')->user()->fast_name . ' ' . Auth::guard('admin')->user()->last_name,
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

    public function downloadTemplate()
    {
        return Excel::download(new ContactsTemplateExport, 'contacts_import_template.xlsx');
    }
    public function showImportForm()
    {
        return view('admin.pages.contact.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ]);

        Excel::queueImport(new ContactsImport, $request->file('file'));

        // Prepare data for the email
        $user = auth('admin')->user();
        $ip = $request->ip();
        $time = now();

        // Dispatch the job with necessary data
        SendImportCompletedEmail::dispatch($user, $ip, $time);

        return back()->with('success', 'Contacts import started in the background. You will be notified when it finishes.');
    }
}
