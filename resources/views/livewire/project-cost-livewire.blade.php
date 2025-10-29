<div wire:poll.10s="refreshCost" class="bg-green-50 p-4 rounded-lg border border-green-200">
    <h3 class="text-lg font-semibold text-gray-800 mb-2">💰 Total Cost (Realtime)</h3>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
        <p class="text-gray-700">
            <span class="font-semibold">Total Cost di Database:</span>
            Rp {{ number_format($project->total_cost, 0, ',', '.') }}
        </p>

        <p class="text-gray-700">
            <span class="font-semibold">Total Cost Realtime:</span>
            Rp {{ number_format($realtimeCost, 0, ',', '.') }}
        </p>
    </div>

    @if($realtimeCost != $project->total_cost)
        <p class="mt-3 text-sm text-amber-600">
            ⚠️ Nilai realtime berbeda dari database (belum diupdate).
        </p>
    @else
        <p class="mt-3 text-sm text-green-600">
            ✅ Nilai realtime sama dengan total cost yang tersimpan.
        </p>
    @endif
</div>
