<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Gate;
use Mail;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $response = Gate::inspect('check-user', "enquries-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $contacts = Contact::sortable(['created_at' => 'desc'])->paginate(8);
        return view('Admin.contacts.index', compact('contacts'));
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function show(Contact $contact)
    {
        $response = Gate::inspect('check-user', "enquries-index");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        return view('Admin.contacts.show', compact('contact'));
    }

    public function deleteContact($id)
    {
        $response = Gate::inspect('check-user', "enquries-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        try {
            $deleteContact = Contact::where('id', $id)->first();
            $deleteContact->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        $responce = ['status' => true, 'message' => 'Enquiry has been deleted successfully.', 'data' => ['id' => $id]];
        return $responce;
    }

    public function contactReply(Request $request, $id)
    {
        $response = Gate::inspect('check-user', "enquries-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $validated = $request->validate([
            'reply_text' => 'required',
        ]);
        try {
            $params = $request->all();
            $enquiry = Contact::find($id);
            $enquiry->fill($request->all());
            $enquiry->save();
            //send mail to user
            $replacement['name'] = $enquiry->name;
            $replacement['enquiry'] = $enquiry->message;
            $replacement['reply_text'] = $params['reply_text'];
            $data = ['template' => 'reply-enquiry-email', 'hooksVars' => $replacement];
            Mail::to($enquiry->email)->send(new \App\Mail\ManuMailer($data));
        } catch (\Illuminate\Database\QueryException $e) {
            return back()->withError($e->getMessage())->withInput();
        }
        return redirect()->route('admin.contacts.index')->with('success', 'You have successfully replied to user over this enquiry.');
    }

    public function contactEdit($id)
    {
        $response = Gate::inspect('check-user', "enquries-create");
        if (!$response->allowed()) {
            return redirect()->route('admin.dashboard', app('request')->query())->with('error', $response->message());
        }
        $enquiry = Contact::find($id);
        return view('Admin.contacts.contact_reply', compact('enquiry'));
    }
}
