document.addEventListener("DOMContentLoaded", function () {
    const newChatBtn = document.getElementById("newChatBtn");
    const clientSelectionModal = document.getElementById(
        "clientSelectionModal"
    );
    const closeClientModal = document.getElementById("closeClientModal");
    const clientSearch = document.getElementById("clientSearch");
    const clientList = document.getElementById("clientList");

    let clients = [];

    // Fetch clients on load
    fetchClients();

    // Event listeners
    newChatBtn?.addEventListener("click", openClientModal);
    closeClientModal?.addEventListener("click", closeModal);
    clientSearch?.addEventListener("input", filterClients);

    window.addEventListener("click", function (event) {
        if (event.target === clientSelectionModal) {
            closeModal();
        }
    });

    function openClientModal() {
        if (clientSelectionModal) {
            clientSelectionModal.classList.remove("hidden");
            setTimeout(() => clientSearch?.focus(), 100);
        }
    }

    function closeModal() {
        clientSelectionModal?.classList.add("hidden");
        if (clientSearch) clientSearch.value = "";
    }

    function fetchClients() {
        fetch("/api/clients", {
            headers: {
                Accept: "application/json",
            },
        })
            .then((response) => {
                if (!response.ok) throw new Error("Failed to fetch clients");
                return response.json();
            })
            .then((data) => {
                clients = data;
                renderClientList(clients);
            })
            .catch((error) => {
                console.error("Error fetching clients:", error);
                clientList.innerHTML = `
                    <li class="px-3 py-4 text-center text-red-500">
                        Failed to load clients.
                    </li>
                `;
            });
    }

    function renderClientList(clientsToRender) {
        if (!clientList) return;

        clientList.innerHTML = "";

        if (clientsToRender.length === 0) {
            clientList.innerHTML = `
                <li class="px-3 py-4 text-center text-gray-500">
                    لا يوجد عملاء مطابقين
                </li>
            `;
            return;
        }

        clientsToRender.forEach((client) => {
            const listItem = document.createElement("li");
            listItem.className =
                "hover:bg-gray-50 transition-colors duration-150 rounded-md";

            const avatarInitial =
                client.company_name?.charAt(0).toUpperCase() ?? "?";
            const safeCompany = client.company_name ?? "Unknown Company";
            const safeSalesRepName = client.sales_rep_name ?? "Unnamed";

            listItem.innerHTML = `
                <a href="/client/${encodeURIComponent(
                    client.id
                )}/message" class="flex items-center px-3 py-4" aria-label="Open chat with ${safeSalesRepName}">
                    <div class="flex-shrink-0">
                        ${
                            client.avatar
                                ? `<img class="h-10 w-10 rounded-full" src="${client.avatar}" alt="${safeCompany}">`
                                : `<div class="h-10 w-10 rounded-full bg-grey-300 flex items-center justify-center text-purple-600 font-medium">
                                    ${avatarInitial}
                                   </div>`
                        }
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-900">${safeCompany}</p>
                        <p class="text-xs text-gray-500">${safeSalesRepName}</p>
                    </div>
                </a>
            `;

            clientList.appendChild(listItem);
        });
    }

    function filterClients() {
        const term = clientSearch?.value.trim().toLowerCase() ?? "";

        if (!term) {
            renderClientList(clients);
            return;
        }

        const filtered = clients.filter(
            (client) =>
                (client.name ?? "").toLowerCase().includes(term) ||
                (client.company_name ?? "").toLowerCase().includes(term)
        );

        renderClientList(filtered);
    }

    function fetchUnreadConversationsCount() {
        fetch("/chat/unread-count")
            .then((response) => response.json())
            .then((data) => {
                const count = data.unread_conversations_count;
                const badge = document.getElementById("totalUnreadCount");
                const list = document.getElementById("chatDropdownList");

                // تحديث البادج
                if (count > 0) {
                    badge.textContent = count;
                    badge.classList.remove("d-none");
                } else {
                    badge.classList.add("d-none");
                }

                list.innerHTML = "";
                if (data.conversations.length === 0) {
                    list.innerHTML =
                        '<li class="p-4 text-center text-gray-500">لا توجد محادثات حتى الآن</li>';
                    return;
                }

                data.conversations.forEach((convo) => {
                    const item = document.createElement("li");
                    item.className = "border-b hover:bg-gray-50";

                    item.innerHTML = `
                    <a href="${
                        convo.url
                    }" class="flex items-center justify-between px-4 py-3">
                        <div>
                            <div class="font-semibold">${
                                convo.client_name
                            }</div>
                            <small class="text-gray-500">${
                                convo.last_message_time || ""
                            }</small>
                        </div>
                        ${
                            convo.unread_count > 0
                                ? `<span class="bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">${convo.unread_count}</span>`
                                : ""
                        }
                    </a>
                `;

                    list.appendChild(item);
                });
            })
            .catch((error) => {
                console.error("Error fetching unread conversations:", error);
            });
    }

    document.addEventListener(
        "DOMContentLoaded",
        fetchUnreadConversationsCount
    );
    setInterval(fetchUnreadConversationsCount, 30000);
});
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

        // toggle empty-state
        if (emptyState) emptyState.classList.toggle('hidden', visible !== 0);
    }

    // run once on load (helps if browser kept text in the field)
    filterList();

    // filter as user types
    searchInput.addEventListener('input', filterList);
});
