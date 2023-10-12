<script setup>
const props = defineProps({
    message: {
        type: Object,
        required: true
    },
});
</script>

<template>
    <div>
        <div class="px-3 py-1 border border-1 rounded border-gray-200 max-w-max mb-4"
             :class="{'ml-auto': message.isOwner}"
        >
            <div class="text-xs text-sky-500" v-if="!message.isOwner">
                {{message.user.name}}:
            </div>
            <div>
                {{message.body}}
            </div>

            <div class="text-xs text-gray-400 text-right italic mt-2">
                <hr>
                {{ message.time }}
            </div>
        </div>
    </div>
</template>
