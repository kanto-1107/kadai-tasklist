<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Task;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         $date =[];
         
         if(\Auth::check()){
         
          $user = \Auth::user();
             
             $tasks = $user->tasks()->orderBy('created_at','desc')->paginate(10);
             
             $data =[
                 'tasks'=>$tasks,
                 ];
            return view('tasks.index', $data,[
                
                'task' => $tasks,
                ]);
         }
         return view('welcome', $data);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $task = new Task;
        
        return view('tasks.create',[
            'task' => $task,
            ]);
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
            'status' => 'required|max:10',
            'content'=>'required|max:255',
            ]);
            $request->user()->tasks()->create([
                'content' => $request->content,
                'status' => $request->status,
                ]);
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $task = Task::findOrFail($id);
        
        if($task->user->id != \Auth::user()->id){
            return redirect('/');
        }
        
        return view('tasks.show',[
            'task' =>$task, 
            ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $task = Task::findOrFail($id);
        
        if(\Auth::id() === $task->user_id){
            
        return view('tasks.edit',[
            'task' =>$task,
            ]);
        }
        return redirect('/');
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
        
        $this->validate($request,[
            'status' => 'required|max:10',
            'content'=> 'required|max:255'
            ]);
        
        
        $task = Task::findOrFail($id);
        $task->status = $request->status;
        $task->content = $request->content;
        $task->save();
        
        if(\Auth::id() === $task->user_id){
            $task->update();
        }
        
        return redirect('/');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $task = Task::findOrFail($id);
        
        if (\Auth::id() === $task->user_id) {
            $task->delete();
        }

        
        return redirect('/');
    }
}
