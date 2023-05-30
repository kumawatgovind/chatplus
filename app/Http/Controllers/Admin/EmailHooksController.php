<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Models\EmailHook;
use App\Http\Requests\EmailHookRequest;

/**
 * EmailHooks Controller
 *
 * @property App\Models\EmailHook; $EmailHooks
 *
 * @method App\Models\EmailHook;[] index(Request $request), create, store(EmailHookRequest $request), show(EmailHook $EmailHook), edit($id), update(EmailHookRequest $request, $id), destroy($id)
 */
class EmailHooksController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $emailHooks = EmailHook::all();
        return view('Admin.hooks.index', compact('emailHooks'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $emailHookLists = EmailHook::all();
        return view('Admin.hooks.createOrUpdate', compact('emailHookLists'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(EmailHookRequest $request)
    {

        try {
            EmailHook::create($request->all());
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.hooks.index')->with('success', 'Email Hook created successfully');
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $email_hook = EmailHook::find($id);
        return view('Admin.hooks.show', compact('email_hook'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $emailHook = EmailHook::find($id);
        $emailHookLists = EmailHook::all();
        return view('Admin.hooks.createOrUpdate', compact('emailHook', 'emailHookLists'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(EmailHookRequest $request, $id)
    {
        try {
            $emailHook = EmailHook::find($id);
            $emailHook->title = $request->get('title');
            $emailHook->description = $request->get('description');
            $emailHook->status = $request->get('status');
            $emailHook->save();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.hooks.index')->with('success', 'Email hook has been updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        $emailHook = EmailHook::find($id);
        $emailHook->delete();
        return redirect()->route('admin.hooks.index')->with('success', 'Email hook has been deleted Successfully');
    }
}
