<div class="min-h-screen flex flex-col justify-center items-center px-4 py-10 bg-slate-50">
    <div class="mb-6">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md px-6 py-6 sm:px-8 sm:py-8 bg-white border border-gray-200 shadow-sm overflow-hidden rounded-2xl">
        {{ $slot }}
    </div>
</div>
