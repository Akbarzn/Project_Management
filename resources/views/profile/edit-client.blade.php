@if(Auth::user()->hasRole('client'))
<div class="mb-4">
    <label for="nik" class="block text-gray-700 font-medium mb-1">Nik</label>
    <input type="text" name="nik" value="{{ old('nik', auth::user()->client->nik) }}"
    class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
</div>

<div class="mb-4">
    <label for="kode_organisasi" class="block text-gray-700 font-medium mb-1">Kode Organisasi</label>
    <input type="text" name="kode_organisasi" value="{{ old('kode_organisasi', Auth::user()->client->kode_organisasi) }}"
    class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
</div>

<div class="mb-4">
    <label for="phone" class="block text-gray-700 font-medium mb-1">Phone</label>
    <input type="text" name="phone" value="{{ old('phone', Auth::user()->client->phone) }}"
    class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
</div>
@endif