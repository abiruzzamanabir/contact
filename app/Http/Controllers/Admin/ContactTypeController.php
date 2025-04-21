<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use App\Models\ContactTypes;
use Illuminate\Http\Request;

class ContactTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $Contacttypes = ContactTypes::orderBy("name", "asc")->get();
        return view('admin.pages.contacttype.index', [
            'all_contacttypes' => $Contacttypes,
            'form_type'  => 'create',
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Check for duplicate
        $existingType = ContactTypes::where('name', $request->name)->first();
        if ($existingType) {
            return response()->json([
                'success' => false,
                'message' => 'This contact type already exists.'
            ], 409); // 409 Conflict
        }

        // Create new contact type
        $type = ContactTypes::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'id' => $type->id,
            'name' => $type->name,
            'created_at_human' => $type->created_at->diffForHumans(), // 👈 important
            'message' => 'Contact type added successfully.'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $contact = ContactTypes::orderBy("name", "asc")->get();
        $per = ContactTypes::findOrFail($id);
        return view('admin.pages.contacttype.index', [
            'all_contacttypes' => $contact,
            'form_type'  => 'edit',
            'edit'  => $per,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $type = ContactTypes::findOrFail($id);
        $type->update([
            'name' => $request->name,
        ]);

        return response()->json(['success' => true]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $type = ContactTypes::findOrFail($id);
        $type->delete();

        return response()->json(['success' => true]);
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        // Check for duplicate
        $existingType = ContactTypes::where('name', $request->name)->first();
        if ($existingType) {
            return response()->json([
                'success' => false,
                'message' => 'This contact type already exists.'
            ], 409); // 409 Conflict
        }

        // Create new contact type
        $type = ContactTypes::create([
            'name' => $request->name
        ]);

        return response()->json([
            'success' => true,
            'id' => $type->id,
            'name' => $type->name,
            'message' => 'Contact type added successfully.'
        ]);
    }
}
