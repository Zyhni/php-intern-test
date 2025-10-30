@extends('layouts.app')
@section('content')
<h2>Employee {{ $emp->nama }}</h2>
<p>Nomor: {{ $emp->nomor }}</p>
<p>Jabatan: {{ $emp->jabatan }}</p>
@if($emp->photo_upload_path) <img src="{{ $emp->photo_upload_path }}" width="200"> @endif
@endsection
