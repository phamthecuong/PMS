@extends('layouts.app')

@push('sidebar')
@include("libressltd.lbsidemenu.sidemenu")
@endpush

@push('css')
<style type="text/css">
    .note-error {
        color: #b94a48;
    }
</style>
@endpush
@push('script')
<script>
    localStorage.clear();
</script>
@endpush
