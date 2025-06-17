<div class="border-b justify-between flex items-center pb-2">
    <div class="flex items-center gap-2">
        <h5 class="font-extrabold text-2xl">المحادثات</h5>
    </div>

    <div class="flex items-center gap-2">
        <!-- New Chat Button with Animation -->
        <button id="newChatBtn" class=" flex items-center focus:outline-hidden">
            <svg class="w-8 h-8 -mb-1 text-gray-500 cursor-pointer hover:text-gray-900 dark:hover:text-gray-200 dark:text-gray-300"
                xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <g fill="none" stroke="currentColor">
                    <path
                        d="M12.875 5C9.225 5 7.4 5 6.242 6.103a4 4 0 0 0-.139.139C5 7.4 5 9.225 5 12.875V17c0 .943 0 1.414.293 1.707S6.057 19 7 19h4.125c3.65 0 5.475 0 6.633-1.103a4 4 0 0 0 .139-.139C19 16.6 19 14.775 19 11.125">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 10h6m-6 4h3m7-6V2m-3 3h6"></path>
                </g>
            </svg>
            <span
                class="absolute top-full right-0 mt-2 w-max bg-gray-800 text-white text-xs rounded py-1 px-2 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                محادثة جديدة
            </span>
        </button>


        <a id="redirect-button" href="{{ route('dashboard') }}" class="flex items-center">
            <svg class="bi bi-x-octagon-fill w-8 my-auto h-8 stroke-[0.9] text-gray-500 dark:text-gray-400 transition-colors duration-300 dark:hover:text-gray-500 hover:text-gray-900"
                xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24">
                <g fill="none" stroke="currentColor">
                    <path
                        d="M5 12.76c0-1.358 0-2.037.274-2.634c.275-.597.79-1.038 1.821-1.922l1-.857C9.96 5.75 10.89 4.95 12 4.95s2.041.799 3.905 2.396l1 .857c1.03.884 1.546 1.325 1.82 1.922c.275.597.275 1.276.275 2.634V17c0 1.886 0 2.828-.586 3.414S16.886 21 15 21H9c-1.886 0-2.828 0-3.414-.586S5 18.886 5 17z">
                    </path>
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M14.5 21v-5a1 1 0 0 0-1-1h-3a1 1 0 0 0-1 1v5">
                    </path>
                </g>
            </svg>
        </a>
    </div>

</div>
<div class="bg-gray-100 rounded-xl">
    <section class="mt-2">
        <div
            class="px-4 py-2 rounded-lg dark:bg-[var(--wc-dark-secondary)] bg-[var(--wc-light-secondary)] grid grid-cols-12 items-center shadow-sm">

            <!-- Search Icon -->
            <label for="chats-search-field" class="col-span-1 flex justify-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                    stroke="currentColor" class="w-5 h-5 text-gray-600 dark:text-gray-300">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z">
                    </path>
                </svg>
            </label>

            <input id="clientSearch" name="chats_search" maxlength="100" type="search"
                wire:model.live.debounce="search" placeholder="Search" autocomplete="off"
                class="col-span-11 bg-inherit text-sm border-none dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:ring-0 focus:outline-none w-full">
        </div>
    </section>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
    const searchInput      = document.getElementById('chats-search-field');
    const listContainer    = document.getElementById('conversationsList');
    const emptyState       = document.getElementById('noConvoMsg');

    if (!searchInput || !listContainer) return;

    const items = Array.from(listContainer.querySelectorAll('li[data-name]'));

    function filterList() {
        const q = searchInput.value.trim().toLowerCase();
        let visible = 0;

        items.forEach(li => {
            const name    = li.dataset.name;
            const company = li.dataset.company;

            const show = !q || name.includes(q) || company.includes(q);

            li.classList.toggle('hidden', !show);
            if (show) visible++;
        });

        if (emptyState) emptyState.classList.toggle('hidden', visible !== 0);
    }

    filterList();

    searchInput.addEventListener('input', filterList);
});

</script>
