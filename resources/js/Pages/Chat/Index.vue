<script setup>
import {Head, router, Link, usePage} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NavLink from "@/Components/NavLink.vue";

import {ref} from "vue";

const isMakingGroup = ref(false);
const selectedUserIds = ref([]);
const title = ref('');
const page = usePage();

defineProps({
    users: Array,
    chats: Array,
})

window.Echo.private(`users.${page.props.auth.user.id}`)
    .listen('.store-message-status', res => {
        page.props.chats.filter(chat => {
            if (chat.id === res.chat_id) {
                chat.unread_count = res.count;
                chat.last_message = res.message;
            }
        })
    });

const toggleSelectedUserIds = (id) => {
    if(!isMakingGroup.value) return;

    if (selectedUserIds.value.includes(id)) {
        selectedUserIds.value = selectedUserIds.value.filter(
            user => user.id !== id.id
        );
        return;
    }

    selectedUserIds.value.push(id);
}

const isSelectedUser = (id) => {
    return !!selectedUserIds.value.includes(id);
}

const isCurrentUserMessage = (message) => {
    return message.user.id === page.props.auth.user.id
}

const cancelMakingGroup = () => {
    isMakingGroup.value = false;
    selectedUserIds.value = [];
}

const store = (userId) => {
    router.post('/chats', {
        title: null,
        users: [
            userId
        ]
    });
}

const storeGroup = () => {
    router.post('/chats', {
        title: title.value,
        users: selectedUserIds.value,
    });
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
                    <div class="flex justify-between">
                        <h2 class="text-2xl text-gray-600">Users</h2>
                        <button
                            v-if="!isMakingGroup && !!users.length"
                            @click="isMakingGroup = true"
                            class="px-3 bg-indigo-400 hover:bg-indigo-500 text-white rounded"
                        >
                            Make a group
                        </button>
                        <div v-if="isMakingGroup" class="space-x-2 flex justify-end">
                            <input
                                type="text"
                                class="w-1/4"
                                placeholder="Title"
                                v-model="title">
                            <button
                                @click.prevent="storeGroup"
                                class="px-3 py-1 bg-indigo-400 hover:bg-indigo-500 text-white rounded"
                            >
                                Create group
                            </button>
                            <button
                                @click="cancelMakingGroup"
                                class="px-3 py-1 bg-red-400 hover:bg-red-500 text-white rounded"
                            >
                                Cancel
                            </button>
                        </div>
                    </div>

                    <div
                        v-if="users"
                        v-for="user in users"
                        :key="user.id"
                    >
                        <div
                            :class="['flex border border-1 rounded overflow-hidden',
                                {'cursor-pointer': isMakingGroup },
                                {'bg-green-100 hover:none': isSelectedUser(user.id)},
                            ]"
                             @click.stop.prevent="toggleSelectedUserIds(user.id)"
                        >
                            <div
                                :class="['py-1 pl-3']"
                            >
                                {{ user.name }}
                            </div>
                            <div
                                v-if="!isMakingGroup"
                                class="ml-auto bg-indigo-400 hover:bg-indigo-500 text-white "
                            >
                                <a class="inline-block py-1 pl-2 pr-3" href="#"
                                   @click.prevent="store(user.id)"
                                >
                                    Message
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h2 class="text-2xl text-gray-600 mb-4">Chats</h2>
                    <div
                        v-if="chats"
                        v-for="chat in chats"
                        :key="chat.id"
                    >
                        <div class="flex border border-1 rounded overflow-hidden hover:bg-gray-100">
                            <Link :href="route('chats.show', chat.id)" class="inline-block w-full pl-3 py-1">
                                {{`${chat.id} -- ${chat.title ?? 'My Chat'}` }}
                                <span v-if="chat?.unread_count"
                                      class="bg-sky-500 rounded-full px-1.5 py-0.5 text-white text-xs ml-4"
                                >
                                    {{ chat.unread_count}}
                                </span>
                                <div class="flex text-xs">
                                    <span
                                        v-if="!isCurrentUserMessage(chat.last_message)"
                                        class="mr-2 text-sky-500"
                                    >
                                        {{chat.last_message.user.name}}:
                                    </span>
                                    <span class=" text-gray-500">
                                        {{chat.last_message.body}}
                                    </span>
                                    <div class="italic ml-auto text-gray-400 mr-3">
                                        {{chat.last_message.time}}
                                    </div>
                                </div>

                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
