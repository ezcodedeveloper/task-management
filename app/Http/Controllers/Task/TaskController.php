<?php

namespace App\Http\Controllers\Task;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Tasks\Tasks;
use Illuminate\Support\Facades\Auth;
class TaskController extends Controller
{
    public function list(){
         return Inertia::render('tasks/TaskList');
    }
    public function store(Request $request){
        $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'due_date' => 'nullable|date',
        'priority' => 'required|in:Low,Medium,High', // Adjust values as needed
        'status' => 'required|in:Pending,In Progress,Completed',
    ]);
   
     $taskAttr = [
            'title'=>$request->title,
            'description'=>$request->description,
            'due_date'=>$request->due_date,
            'priority'=>$request->priority,
            'status'=>$request->status,
            'userId'=>Auth::id()
        ];
    Tasks::create($taskAttr);
    return response()->json(['message' => 'Task has beem created successfully!'],200);
    }

    public function taskDataTable(Request $request){
   $query = Tasks::query();

    // 🔒 Filter by logged-in user
    $query->where('userId', Auth::id());

    // 🔍 Search filter
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // ✅ Status filter
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 📦 Paginate results
    $perPage = $request->get('per_page', 10); // Optional: frontend can send per_page
    $tasks = $query->latest()->paginate($perPage);

    return response()->json($tasks);
    }

    public function delete(Request $request)
    {
        $task = Tasks::find($request->id);

        if ($task) {
            $task->delete();
            return response()->json(['message' => 'Task has been deleted successfully!'], 200);
        }

        // Explicitly return 404 Not Found
        return response()->json(['message' => 'Task not found!'], 404);
    }
    public function update(Request $request){
        $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'due_date' => 'nullable|date',
                'priority' => 'required|in:Low,Medium,High', // Adjust values as needed
                'status' => 'required|in:Pending,In Progress,Completed',
    ]);
    $task = Tasks::find($request->id);
    if($task){
        $taskAttr = [
            'title'=>$request->title,
            'description'=>$request->description,
            'due_date'=>$request->due_date,
            'priority'=>$request->priority,
            'status'=>$request->status,
        ];
     $task->update($taskAttr);
      return response()->json(['message' => 'Task has been updated successfully!'], 200);
    }
     return response()->json(['message' => 'Task not found!'], 404);
    }
}
