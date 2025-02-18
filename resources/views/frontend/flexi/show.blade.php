@extends('frontend.layouts.public')

@section('title', 'Flexi User')

@section('content')
    <section class="wrapper bg-soft-primary mb-5">
        <div class="container pt-10 pb-6 pt-md-7 pb-md-10 text-center">
            @livewire('frontend.flexi')
        </div>
    </section>
@endsection