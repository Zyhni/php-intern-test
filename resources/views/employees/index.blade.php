@extends('layouts.app')

@section('content')
<h2>Employees</h2>
<a href="{{ route('employees.create') }}">Create</a>
@if(session('success')) <div style="color:green">{{ session('success') }}</div> @endif

<table border="1" cellpadding="6">
<tr><th>Nomor</th><th>Nama</th><th>Jabatan</th><th>Foto</th><th>Aksi</th></tr>
@foreach($employees as $e)
<tr>
  <td>{{ $e->nomor }}</td>
  <td>{{ $e->nama }}</td>
  <td>{{ $e->jabatan }}</td>
  <td>
    @if($e->photo_upload_path)
      <img src="{{ $e->photo_upload_path }}" width="80" alt="foto">
    @endif
  </td>
  <td>
    <a href="{{ route('employees.edit', $e->id) }}">Edit</a>
    <form action="{{ route('employees.destroy', $e->id) }}" method="POST" style="display:inline">
      @csrf @method('DELETE')
      <button onclick="return confirm('Hapus?')">Delete</button>
    </form>
  </td>
</tr>
@endforeach
</table>
@endsection
