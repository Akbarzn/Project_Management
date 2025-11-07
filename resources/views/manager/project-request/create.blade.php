@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto bg-white p-8 rounded-xl shadow-md">
        <h2 class="text-2xl font-bold mb-6 text-gray-800">Create Project Request </h2>

        <form action="{{ route('manager.project-request.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{--  tiket --}}
            <div class="mb-4">
                <label class="block text-gray-700 font-medium mb-1">Nomor Tiket</label>
                <input type="text" name="tiket" value="{{ $ticketNumber }}" readonly
                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100 text-gray-600">
            </div>

            {{-- onchange="if(this.value) window.location='{{ route('manager.project-request.create', $projectRequest->id) }}'"> --}}
            {{-- client --}}
            <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Pilih Client</label>
            <select name="client_id" required
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500"
                <option value="">-- Pilih Client --</option>
                @foreach ($clients as $client)
                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->name }}
             </option>
                @endforeach
            </select>
            @error('client_id')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>


        @if($selectedClient)
        <div class="mb-6 p-4 bg-gray-50 border rounded-lg">
            <h3 class="font-semibold text-gray-800 mb-2">Detail Client:</h3>
            <p><span class="font-medium text-gray-700">Nama:</span> {{ $selectedClient->name }}</p>
            <p><span class="font-medium text-gray-700">NIK:</span> {{ $selectedClient->nik }}</p>
            <p><span class="font-medium text-gray-700">Kode Organisasi:</span> {{ $selectedClient->kode_organisasi }}</p>
            <p><span class="font-medium text-gray-700">Phone:</span> {{ $selectedClient->phone }}</p>
        </div>
        @endif
        

            {{-- <div class="mb-4">
                <label class="block"> Pilih Client</label>
                <form action="{{ route('manager.project-request.create') }}" method="GET">
                    <select name="client_id" id="">
                        <option value="">Pilih Client</option>
                        @foreach ($clients as $client)
                            <option value="{{ $client->id }} {{ request('client_id') == $client->id ? 'selected' : '' }}">
                                {{ $client->name }}
                            </option>
                        @endforeach
                    </select>
                </form> --}}

                {{-- @if ($selectedClient)
                    <div class="mb-4">
                        <label for="nik">Nik:</label>
                        <input type="text" name="nik" readonly value="{{ $selectedClient->nik }}">
                    </div>

                    <div class="mb-4">
                        <label for="kode_organisasi">Kode Organisasi</label>
                        <input type="text" name="kode_organisasi" value="{{ $selectedCLient->kode_organisasi }}">
                    </div>

                    <div class="mb-4">
                        <label for="phone">Phone :</label>
                        <input type="text" name="phone" value="{{$selectedClient->phone }}">
                    </div>
                @endif --}}


                {{-- nama project --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Nama Project</label>
                    <input type="text" name="name_project" value="{{ old('name_project') }}" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    @error('name_project')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- kategori --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-2">Kategori</label>
                    <div class="flex items-center space-x-4">
                        <label class="inline-flex items-center">
                            <input type="radio" name="kategori" value="New Aplikasi"
                                {{ old('kategori') == 'New Aplikasi' ? 'checked' : '' }}
                                class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-700">New Aplikasi</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="kategori" value="Update Aplikasi"
                                {{ old('kategori') == 'Update Aplikasi' ? 'checked' : '' }}
                                class="text-indigo-600 focus:ring-indigo-500">
                            <span class="ml-2 text-gray-700">Update Aplikasi</span>
                        </label>
                    </div>
                    @error('kategori')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- deskripsi --}}
                <div class="mb-4">
                    <label class="block text-gray-700 font-medium mb-1">Deskripsi Project</label>
                    <textarea name="description" rows="4" required
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- dokumen --}}
                <div class="mb-6">
                    <label class="block text-gray-700 font-medium mb-1">Upload Dokumen Pendukung (opsional)</label>
                    <input type="file" name="document"
                        class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-sm text-gray-500 mt-1">Format: PDF, DOC, DOCX, PNG, JPG (maks. 2MB)</p>
                    @error('document')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-6 py-2 rounded-md shadow-md transition">
                        Simpan Request
                    </button>
                </div>
        </form>
    </div>
@endsection
