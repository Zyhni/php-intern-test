@extends('layouts.app')
@section('content')
<h2>Edit Employee</h2>
<form action="{{ route('employees.update', $employee->id) }}" method="post" enctype="multipart/form-data">
  @csrf @method('PUT')
  <div>Nomor: <input name="nomor" value="{{ $employee->nomor }}" readonly></div>
  <div>Nama: <input name="nama" value="{{ $employee->nama }}"></div>
  <div>Jabatan: <input name="jabatan" value="{{ $employee->jabatan }}"></div>
  <div>Tanggal Lahir: <input type="date" name="talahir" value="{{ $employee->talahir?->format('Y-m-d') }}"></div>
  <div>Photo: <input type="file" name="photo"></div>
  @if($employee->photo_upload_path)
    <div><img src="{{ $employee->photo_upload_path }}" width="120"></div>
  @endif
  <button type="submit">Update</button>
</form>
@endsection
