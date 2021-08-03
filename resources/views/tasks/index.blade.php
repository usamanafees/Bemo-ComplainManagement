@extends('layouts.app')
<!-- Latest compiled and minified CSS -->

@section('content')
<div class="md:mx-4 relative overflow-hidden">
    <main class="h-full flex flex-col overflow-auto">
        <kanban-board :initial-data="{{ $tasks }}"></kanban-board>
    </main>
    <div class="flex items-center py-4">
        <a href="{{route('dump_sql')}} " class="flex-shrink-0 bg-teal-500 hover:bg-teal-700  text-sm border-4 text-white py-1 px-2 rounded">Download sql</a>
    </div>
</div>
@endsection
