@extends('layouts.app')
@section('content')
<h2>Create Employee</h2>
@if($errors->any()) <div style="color:red">@foreach($errors->all() as $err) <div>{{ $err }}</div> @endforeach</div>@endif

<form action="{{ route('employees.store') }}" method="post" enctype="multipart/form-data">
  @csrf
  <div>Nomor: <input name="nomor" value="{{ old('nomor') }}"></div>
  <div>Nama: <input name="nama" value="{{ old('nama') }}"></div>
  <div>Jabatan: <input name="jabatan" value="{{ old('jabatan') }}"></div>
  <div>Tanggal Lahir: <input type="date" name="talahir" value="{{ old('talahir') }}"></div>
  <div>Photo: <input type="file" name="photo"></div>
  <button type="submit">Simpan</button>
</form>
@endsection
