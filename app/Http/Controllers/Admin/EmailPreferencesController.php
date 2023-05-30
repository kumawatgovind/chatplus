<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\EmailPreference;
use App\Http\Requests\EmailPreferenceRequest;

/**
 * EmailPreference Controller
 *
 * @property App\Models\EmailPreference; $EmailPreference
 *
 * @method App\Models\EmailPreference;[], index(Request $request), create, store(EmailPreferenceRequest $request), show(EmailPreference $EmailPreference), edit($id), update(EmailPreferenceRequest $request, $id), destroy($id)
 */
class EmailPreferencesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $emailPreferences = EmailPreference::all();
        return view('Admin.preferences.index', compact('emailPreferences'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('Admin.preferences.createOrUpdate');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(EmailPreferenceRequest $request)
    {
        try {
            EmailPreference::create($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.email-preferences.index')->with('success', 'Email Layout has been saved Successfully');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show(EmailPreference $emailPreference)
    {
        return view('Admin.preferences.show', compact('emailPreference'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $emailPreference = EmailPreference::find($id);
        return view('Admin.preferences.createOrUpdate', compact('emailPreference'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(EmailPreferenceRequest $request, $id)
    {
        try {
            $emailPreference = EmailPreference::find($id);
            $emailPreference->title = $request->get('title');
            $emailPreference->layout_html = $request->get('layout_html');
            $emailPreference->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.email-preferences.index')->with('success', 'Email Layout has been updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
}
