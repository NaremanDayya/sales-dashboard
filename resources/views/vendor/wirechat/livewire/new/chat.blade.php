@use('Namu\WireChat\Facades\WireChat')
<div id="new-chat-modal ">

    <div
        class="relative w-full h-96  border mx-auto border-[var(--wc-light-secondary)]  dark:border-[var(--wc-dark-secondary)] overflow-auto bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)] dark:text-white px-7 sm:max-w-lg sm:rounded-lg">

        <header class=" sticky top-0 bg-[var(--wc-light-primary)] dark:bg-[var(--wc-dark-primary)] z-10 py-2">
            <div class="flex justify-between items-center justify-between pb-2">

                <h3 class="text-lg font-semibold">{{__('wirechat::new.chat.labels.heading') }}</h3>

                <x-wirechat::actions.close-modal>
                    <button
                        dusk="close_modal_button"
                        class="p-2  text-gray-600 hover:bg-[var(--wc-light-secondary)] dark:hover:bg-[var(--wc-dark-secondary)] dark:hover:text-white rounded-full hover:text-gray-800 ">
                        <svg class="w-5 h-5 cursor-pointer" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </x-wirechat::actions.close-modal>

            </div>

            <section
                class="flex flex-wrap items-center px-0 border-b border-[var(--wc-light-border)] dark:border-[var(--wc-dark-border)]">
                <input dusk="search_users_field" autofocus type="search" id="users-search-field"
                       wire:model.live.debounce='search' autocomplete="off"
                       placeholder="{{__('wirechat::new.chat.inputs.search.placeholder')}}"
                       class=" w-full border-0 w-auto px-0 dark:bg-[var(--wc-dark-primary)] outline-hidden focus:outline-hidden bg-[var(--wc-light-primary)] rounded-lg focus:ring-0 hover:ring-0">

            </section>
        </header>

        <div class="relative w-full">

            {{-- New Group button --}}
            @if (WireChat::showNewGroupModalButton() && auth()->user()->canCreateGroups())

                {{-- Buton to trigger opening of new grop modal --}}
                <x-wirechat::actions.new-group widget="{{$this->isWidget()}}">
                    <button @dusk="open_new_group_modal_button"
                            class="flex items-center gap-3 my-4  rounded-lg p-2 w-full border  transition-colors border-[var(--wc-light-border)]  dark:border-[var(--wc-dark-border)] hover:border-[var(--wc-light-secondary)] dark:hover:border-[var(--wc-dark-secondary)]">
            <span style=" color: var(--wc-brand-primary); " class="p-1 bg-gray-100  rounded-full ">


            </span>

                        <p class="dark:text-white">@lang('wirechat::new.chat.actions.new_group.label')</p>
                    </button>
                </x-wirechat::actions.new-group>
            @endif
            {{-- <h5 class="text font-semibold text-gray-800 dark:text-gray-100">Recent Chats</h5> --}}
            <section class="my-4 grid">
                @if (count($users)!=0)

                    <ul class="overflow-auto flex flex-col">

                        @foreach ($users as $key => $user)
                            <li wire:key="user-{{ $key }}"
                                wire:click="createConversation('{{ $user->id }}',{{ json_encode(get_class($user)) }})"
                                class="flex cursor-pointer group gap-2 items-center p-2">

                                <x-wirechat::avatar :src="{{ $user->cover_url
    ? Storage::disk('s3')->temporaryUrl($user->cover_url, now()->addMinutes(5))
    : 'https://ui-avatars.com/api/?name=' . urlencode($user->name) . '&background=random' }}"
                                                    alt="{{ $user->name }}" class="w-10 h-10"/>

                                <p class="group-hover:underline transition-all">
                                    {{ $user->display_name }}</p>

                            </li>
                        @endforeach


                    </ul>
                @else
                    @if (!empty($search))

                        <span class="m-auto">@lang('wirechat::new.chat.messages.empty_search_result')</span>
                    @endif
                @endif

            </section>
        </div>
    </div>
</div>
