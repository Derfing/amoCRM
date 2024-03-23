@extends('layouts.base_layout')

@section('title', 'Import Transaction From File')

@section('body')
    <div>
        <form action="/importLeadsFile" method="post" enctype="multipart/form-data">
            <label for="json_data">Файл JSON</label>
            <input type="file" id="json_data" name="json_data">
            @csrf
            <button type="submit">Импортировать данные</button>
        </form>
    </div>
@endsection
