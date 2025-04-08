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
        $this->validate($request, [
            'name' => 'required|unique:contact_types'
        ]);

        ContactTypes::create([
            'name' => Str::ucfirst($request->name),
        ]);
        return back()->with('success', 'permission added successfully');
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
        $update_data = ContactTypes::findOrFail($id);
        $update_data->update([
            'name' => Str::ucfirst($request->name),
        ]);
        return back()->with('success', 'permission updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $delete = ContactTypes::findOrFail($id);
        $delete->delete();
        return back()->with('success-main', 'permission removed successfully');
    }
}
