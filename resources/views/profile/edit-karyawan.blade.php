@if (Auth::user()->hasRole('karyawan'))
    <div class="grid grid-cols-2 gap-4 mb-4">
        <div class="mb-4">
            <label for="nik" class="block text-gray-700 font-medium mb-1">Nik
            </label>
            <input type="text" value="{{ old('nik', Auth::user()->karyawan->nik) }}" name="nik"
                class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mb-4">
            <label for="phone" class="block text-gray-700 font-medium mb-1">Phone</label>
            <input type="text" value="{{ old('phone', Auth::user()->karyawan->phone) }}" name="phone"
            class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>

        <div class="mb-4">
            <label for="jabatan" class="block text-gray-700 font-medium mb-1">Jabatan</label>
            <input type="text" value="{{ old('jabatan', Auth::user()->karyawan->jabatan) }}" name="jabatan"
            class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
        </div>


        <div class="mb-4">
            <label for="job_title" class="block text-gray-700 font-medium mb-1">Job Title</label>
            <select name="job_title" id="job_title" class="w-full border rounded-lg px-3 py-2 focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Pilih Jabatan</option>
                @php
                $jobTitles = [
                    'Analisis Proses Bisnis',
                    'Database Functional',
                    'Programmer',
                    'Quality Test',
                    'SysAdmin'
                ]
                @endphp
                @foreach ($jobTitles as $title )
                    <option value="{{ $title }}"
                        {{ old('job_title', Auth::user()->karyawan->job_title ?? Auth::user()->karyawan->job_title) == $title ? 'selected' : ''  }}>
                        {{ $title }}
                    </option>
                @endforeach
            </select>
        </div>

    </div>
@endif
