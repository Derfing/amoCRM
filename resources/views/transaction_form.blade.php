@extends('layouts.base_layout')

@section('title', 'Create Transaction Form')

@section('body')
    <div>
        <form action="/createLead" method="post">
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
            <button type="submit">Создать заявку</button>
        </form>
    </div>
@endsection
