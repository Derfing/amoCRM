@extends('layouts.base_layout')

@section('title', 'Generate Leads File')

@section('body')
    <div>
        <form action="/downloadLeadsFile" method="get">
            @csrf
            <button type="submit">Скачать файл</button>
        </form>
    </div>
@endsection
