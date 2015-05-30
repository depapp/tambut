@extends('layouts.master')

@section('asset')
    @include('layouts.partials.datatable')
@stop

@section('title')
    {{ $title }}
@stop

@section('title-button')
    {{ HTML::buttonAdd() }}
@stop

@section('breadcrumb')
    <li><a href="/">Dashboard</a></li>
    <li class="uk-active">{{ $title }}</li>
@stop

@section('content')

    {{ Datatable::table()
    ->addColumn('id','Nama', '')       // these are the column headings to be shown
    ->setOptions('aoColumnDefs',array(
        array(
            'bVisible' => false,
            'aTargets' => [0]),
        array(
            'sTitle' => 'Nama',
            'aTargets' => [1]),
        array(
            'bSortable' => false,
            'aTargets' => [2])
        ))
    ->setOptions('bProcessing', true)
    ->setUrl(route('admin.authors.index'))   // this is the route where data will be retrieved
    ->render('datatable.uikit') }}

@stop