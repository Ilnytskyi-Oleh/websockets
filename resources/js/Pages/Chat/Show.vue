<script setup>
import {
    Head, router, usePage,
} from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import {onBeforeMount, ref} from "vue";
import Message from "@/Components/Message.vue";

const { chat, messages } = defineProps({
    chat: Object,
    users: Array,
    messages: Array,
    isLastPage: Boolean
})

const page = usePage();

onBeforeMount(() => {
    window.Echo.channel(`store-message.${chat.id}`)
        .listen('.store-message', res => {
            messages.push(res.message)

            if (page.url === `/chats/${chat.id}`) {
                axios.patch('/message_statuses', {
                    'message_id': res.message.id
                })
            }

        })
})

let newMessageBody = ref('');
let isSending = ref(false);
let params = new URLSearchParams(window.location.search)
let currentPage = ref( 1);

const store = () => {
    if (isSending.value) {
        return;
    }

    isSending.value = true;

    axios.post(route('message.store'), {
        chat_id: chat.id,
        body: newMessageBody.value,
    })
    .then(res => {
        messages.push(res.data)
        newMessageBody.value = '';
    })
    .finally(() => {
        isSending.value = false;
    });
}

const loadMore = () => {

    axios.get(route('chats.show', {
        page: ++currentPage.value,
        chat: chat.id
    }))
        .then(res => {
            messages.unshift(...res.data.messages)
            page.props.isLastPage = res.data.is_last_page
        })
}
</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>
        <template #header>
            <!--            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Chat</h2>-->
            <ul class="flex">
                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                    Link
                </NavLink>

            </ul>
        </template>

        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 flex flex-col md:flex-row gap-x-4 lg:gap-x-6">
            <div class="w-full ">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg space-y-2">
                    <h2 class="text-2xl text-gray-600 mb-4">Users</h2>
                    <div
                        v-if="users"
                        v-for="user in users"
                        :key="user.id"
                    >
                        <div class="flex border border-1 rounded overflow-hidden hover:bg-gray-100">
                            <div class="py-1 pl-3">{{ user.name }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h2 class="text-2xl text-gray-600 mb-4">Chat</h2>
                    <div>
                        <div >
                            <div v-if="!!messages.length" class="px-3 py-2 border border-1 rounded border-gray-200 mb-4">
                                <div class="text-center">
                                    <a
                                        @click.prevent="loadMore"
                                        href="#"
                                        v-if="!isLastPage"
                                        class="inline-block underline text-gray-500  tracking-wide hover:text-gray-600"
                                    >
                                        Load more...
                                    </a>
                                </div>
                                <div v-for="message in messages" :key="message.id">
                                    <Message :message="message"/>
                                </div>
                            </div>
                            <div>
                                <input
                                    v-model="newMessageBody"
                                    @keyup.enter="store"
                                    type="text"
                                    class="w-full border border-1 rounded border-gray-200 mb-4"
                                >
                                <div class="flex">
                                    <button
                                        @click.prevent="store"
                                        class="ml-auto py-1 text-white px-3 bg-sky-500 hover:bg-sky-400 rounded-lg"
                                    >
                                        Send
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
