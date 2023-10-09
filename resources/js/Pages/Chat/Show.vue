<script setup>
import {
    Head, router, usePage,
} from "@inertiajs/vue3";

import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NavLink from "@/Components/NavLink.vue";
import {ref} from "vue";

const {chat} = defineProps({
    chat: Object,
    users: Object,
})

let newMessageBody = ref('');

const store = () => {
    axios.post(route('message.store'), {
        chat_id: chat.id,
        body: newMessageBody.value,
    }).then(res => {
        console.log(res)
    });

    // axios.post(route('message.store'), {
    //   chat_id: chat.id,
    //   body: newMessageBody.value,
    // })
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
                        </div>
                    </div>
                </div>
            </div>
            <div class="w-full">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h2 class="text-2xl text-gray-600 mb-4">Chat</h2>
                    <div>
                        <div >
                            <div>

                            </div>
                            <div>
                                <input
                                    v-model="newMessageBody"
                                    @keyup.enter="store"
                                    type="text"
                                    class="w-full border border-1 rounded border-gray-300 mb-4"
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
