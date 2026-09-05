@extends('layouts.app')

@section('title', 'Корзина | Dutch Seeds')

@section('seo')

    <meta name="description" content="Корзина">
    <meta name="keywords" content="Корзина Dutch Seeds">
    {{--<link rel="canonical" href="{{ url('/news') }}">
--}}
@endsection



@section('content')



    <livewire:cart-page />

@endsection
