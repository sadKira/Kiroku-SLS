<div class="h-screen flex items-center bg-gray-50 dark:bg-zinc-900">
    {{-- Left Column: Barcode Scanner --}}
    <div class="flex-2 flex flex-col p-8 h-screen relative">

        {{-- Back Button --}}
        <a href="{{ route('logger_dashboard') }}" wire:navigate 
           class="absolute top-4 left-4 z-10">
            <flux:button variant="ghost" icon="arrow-left">
                Back
            </flux:button>
        </a>

        {{-- Log Session Details (Top Center) --}}
        <div class="flex flex-col items-center" 
             x-data="{
                 barcodeBuffer: '',
                 lastKeyTime: 0,
                 init() {
                     const getBarcodeInput = () => {
                         return document.querySelector('[data-barcode-input] input') || 
                                this.$el.querySelector('input');
                     };
                     
                     const handleKeyPress = (e) => {
                         const activeElement = document.activeElement;
                         const barcodeInput = getBarcodeInput();
                         
                         const isTypingInOtherField = activeElement && 
                             (activeElement.tagName === 'INPUT' || activeElement.tagName === 'TEXTAREA') && 
                             activeElement !== barcodeInput && 
                             !activeElement.closest('[data-barcode-input]');
                         
                         if (isTypingInOtherField) return;
                         
                         const now = Date.now();
                         const timeSinceLastKey = now - this.lastKeyTime;
                         
                         if (timeSinceLastKey > 200) {
                             this.barcodeBuffer = '';
                         }
                         
                         if (e.key >= '0' && e.key <= '9') {
                             this.barcodeBuffer += e.key;
                             this.lastKeyTime = now;
                             
                             const input = getBarcodeInput();
                             if (input) {
                                 if (document.activeElement !== input) {
                                     e.preventDefault();
                                     input.focus();
                                 }
                                 
                                 input.value = this.barcodeBuffer;
                                 
                                 const inputEvent = new Event('input', { 
                                     bubbles: true, 
                                     cancelable: true 
                                 });
                                 input.dispatchEvent(inputEvent);
                                 input.dispatchEvent(new Event('change', { bubbles: true }));
                             }
                             
                             if (this.barcodeBuffer.length >= 7) {
                                 setTimeout(() => {
                                     this.barcodeBuffer = '';
                                 }, 200);
                             }
                         } else if (e.key === 'Enter' && this.barcodeBuffer.length > 0) {
                             e.preventDefault();
                             this.barcodeBuffer = '';
                         }
                     };
                     
                     document.addEventListener('keydown', handleKeyPress);
                     
                     this.$el.addEventListener('livewire:destroy', () => {
                         document.removeEventListener('keydown', handleKeyPress);
                     });
                 }
             }">

                <flux:heading size="xl" class="text-gray-800 dark:text-neutral-200 mb-1">
                    {{ \Carbon\Carbon::parse($logSession->date)->format('F j, Y') }} ({{ \Carbon\Carbon::parse($logSession->date)->format('l') }})
                </flux:heading>
                <flux:heading size="lg" class="text-gray-600 dark:text-neutral-400">
                    Academic Year: {{ $logSession->school_year }}
                </flux:heading>

                {{-- Barcode input --}}
                <div data-barcode-input>
                    <flux:input type="hidden" wire:model.live="barcode" mask="9999999" placeholder="Scan barcode..."
                        class="w-full text-center pointer-events-none" autocomplete="off" />
                </div>
        </div>

        {{-- Barcode Scanner Input --}}
        <div class="flex-1 flex flex-col items-center justify-center">
            <div class="w-full max-w-xl">

                {{-- Dynamic Label for Student Display --}}
                <div x-data="{
                    shown: false,
                    studentName: '',
                    studentYearLevel: '',
                    studentCourse: ''
                }" x-init="@this.on('scan-label', () => {
                    // Get latest values from Livewire
                    studentName = $wire.studentName;
                    studentYearLevel = $wire.studentYearLevel;
                    studentCourse = $wire.studentCourse;
                    shown = true;
                    setTimeout(() => { shown = false; }, 3000);
                });" class="mt-8">

                    <!-- Default State (shown when no scan) -->
                    <template x-if="!shown">
                        <div class="flex flex-col items-center justify-center gap-2 mt-8 w-full">
                            <flux:icon.barcode class="w-32 h-32" />
                            <div class="flex items-center gap-2">
                                <flux:heading size="xl" class="">Scan Your Barcode</flux:heading>
                            </div>
                        </div>
                    </template>

                    <!-- Student Details (shown temporarily when scan succeeds) -->
                    <template x-if="shown">
                        <div class="flex flex-col items-center justify-center gap-3 w-full">
                            
                            <flux:icon.user variant="solid" class="w-32 h-32" />

                            <div class="flex flex-col justify-center items-center">
                                <flux:heading size="xl" class="text-gray-800 dark:text-neutral-200"
                                    x-text="studentName"></flux:heading>
                                <flux:heading size="lg" class="text-gray-600 dark:text-neutral-400"
                                    x-text="studentYearLevel + ' - ' + studentCourse"></flux:heading>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Success Callout --}}
                <div x-data="{ shown: false }" x-init="@this.on('scan-success', () => {
                    shown = true;
                    setTimeout(() => { shown = false; }, 3000);
                });" class="mt-6">

                    <template x-if="shown">
                        <flux:callout variant="success" icon="check-circle" heading="Scan Successful" />
                    </template>
                </div>

                {{-- Error Callout --}}
                <div x-data="{ shown: false, message: '' }" x-init="@this.on('scan-error', (event) => {
                    message = event.message || event[0]?.message || 'An error occurred. Please try again.';
                    shown = true;
                    setTimeout(() => { shown = false;
                        message = ''; }, 5000);
                });" class="mt-6">

                    <template x-if="shown">
                        <flux:callout variant="danger" icon="exclamation-circle" heading="Error">
                            <span x-text="message"></span>
                        </flux:callout>
                    </template>
                </div>
            </div>
        </div>
    </div>

    <flux:separator vertical class="my-20" />

    {{-- Right Column: Log Records Table --}}
    <div class="flex-1 p-8 flex flex-col h-screen relative">

        {{-- Scrollable container with blur effects --}}
        <div class="flex-1 relative overflow-hidden" 
             x-data="{
                 showTopBlur: false,
                 showBottomBlur: false,
                 init() {
                     const scrollContainer = this.$refs.scrollContainer;
                     const checkScroll = () => {
                         this.showTopBlur = scrollContainer.scrollTop > 10;
                         const maxScroll = scrollContainer.scrollHeight - scrollContainer.clientHeight;
                         this.showBottomBlur = scrollContainer.scrollTop < maxScroll - 10;
                     };
                     scrollContainer.addEventListener('scroll', checkScroll);
                     checkScroll();
                     
                     // Also check on Livewire updates
                     if (typeof Livewire !== 'undefined') {
                         Livewire.hook('morph.updated', () => {
                             setTimeout(checkScroll, 100);
                         });
                     }
                     
                     // Check periodically in case content changes
                     setInterval(checkScroll, 500);
                 }
             }">
            {{-- Top blur overlay --}}
            <div class="absolute top-0 left-0 right-0 h-4 pointer-events-none z-10 
                        bg-gradient-to-b from-gray-50 via-gray-50/80 to-transparent
                        dark:from-zinc-900 dark:via-zinc-900/80 dark:to-transparent"
                 x-show="showTopBlur"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
            </div>

            {{-- Bottom blur overlay --}}
            <div class="absolute bottom-0 left-0 right-0 h-4 pointer-events-none z-10
                        bg-gradient-to-t from-gray-50 via-gray-50/80 to-transparent
                        dark:from-zinc-900 dark:via-zinc-900/80 dark:to-transparent"
                 x-show="showBottomBlur"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
            </div>

            <div
                x-ref="scrollContainer"
                class="h-full overflow-y-auto
                {{-- scroll-smooth --}}
                [&::-webkit-scrollbar]:w-2
                [&::-webkit-scrollbar-thumb]:rounded-full
                [&::-webkit-scrollbar-track]:bg-gray-100
                [&::-webkit-scrollbar-thumb]:bg-gray-300
                dark:[&::-webkit-scrollbar-track]:bg-neutral-700
                dark:[&::-webkit-scrollbar-thumb]:bg-neutral-500
            ">
                @livewire('logger.view-logs-table', ['logSession' => $logSession])
            </div>
        </div>

    </div>
</div>
