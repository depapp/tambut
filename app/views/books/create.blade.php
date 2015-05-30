@extends('layouts.master')

@section('title')
    {{ $title }}
@stop

@section('asset')
    @include('layouts.partials.select2')
@stop

@section('breadcrumb')
    <li><a href="/">Dashboard</a></li>
    <li><a href="{{ route('admin.books.index') }}">Buku</a></li>
    <li class="uk-active">{{ $title }}</li>
@stop

@section('content')
    <!-- Navigasi dengan tab untuk memilih cara input buku -->
    <ul class="uk-tab" data-uk-tab="{connect:'#tambahbuku'}">
        <li class="uk-active"><a href="#"><i class="uk-icon-pencil-square-o"></i> Isi Form</a></li>
        <li ><a href="#"><i class="uk-icon-cloud-upload"></i> Upload Excel</a></li>
    </ul>

    <!-- Container dari isian tab -->
    <ul id="tambahbuku" class="uk-switcher uk-margin">
        <li>
            {{ Form::open(array('url' => route('admin.books.store'), 'method' => 'post', 'files'=>'true', 'class'=>'uk-form uk-form-horizontal')) }}
                @include('books._form')
            {{ Form::close() }}
        </li>
        <li>
            {{ Form::open(array('url' => route('admin.books.import'), 'method' => 'post', 'files'=>'true', 'class'=>'uk-form uk-form-horizontal')) }}
                @include('books._import')
            {{ Form::close() }}
        </li>
    </ul>
@stop