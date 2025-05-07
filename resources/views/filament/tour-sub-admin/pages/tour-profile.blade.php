<x-filament::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-primary-600 mb-4">🏨 Tour Information</h2>
            <ul class="space-y-2 text-gray-900">
                <li><strong>Name:</strong> {{ $tour->name }}</li>
                <li><strong>Location:</strong> {{ $tour->location->name ?? 'N/A' }}</li>
                <li><strong>Duration:</strong> {{ $tour->durationDays }} days / {{ $tour->durationHours }} hrs</li>
                <li><strong>Base Price:</strong> ${{ $tour->basePrice }}</li>
                <li><strong>Status:</strong> {{ $tour->isActive ? 'Active' : 'Inactive' }}</li>
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
               
            <h2 class="text-2xl font-bold text-primary-600 mb-4">👤 Admin Information</h2>
            <ul class="space-y-2 text-gray-900">
                <li><strong>Name:</strong> {{ $tour->admin->name }}</li>
                <li><strong>Email:</strong> {{ $tour->admin->email }}</li>
                <li><strong>Role:</strong> {{ $tour->admin->role }}</li>
                <li><strong>Section:</strong> {{ $tour->admin->section }}</li>
            </ul>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-2xl p-6 border border-gray-200 dark:border-gray-700">
            <h2 class="text-2xl font-bold text-primary-600 mb-4">📅 Schedules</h2>
            @foreach($tour->schedules as $schedule)
                <div class="border-b py-2">
                    <strong>{{ $schedule->startDate }} - {{ $schedule->endDate }}</strong><br>
                    Spots: {{ $schedule->availableSpots }} - 
                    Price: ${{ $schedule->price ?? $tour->basePrice }}
                </div>
            @endforeach
        </div>

    </div>
</x-filament::page>
