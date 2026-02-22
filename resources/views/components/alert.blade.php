<div {{ $attributes->merge([
    'class' => 'fixed bottom-4 right-4 z-[9999] ',
]) }}>

    @if (session()->has('message'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-6 right-6 space-y-4 z-50">
            <div x-bind:class="show ? 'alert-slide-in' : 'alert-fade-out'"
                class="alert  w-80 bg-green-500 text-white rounded-lg shadow-xl overflow-hidden relative">

                <div class=" flex items-start p-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-2xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold">Success!</h3>
                        <p class="mt-1 text-sm">{{ session('message') }}</p>
                    </div>
                    <button class="text-white opacity-70 hover:opacity-100 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="progress-bar"></div>
            </div>

        </div>
    @endif
    @if (session()->has('error'))
        <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 3000)" class="fixed bottom-6 right-6 space-y-4 z-50">
            <div x-bind:class="show ? 'alert-slide-in' : 'alert-fade-out'"
                class="alert  w-80 bg-danger text-white rounded-lg shadow-xl overflow-hidden relative">

                <div class=" flex items-start p-4">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-2xl"></i>
                    </div>
                    <div class="ml-3 flex-1">
                        <h3 class="text-lg font-semibold">Error!</h3>
                        <p class="mt-1 text-sm">{{ session('error') }}</p>
                    </div>
                    <button class="text-white opacity-70 hover:opacity-100 focus:outline-none">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="progress-bar"></div>
            </div>
        </div>
    @endif
</div>

<style>
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    @keyframes fadeOut {
        from {
            opacity: 1;
        }

        to {
            opacity: 0;
        }
    }

    .alert-slide-in {
        animation: slideIn 0.5s ease-out forwards;
    }

    .alert-fade-out {
        animation: fadeOut 0.5s ease-out forwards;
    }

    .progress-bar {
        width: 100%;
        height: 4px;
        background: rgba(255, 255, 255, 0.3);
        position: absolute;
        bottom: 0;
        left: 0;
        overflow: hidden;
    }

    .progress-bar::after {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        height: 100%;
        width: 100%;
        background: white;
        animation: progress 3s linear forwards;
        transform-origin: left;
    }

    @keyframes progress {
        0% {
            transform: scaleX(1);
        }

        100% {
            transform: scaleX(0);
        }
    }
</style>
