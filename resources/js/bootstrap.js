import axios from "axios";
window.axios = axios;
window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

// Echo and Pusher setup
import Echo from "laravel-echo";
import Pusher from "pusher-js";
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? "mt1",
    wsHost: import.meta.env.VITE_PUSHER_HOST
        ? import.meta.env.VITE_PUSHER_HOST
        : `ws-${import.meta.env.VITE_PUSHER_APP_CLUSTER}.pusher.com`,
    wsPort: import.meta.env.VITE_PUSHER_PORT ?? 80,
    wssPort: import.meta.env.VITE_PUSHER_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? "http") !== "http",
    enabledTransports: ["ws", "wss"],
});

// New client created
const userIdMeta = document.querySelector('meta[name="user-id"]');
const userId = userIdMeta ? userIdMeta.getAttribute("content") : null;

if (userId) {
    // ✅ Approved Requests
    window.Echo.private(`client.request.approved.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "success",
                title: "✅ تم قبول الطلب",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب الموافق عليه
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );
	  window.Echo.private(`salesrep-login-ip`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "success",
                title: "✅ تم قبول الطلب",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب الموافق عليه
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );
          window.Echo.private(`birthday`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "success",
                title: "✅ تم قبول الطلب",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب الموافق عليه
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );
    // ❌ Rejected Requests
    window.Echo.private(`client.request.rejected.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "error",
                title: "❌ تم رفض الطلب",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب المرفوض
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );

    // 📨 New Edit Request Notification (Admin Only)
    window.Echo.private(`client.request.sent.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "info",
                title: "طلب تعديل جديد",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );

    window.Echo.private(`agreement.request.approved.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "success",
                title: "✅ تم قبول الطلب",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب الموافق عليه
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );

    // ❌ Rejected Requests
    window.Echo.private(`agreement.request.rejected.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "error",
                title: "❌ تم رفض الطلب",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب المرفوض
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );

    // 📨 New Edit Request Notification (Admin Only)
    window.Echo.private(`agreement.request.sent.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "info",
                title: "طلب تعديل إتفاقية جديد",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض الطلب
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );

    // 🔔 Your existing new-client channel
    window.Echo.private(`new-client.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "success",
                title: "✅ عميل جديد",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض العميل
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );
    // new target
    window.Echo.private(`target.achieved.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            Toast.fire({
                icon: "success",
                title: "✅ تارجت جديد متحقق",
                html: `<div>
                    <p>${data.message}</p>
                    <a href="${data.url}" class="text-blue-500 hover:underline">
                        عرض التارجت
                    </a>
                </div>`,
                timer: 8000,
            });
        }
    );
    //agreement notice
    window.Echo.private(`agreement.notice.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            console.log(
                "⏰  Agreement Notice Periode Started notification:",
                data
            );

            Toast.fire({
                icon: "success",
                title: "⏰  Agreement Noticed Period",
                html: `<div>
                <p>${data.message}</p>
                <a href="${data.url}" class="text-blue-500 hover:underline">
                    View Agreement
                </a>
            </div>`,
                timer: 8000,
            });
        }
    );
    //late customer
    window.Echo.private(`late.customer.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            console.log("⏳ Late Customer Notification:", data);

            Toast.fire({
                icon: "warning", // or "info" depending on tone
                title: "⏳ Late Customer Alert",
                html: `<div>
                <p>${data.message}</p>
                <a href="${data.url}" class="text-blue-500 hover:underline">
                    View Customer
                </a>
            </div>`,
                timer: 8000,
            });
        }
    );
    window.Echo.private(`pended-request.notice.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            console.log("⏳ Pended Request Notification:", data);

            Toast.fire({
                icon: "warning", // or "info" depending on tone
                title: "⏳ Pended Request Alert",
                html: `<div>
                <p>${data.message}</p>
                <a href="${data.url}" class="text-blue-500 hover:underline">
                    View Requests
                </a>
            </div>`,
                timer: 8000,
            });
        }
    );
    // New Agreement
    window.Echo.private(`new-agreement.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            console.log("🎯 Target achieved notification:", data);

            Toast.fire({
                icon: "success",
                title: "🎯 Target Achieved",
                html: `<div>
                <p>${data.message}</p>
                <a href="${data.url}" class="text-blue-500 hover:underline">
                    View Target
                </a>
            </div>`,
                timer: 8000,
            });
        }
    );

    window.Echo.private(`agreement.renewed.admin`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            console.log("🎯 Target achieved notification:", data);

            Toast.fire({
                icon: "success",
                title: "🎯 Agreement Renewed",
                html: `<div>
                <p>${data.message}</p>
                <a href="${data.url}" class="text-blue-500 hover:underline">
                    View Target
                </a>
            </div>`,
                timer: 8000,
            });
        }
    );
    //agreement renewed
    window.Echo.channel(`agreement-renewed.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (data) => {
            const urgencyClass =
                data.urgency === "high"
                    ? "bg-red-100 text-red-800"
                    : "bg-yellow-100 text-yellow-800";

            Toast.fire({
                icon: data.urgency === "high" ? "error" : "warning",
                title: data.title,
                html: `<div>
                <p>${data.message}</p>
                <p class="mt-2 text-sm ${urgencyClass} rounded px-2 py-1">
                    ${data.days_left} days remaining
                </p>
                <a href="${data.url}" class="mt-2 inline-block text-blue-600 hover:underline">
                    View Agreement
                </a>
            </div>`,
                timer: 8000,
            });
        }
    );
window.Echo.private(`chat.${userId}`).listen(
        ".Illuminate\\Notifications\\Events\\BroadcastNotificationCreated",
        (e) => {
             //alert("Receiver got a message!\nMessage ID: " + e.message_id);
            Livewire.dispatch("refresh");
        }
    );
} else {
    console.warn("User ID meta tag not found.");
}


Echo.private(`participant.${encodedType}.${userId}`).listen(
    ".Namu\\WireChat\\Events\\NotifyParticipant",
    (e) => {
        console.log(e);
    }
);
// Pusher error handler
window.Echo.connector.pusher.connection.bind("error", (err) => {
    console.error("🚨 Pusher error:", err);

    Toast.fire({
        icon: "error",
        title: "Connection Error",
        text: "Realtime updates may not work",
    });
});

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allow your team to quickly build robust real-time web applications.
 */

import "./echo";
