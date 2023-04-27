<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function showAllTasks()
    {
        return response()->json(Task::all());
    }

    public function showOneTask($id)
    {
        $task = Task::find($id);

        if ($task === null) {
            return response()->json(['error' => 'Task not found!'], 404);
        }
        return response()->json(Task::find($id));
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'title' => 'required|max:50',
            'category' => 'required|max:20',
            'description' => 'required|max:255',
            'user_id' => 'required',
        ]);

        $task = Task::create($request->all());
        return response()->json($task, 201);
    }

    public function update($id, Request $request)
    {
        $this->validate($request, [
            'title' => 'max:50',
            'category' => 'max:20',
            'description' => 'max:255',
            'user_id' => 'required',
        ]);

        $task = Task::findOrFail($id);
        $task->update($request->all());

        return response()->json($task, 200);
    }

    public function delete($id)
    {
        Task::findOrFail($id)->delete();
        return response()->json(['message' => 'Delete succesfully'], 200);
    }
}
