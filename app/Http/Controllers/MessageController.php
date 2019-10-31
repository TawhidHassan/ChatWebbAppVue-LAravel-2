<?php

namespace App\Http\Controllers;

use App\Events\MessageSend;
use App\Message;
use App\User;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }


    //get user
    public function users()
    {
     $user=User::latest()
         ->where('id','!=',auth()->user()->id)
         ->get();

     if (\Request::ajax())
     {
         return response()->json($user,200);
     }
     return abort(404);

    }

    //send massage
    public function sendmassage(Request $request)
    {
        if(!$request->ajax()){
            abort(404);
        }
        $messages = Message::create([
            'message'=>$request->message,
            'from'=>auth()->user()->id,
            'to'=>$request->user_id,
            'type'=>0
        ]);
        $messages = Message::create([
            'message'=>$request->message,
            'from'=>auth()->user()->id,
            'to'=>$request->user_id,
            'type'=>1
        ]);

        broadcast(new MessageSend($messages));
        return response()->json($messages,201);
    }
    //get user massage
    public function usersmassage($userid)
    {
        $user=User::find($userid);
     $massage=Message::where(function ($q) use ($userid){
         $q->where('from',auth()->user()->id);
         $q->where('to',$userid);
         $q->where('type',0);
         })
         ->orWhere(function ($q) use ($userid){
             $q->where('from',$userid);
             $q->where('to',auth()->user()->id);
             $q->where('type',1);
         })
         ->with('user')->get();

     if (\Request::ajax())//kaw jata direct route a hit kora data na pai tar jonno
     {
         return response()->json([
             'user'=>$user,
             'massage'=>$massage,
         ],200);
     }
     return abort(404);

    }

    public function delete_single_message($id=null){
        if(!\Request::ajax()){
            return  abort(404);
        }
        Message::findOrFail($id)->delete();
        return response()->json('deleted',200);
    }

    //delete all massages
    public function message_by_user_id($id){
        $messages = Message::where(function($q) use($id){
            $q->where('from',auth()->user()->id);
            $q->where('to',$id);
            $q->where('type',0);
        })->orWhere(function($q) use ($id){
            $q->where('from',$id);
            $q->where('to',auth()->user()->id);
            $q->where('type',1);
        })->with('user')->get();
        return $messages;
    }
    public function delete_all_message($id=null){
        $messages =  $this->message_by_user_id($id);
        foreach ($messages as $value) {
            Message::findOrFail($value->id)->delete();
        }
        return response()->json('all deleted',200);
    }



    public function index()
    {

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function show(Message $message)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function edit(Message $message)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Message $message)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Message  $message
     * @return \Illuminate\Http\Response
     */
    public function destroy(Message $message)
    {
        //
    }
}
