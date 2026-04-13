<div class="max-w-5xl mx-auto px-4">
    <div class="flex items-end justify-between bg-white border border-black/10 [:where(&)]:p-4 [:where(&)]:rounded-lg">
        <div class="flex items-center gap-4">
            <img src="{{ asset('mkdlib-logo.ico') }}" alt="MKD Library Logo" class="w-auto h-50">
            <div>
                <div class="flex items-center gap-2">
                    <flux:heading size="xl">Kiroku SLS (Student Logging System)</flux:heading>
                    <flux:badge class="ml-1 max-sm:hidden">2025</flux:badge>
                </div>

                <flux:text class="mt-2">
                    On-the-job training project for <span class="font-semibold">Mindanao Kokusai Daigaku (MKD) Learning Resource Center</span> to streamline library entry/exit logging through barcode scanning. Built on the TALL stack for a responsive, real-time experience tailored to MKD librarians and students.
                </flux:text>

                <div class="flex items-end justify-between mt-6">
                    <flux:avatar.group class="">
                        <flux:avatar circle size="lg" initials="JL" />
                        <flux:avatar circle size="lg" initials="ES" />
                        <flux:avatar circle size="lg" initials="CY" />
                        <flux:avatar circle size="lg" initials="SF" />
                        <flux:avatar circle size="lg" initials="GA" />
                        <flux:avatar circle size="lg" color="red" initials="LN" />
                    </flux:avatar.group>

                    {{-- TALL Stack icons: faded, aligned to the bottom-right --}}
                    <div class="flex items-end gap-3 opacity-60 shrink-0 pb-1">
                        {{-- Tailwind CSS --}}
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-7 h-7 fill-current text-slate-800 dark:text-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-label="Tailwind CSS">
                                <path d="M12.001,4.8c-3.2,0-5.2,1.6-6,4.8c1.2-1.6,2.6-2.2,4.2-1.8c0.913,0.228,1.565,0.89,2.288,1.624 C13.666,10.618,15.027,12,18.001,12c3.2,0,5.2-1.6,6-4.8c-1.2,1.6-2.6,2.2-4.2,1.8c-0.913-0.228-1.565-0.89-2.288-1.624 C16.337,6.182,14.976,4.8,12.001,4.8z M6.001,12c-3.2,0-5.2,1.6-6,4.8c1.2-1.6,2.6-2.2,4.2-1.8c0.913,0.228,1.565,0.89,2.288,1.624 c1.177,1.194,2.538,2.576,5.512,2.576c3.2,0,5.2-1.6,6-4.8c-1.2,1.6-2.6,2.2-4.2,1.8c-0.913-0.228-1.565-0.89-2.288-1.624 C10.337,13.382,8.976,12,6.001,12z"/>
                            </svg>
                            {{-- <span class="text-[9px] font-medium tracking-wide text-slate-600 dark:text-neutral-400">Tailwind</span> --}}
                        </div>

                        {{-- Alpine.js --}}
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-7 h-7 fill-current text-slate-800 dark:text-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-label="Alpine.js">
                                <path d="m24 12-5.72 5.746-5.724-5.741 5.724-5.75L24 12zM5.72 6.254 0 12l5.72 5.746h11.44L5.72 6.254z"/>
                            </svg>
                            {{-- <span class="text-[9px] font-medium tracking-wide text-slate-600 dark:text-neutral-400">Alpine</span> --}}
                        </div>

                        {{-- Laravel --}}
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-7 h-7 fill-current text-slate-800 dark:text-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-label="Laravel">
                                <path d="M23.642 5.43a.364.364 0 01.014.1v5.149c0 .135-.073.26-.189.326l-4.323 2.49v4.934a.378.378 0 01-.188.326L9.93 23.949a.316.316 0 01-.066.027c-.008.002-.016.008-.024.01a.348.348 0 01-.192 0c-.011-.002-.02-.008-.03-.012-.02-.008-.042-.014-.062-.025L.533 18.755a.376.376 0 01-.189-.326V2.974c0-.033.005-.066.014-.098.003-.012.01-.02.014-.032a.369.369 0 01.023-.058c.004-.013.015-.022.023-.033l.033-.045c.012-.01.025-.018.037-.027.014-.012.027-.024.041-.034H.53L5.043.05a.375.375 0 01.375 0L9.93 2.647h.002c.015.01.027.021.04.033l.038.027c.013.014.02.03.033.045.008.011.02.021.025.033.01.02.017.038.024.058.003.011.01.021.013.032.01.031.014.064.014.098v9.652l3.76-2.164V5.527c0-.033.004-.066.013-.098.003-.01.01-.02.013-.032a.487.487 0 01.024-.059c.007-.012.018-.02.025-.033.012-.015.021-.03.033-.043.012-.012.025-.02.037-.028.014-.01.026-.023.041-.032h.001l4.513-2.598a.375.375 0 01.375 0l4.513 2.598c.016.01.027.021.042.031.012.01.025.018.036.028.013.014.022.03.034.044.008.012.019.021.024.033.011.02.018.04.024.06.006.01.012.021.015.032zm-.74 5.032V6.179l-1.578.908-2.182 1.256v4.283zm-4.51 7.75v-4.287l-2.147 1.225-6.126 3.498v4.325zM1.093 3.624v14.588l8.273 4.761v-4.325l-4.322-2.445-.002-.003H5.04c-.014-.01-.025-.021-.04-.031-.011-.01-.024-.018-.035-.027l-.001-.002c-.013-.012-.021-.025-.031-.04-.01-.011-.021-.022-.028-.036h-.002c-.008-.014-.013-.031-.02-.047-.006-.016-.014-.027-.018-.043a.49.49 0 01-.008-.057c-.002-.014-.006-.027-.006-.041V5.789l-2.18-1.257zM5.23.81L1.47 2.974l3.76 2.164 3.758-2.164zm1.956 13.505l2.182-1.256V3.624l-1.58.91-2.182 1.255v9.435zm11.581-10.95l-3.76 2.163 3.76 2.163 3.759-2.164zm-.376 4.978L16.21 7.087 14.63 6.18v4.283l2.182 1.256 1.58.908zm-8.65 9.654l5.514-3.148 2.756-1.572-3.757-2.163-4.323 2.489-3.941 2.27z"/>
                            </svg>
                            {{-- <span class="text-[9px] font-medium tracking-wide text-slate-600 dark:text-neutral-400">Laravel</span> --}}
                        </div>

                        {{-- Livewire --}}
                        <div class="flex flex-col items-center gap-1">
                            <svg class="w-7 h-7 fill-current text-slate-800 dark:text-white" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" aria-label="Livewire">
                                <path d="M12.001 0C6.1735 0 1.4482 4.9569 1.4482 11.0723c0 2.0888.5518 4.0417 1.5098 5.709.2492.2796.544.4843.9649.4843 1.3388 0 1.2678-2.0644 2.6074-2.0644 1.3395 0 1.4111 2.0644 2.75 2.0644 1.3388 0 1.2659-2.0644 2.6054-2.0644.5845 0 .9278.3967 1.2403.8398-.2213-.2055-.4794-.3476-.8203-.3476-1.1956 0-1.3063 1.6771-2.2012 2.1406v4.5097c0 .9145.7418 1.6563 1.6562 1.6563.9145 0 1.6563-.7418 1.6563-1.6563v-5.8925c.308.4332.647.8144 1.2207.8144 1.3388 0 1.266-2.0644 2.6055-2.0644.465 0 .7734.2552 1.039.58-.1294-.0533-.2695-.0878-.4297-.0878-1.1582 0-1.296 1.574-2.1171 2.0937v2.4356c0 .823.6672 1.4902 1.4902 1.4902s1.4902-.6672 1.4902-1.4902V16.371c.3234.4657.6684.8945 1.2774.8945.7955 0 1.093-.7287 1.4843-1.3203.6878-1.4704 1.0743-3.1245 1.0743-4.873C22.5518 4.9569 17.8284 0 12.001 0zm-.5664 2.877c2.8797 0 5.2148 2.7836 5.2148 5.8066 0 3.023-1.5455 5.1504-5.2148 5.1504-3.6693 0-5.2149-2.1274-5.2149-5.1504S8.5548 2.877 11.4346 2.877zM10.0322 4.537a1.9554 2.1583 0 00-1.955 2.1582 1.9554 2.1583 0 001.955 2.1582 1.9554 2.1583 0 001.9551-2.1582 1.9554 2.1583 0 00-1.955-2.1582zm-.3261.664a.9777.9961 0 01.9785.9962.9777.9961 0 01-.9785.996.9777.9961 0 01-.9766-.996.9777.9961 0 01.9766-.9961zM6.7568 15.6935c-1.0746 0-1.2724 1.3542-1.9511 1.9648v1.7813c0 .823.6672 1.4902 1.4902 1.4902s1.4902-.6672 1.4902-1.4902v-3.1817c-.2643-.3237-.5767-.5644-1.0293-.5644Z"/>
                            </svg>
                            {{-- <span class="text-[9px] font-medium tracking-wide text-slate-600 dark:text-neutral-400">Livewire</span> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>