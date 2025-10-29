@extends('layouts.manager')

@section('title', 'Edit Client')

@section('content')

<div class="container mx-auto px-4 py-4">
    <div class="max-w-xl mx-auto bg-white rounded-xl shadow-2xl border border-gray-100 overflow-hidden">
        
        <div class="bg-indigo-700 py-3 px-6 flex items-center justify-between">
            <h2 class="text-2xl font-semibold text-white">
                <i class="fas fa-user-tag mr-3"></i> 
                Ubah Data Client
            </h2>
            <span class="text-white text-lg font-medium">{{ $client->name }}</span>
        </div>

        <div class="p-4"> 
            @if ($errors->any())
                <div class="mb-5 bg-red-50 border-l-4 border-red-500 text-red-700 p-4 rounded-lg shadow-sm">
                    <p class="font-bold">⚠️ Gagal menyimpan. Silakan periksa:</p>
                    <ul class="mt-2 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('manager.clients.update', $client->id) }}" method="POST" class="space-y-5">
                @csrf
                @method('PUT')
                
                {{-- Nama Client --}}
                <div>
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1">Nama Client</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $client->name) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('name') border-red-500 @enderror" 
                        required />
                </div>

                <hr class="border-gray-200 ">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- NIK --}}
                    <div>
                        <label for="nik" class="block text-sm font-semibold text-gray-700 mb-1">NIK</label>
                        <input type="text" name="nik" id="nik" value="{{ old('nik', $client->nik) }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('nik') border-red-500 @enderror" />
                    </div>
                    
                    {{-- Telepon --}}
                    <div>
                        <label for="phone" class="block text-sm font-semibold text-gray-700 mb-1">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $client->phone) }}" 
                            class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('phone') border-red-500 @enderror" />
                    </div>
                </div>

                {{-- Kode Organisasi --}}
                <div>
                    <label for="kode_organisasi" class="block text-sm font-semibold text-gray-700 mb-1">Kode Organisasi</label>
                    <input type="text" name="kode_organisasi" id="kode_organisasi" value="{{ old('kode_organisasi', $client->kode_organisasi) }}" 
                        class="w-full border border-gray-300 rounded-lg px-4 py-2.5 focus:ring-indigo-500 focus:border-indigo-500 @error('kode_organisasi') border-red-500 @enderror" />
                </div>

                <div class="pt-6 flex justify-between items-center">
                    <a href="{{ route('manager.clients.index') }}"
                       class="px-5 py-2.5 text-gray-700 border border-gray-300 rounded-lg font-semibold hover:bg-gray-100 transition duration-300 ease-in-out">
                        Batal
                    </a>
                    <button type="submit"
                        class="bg-indigo-600 text-white px-5 py-2.5 rounded-lg font-semibold shadow-md shadow-indigo-300/50 hover:bg-indigo-700 transition duration-300 ease-in-out">
                        <i class="fas fa-check mr-2"></i>
                        Update Client
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection