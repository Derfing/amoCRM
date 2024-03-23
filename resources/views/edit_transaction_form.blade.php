@extends('layouts.base_layout')

@section('title', 'Edit Transaction Form')

@section('body')
    <div>
        <form action="/editLead" method="post">
            <div>
                <label for="transaction_id">ID сделки</label>
                <input type="number" id="transaction_id" name="transaction_id">
            </div>
            <div>
                <label for="transaction_name">Название сделки</label>
                <input type="text" id="transaction_name" name="transaction_name">
            </div>
            <div>
                <label for="budget">Бюджет</label>
                <input type="number" id="budget" name="budget">
            </div>
            <div>
                <label for="cost">Себестоимость</label>
                <input type="number" id="cost" name="cost">
            </div>
            @csrf
            <button type="submit">Изменить сделку</button>
        </form>
    </div>
@endsection
