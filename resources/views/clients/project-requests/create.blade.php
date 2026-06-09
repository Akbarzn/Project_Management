@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto bg-white p-6 rounded-lg shadow-md">
    <h3 class="text-2xl font-semibold mb-6 ">Buat Project Request</h3>

    <form action="{{ route('clients.project-requests.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- tiket --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nomor Tiket</label>
            <input type="text" value="{{ $ticketNumber }}" readonly
                class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- nama --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama</label>
            <input type="text" value="{{ $client->name }}" readonly
                class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- nik --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nik</label>
            <input type="text" value="{{ $client->nik }}" readonly
            class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- kode rganisasi--}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Kode Organisasi</label>
            <input type="text" value="{{ $client->kode_organisasi }}" readonly
            class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- phone --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium">Phone</label>
            <input type="text" value="{{ $client->phone }}" readonly
            class="block w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
        </div>

        {{-- nama project --}}
        <div class="mb-4">
            <label class="block text-gray-700 font-medium mb-1">Nama Project</label>
            <input type="text" name="name_project" required
                class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>

        {{-- kategori --}}
        <div class="mb-4">
            <span class="block text-gray-700 font-medium mb-2">Kategori</span>
            <label class="inline-flex items-center mr-4">
                <input type="radio" name="kategori" value="New Aplikasi" required
                    class="form-radio text-indigo-600">
                <span class="ml-2">New Aplikasi</span>
            </label>
            <label class="inline-flex items-center">
                <input type="radio" name="kategori" value="Update Aplikasi" required
                    class="form-radio text-indigo-600">
                <span class="ml-2">Update Aplikasi</span>
            </label>
        </div>

        {{-- deskripsi --}}
        <div class="mb-4">
            <label for="description" class="block text-gray-700 font-medium mb-1">Deskripsi</label>
            <textarea name="description" id="description" rows="4" required
                class="block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500"></textarea>
        </div>

        {{-- Parameter Auto Assignment Tim --}}
        <div class="mb-6 p-5 bg-indigo-50 border border-indigo-200 rounded-xl">
            <h3 class="text-base font-bold text-indigo-800 mb-4 flex items-center gap-2">
                <i class="fas fa-robot text-indigo-600"></i>
                Parameter Auto Assignment Tim
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                {{-- Priority --}}
                <div>
                    <label for="priority" class="block text-sm font-semibold text-gray-700 mb-1">
                        Priority
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="priority" id="priority"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('priority') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Priority --</option>
                        <option value="1" {{ old('priority') == '1' ? 'selected' : '' }}>Low</option>
                        <option value="2" {{ old('priority') == '2' ? 'selected' : '' }}>Medium</option>
                        <option value="3" {{ old('priority') == '3' ? 'selected' : '' }}>High</option>
                        <option value="4" {{ old('priority') == '4' ? 'selected' : '' }}>Critical</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Difficulty --}}
                <div>
                    <label for="difficulty" class="block text-sm font-semibold text-gray-700 mb-1">
                        Difficulty
                        <span class="text-red-500">*</span>
                    </label>
                    <select name="difficulty" id="difficulty"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500 @error('difficulty') border-red-500 @enderror"
                        required>
                        <option value="">-- Pilih Difficulty --</option>
                        <option value="1" {{ old('difficulty') == '1' ? 'selected' : '' }}>Sangat Mudah</option>
                        <option value="2" {{ old('difficulty') == '2' ? 'selected' : '' }}>Mudah</option>
                        <option value="3" {{ old('difficulty') == '3' ? 'selected' : '' }}>Sedang</option>
                        <option value="4" {{ old('difficulty') == '4' ? 'selected' : '' }}>Sulit</option>
                        <option value="5" {{ old('difficulty') == '5' ? 'selected' : '' }}>Sangat Sulit</option>
                    </select>
                    @error('difficulty')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            {{-- Info Task Weight --}}
            <p class="text-xs text-indigo-600 mt-3">
                <i class="fas fa-info-circle mr-1"></i>
                <strong>Task Weight</strong> = Priority × Difficulty — digunakan oleh sistem untuk mengukur bobot penugasan pada pembagian tim otomatis (Least Load).
            </p>
        </div>

        {{-- dokumen --}}
        <div class="mb-4">
            <label for="document" class="block text-gray-700 font-medium mb-1">Upload Dokumen (opsional)</label>
            <input type="file" name="document" id="document"
                class="block w-full border border-gray-300 rounded-md px-3 py-2">
            <p class="text-sm text-gray-500 mt-1">PDF, DOC, DOCX, PNG, JPG, JPEG (Maks 2MB)</p>
        </div>

        <button type="submit"
            class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700 transition">Kirim Request</button>
    </form>
</div>
@endsection
