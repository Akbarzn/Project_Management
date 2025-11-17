@extends('layouts.app')

@section('title', 'Edit Client')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-8">

    {{-- HEADER --}}
    <div class="bg-indigo-700 p-5 rounded-t-xl shadow-lg flex justify-between items-center">
        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
            <i class="fas fa-user-tag"></i>
            Edit Client
        </h2>

        <span class="text-white text-lg font-semibold">
            {{ $client->name }}
        </span>
    </div>

    {{-- CARD --}}
    <div class="bg-white shadow-xl rounded-b-xl border border-gray-200 p-6">

        {{-- ERROR MESSAGE --}}
        @if ($errors->any())
            <div class="mb-5 bg-red-50 border border-red-300 text-red-700 p-4 rounded-lg shadow-sm">
                <p class="font-bold mb-2">⚠️ Terjadi Kesalahan:</p>
                <ul class="list-disc list-inside text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- FORM --}}
        <form action="{{ route('manager.clients.update', $client->id) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            {{-- NAMA --}}
            <div>
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Client</label>
                <input type="text" name="name" id="name"
                       value="{{ old('name', $client->name) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror"
                       required>
            </div>

            <hr class="my-4 border-gray-200">

            {{-- NIK & PHONE --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                
                <div>
                    <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1">NIK</label>
                    <input type="text" name="nik" id="nik"
                           value="{{ old('nik', $client->nik) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('nik') border-red-500 @enderror">
                </div>

                <div>
                    <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                    <input type="text" name="phone" id="phone"
                           value="{{ old('phone', $client->phone) }}"
                           class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror">
                </div>

            </div>

            {{-- KODE ORGANISASI --}}
            <div>
                <label for="kode_organisasi" class="block text-sm font-semibold text-gray-700 mb-1">Kode Organisasi</label>
                <input type="text" name="kode_organisasi" id="kode_organisasi"
                       value="{{ old('kode_organisasi', $client->kode_organisasi) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('kode_organisasi') border-red-500 @enderror">
            </div>


            {{-- BUTTON --}}
            <div class="pt-5 flex justify-between items-center border-t border-gray-200">
                <a href="{{ route('manager.clients.index') }}"
                   class="px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg font-semibold hover:bg-gray-100 transition">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Batal
                </a>

                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-semibold shadow-md shadow-indigo-300/50 transition">
                    <i class="fas fa-save mr-1"></i>
                    Update Client
                </button>
            </div>

        </form>
    </div>

</div>
@endsection
