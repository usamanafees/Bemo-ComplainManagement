<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Artisan;
use Illuminate\Support\Facades\Storage;
use Log;
use File;


class TaskController extends Controller
{
    public function index()
    {
        $tasks = auth()->user()->statuses()->with('tasks')->get();

        return view('tasks.index', compact('tasks'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title' => ['required', 'string', 'max:56'],
            'description' => ['nullable', 'string'],
            'status_id' => ['required', 'exists:statuses,id']
        ]);

        return $request->user()
            ->tasks()
            ->create($request->only('title', 'description', 'status_id'));
    }

    public function sync(Request $request)
    {
        $this->validate(request(), [
            'columns' => ['required', 'array']
        ]);

        foreach ($request->columns as $status) {
            foreach ($status['tasks'] as $i => $task) {
                $order = $i + 1;
                if ($task['status_id'] !== $status['id'] || $task['order'] !== $order) {
                    request()->user()->tasks()
                        ->find($task['id'])
                        ->update(['status_id' => $status['id'], 'order' => $order]);
                }
            }
        }

        return $request->user()->statuses()->with('tasks')->get();
    }

    public function taskDetails(Request $request)
    {
       $task = Task::find($request->id);
       return response()->json($task);
    }

    public function dump_sql()
    {
          try {
            // dd($disk);
               /* only database backup*/
               Artisan::call('backup:clean');
	           Artisan::call('backup:run --only-db');
               /* all backup */
               /* Artisan::call('backup:run'); */
               $files = File::files(storage_path().'/app/Laravel');
          } catch (Exception $e) {
               session()->flash('danger', $e->getMessage());
               return redirect()->back();
          }
          $count= count($files);
          return Response::download($files[$count-1]);

    }

    public function edit(Task $task)
    {
        //
    }

    public function update(Request $request, Task $task)
    {
        //
    }

    public function destroy(Task $task)
    {
        //
    }
}
