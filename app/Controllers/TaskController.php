<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Models\Task;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::all();
        
        return $this->view('tasks/index', [
            'title' => 'Görev Listesi',
            'tasks' => $tasks
        ]);
    }

    public function store(Request $request)
    {
        $title = $request->input('title');
        
        if (!empty($title)) {
            Task::create([
                'title' => $title,
                'is_completed' => 0
            ]);
        }

        return redirect('/tasks');
    }

    public function update(Request $request, $id)
    {
        $task = Task::find($id);

        if ($task) {
            $status = $task['is_completed'] ? 0 : 1;
            Task::update($id, ['is_completed' => $status]);
        }

        return redirect('/tasks');
    }

    public function delete(Request $request, $id)
    {
        Task::delete($id);
        return redirect('/tasks');
    }
}
