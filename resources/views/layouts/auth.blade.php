@extends('layouts.auth.simple')

@section('title', $title ?? null)

@section('content')
    @yield('auth_content')
@endsection
