<script setup>
import {Head, router, Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NavLink from "@/Components/NavLink.vue";

defineProps({
    users: Object,
    chats: Object,
})

const store = (userId) => {
    router.post('/chats', {
        title: null,
        users: [
            userId
        ]
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

        <div class="max-w-7xl mx-auto p-4 sm:p-6 lg:p-8 flex gap-x-4 lg:gap-x-6">
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
                            <div class="ml-auto bg-indigo-400 hover:bg-indigo-500 text-white ">
                                <a class="inline-block  py-1 pl-2 pr-3" href="#"
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
                                {{`${chat.id} -- ${chat.title ?? 'Chat'}` }}
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
