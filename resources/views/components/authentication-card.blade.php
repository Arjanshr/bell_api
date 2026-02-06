<div class="min-h-screen bg-gradient-to-br from-blue-600 via-purple-600 to-pink-600 flex items-center justify-center px-4 py-8">
    <div class="relative w-full max-w-md">

        <!-- Glow Effect -->
        <div class="absolute -inset-1 bg-gradient-to-r from-blue-400 via-purple-400 to-pink-400 opacity-20 blur-3xl rounded-3xl"></div>

        <!-- Card -->
        <div class="relative bg-white/95 backdrop-blur-lg border border-white/30 rounded-3xl shadow-2xl overflow-hidden">

            <!-- Header Gradient -->
            <div class="bg-gradient-to-r from-blue-600 via-purple-600 to-pink-600 px-6 sm:px-8 py-8 sm:py-12">
                <div class="flex flex-col items-center text-center">
                    <a href="/" wire:navigate class="group">
                        <div class="relative h-16 w-16 flex items-center justify-center rounded-2xl bg-white/20 backdrop-blur-sm shadow-lg hover:bg-white/30 transition-all duration-300">
                            <span class="text-white text-2xl font-bold select-none">
                                {{ $logo }}
                            </span>
                        </div>
                    </a>

                    <h1 class="mt-6 text-3xl font-bold text-white tracking-wide">
                        Welcome
                    </h1>
                    <p class="mt-2 text-blue-100 text-sm">
                        Sign in to Bell Admin Panel
                    </p>
                </div>
            </div>

            <!-- Form Content -->
            <div class="px-6 sm:px-8 py-8 sm:py-10">
                {{ $slot }}
            </div>

            <!-- Footer -->
            <div class="px-6 sm:px-8 py-4 border-t border-gray-200 bg-gray-50/50">
                <p class="text-center text-xs text-gray-500">
                    © 2026 Bell Nepal. All rights reserved.
                </p>
            </div>
        </div>

        <!-- Bottom text -->
        <p class="text-center text-xs text-white/70 mt-6">
            Building something exciting
        </p>
    </div>
</div>
<style>
@keyframes bell-ring {
    0% { transform: rotate(0deg); }
    1% { transform: rotate(8deg); }
    3% { transform: rotate(-8deg); }
    5% { transform: rotate(6deg); }
    7% { transform: rotate(-6deg); }
    9% { transform: rotate(3deg); }
    11% { transform: rotate(-3deg); }
    13% { transform: rotate(0deg); }
    100% { transform: rotate(0deg); }
}

.animate-bell {
    animation: bell-ring 6s ease-in-out infinite;
    transform-origin: top center;
}
</style>
