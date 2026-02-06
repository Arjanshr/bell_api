<div id="auth-container-parent" class="relative w-full sm:max-w-md sm:mx-auto" style="background-color: orange; margin:0 auto;">
    <div id="auth-container"
        class="flex relative top-0 z-20 flex-col justify-center items-stretch px-10 py-8 w-full h-screen bg-white border-gray-200 sm:top-auto sm:h-full sm:border sm:rounded-xl">
        <div class="flex flex-col sm:mx-auto sm:w-full mb-5 sm:max-w-md items-center" id="auth-heading-container"
            style="color:#212936">
            <div class="flex flex-col w-full items-center">
                <a href="/" style="height:100px; width:auto; display:block" aria-label="Mobile Mandu Logo"
                    wire:navigate="">
                    {{ $logo }}
                </a>
            </div>
            <h1 id="auth-heading-title" class="mt-1 text-xl font-medium leading-9">Sign in</h1>
        </div>



        {{ $slot }}


        <div class="mt-3 space-x-0.5 text-sm leading-5 text-left" style="color:#212936">
            <span class="opacity-[47%]"> Don't have an account? </span>
            <a class="underline cursor-pointer opacity-[67%] hover:opacity-[80%]" data-auth="register-link"
                href="{{route('register')}}" wire:navigate="">
                Sign up
            </a>
        </div>
    </div>
</div>
