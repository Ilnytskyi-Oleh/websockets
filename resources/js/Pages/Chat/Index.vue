<script setup>
import {Head} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import NavLink from "@/Components/NavLink.vue";


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
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    Users
                </div>
            </div>
            <div class="w-full">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    Chats
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>

</style>
