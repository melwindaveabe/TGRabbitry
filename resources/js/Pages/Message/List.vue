<template>
    <AuthLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-800">Messages</h1>
            <p class="text-sm text-gray-500 mt-0.5">Customer conversations</p>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="px-5 py-3 border-b border-gray-100 flex items-center justify-between">
                <span class="text-sm font-semibold text-gray-600">All Conversations</span>
                <span class="bg-green-100 text-green-700 text-xs font-semibold px-2.5 py-1 rounded-full">
                    {{ data.length }}
                </span>
            </div>

            <!-- Empty state -->
            <div v-if="data.length === 0" class="flex flex-col items-center justify-center py-20 text-gray-400">
                <i class="fas fa-comments text-5xl mb-4 opacity-30"></i>
                <p class="text-sm font-medium">No conversations yet</p>
            </div>

            <!-- Conversation list -->
            <div v-else class="divide-y divide-gray-50">
                <div
                    v-for="d in data"
                    :key="d.id"
                    class="flex items-center gap-4 px-5 py-4 hover:bg-gray-50 cursor-pointer transition-colors group"
                    @click="() => onClick(d.id)"
                >
                    <!-- Avatar -->
                    <div class="relative flex-shrink-0">
                        <div class="size-11 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold text-sm shadow-sm">
                            {{ d.name.charAt(0).toUpperCase() }}
                        </div>
                        <!-- Unread dot -->
                        <span
                            v-if="d.user_messages_count > 0"
                            class="absolute -top-0.5 -right-0.5 size-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center"
                        >{{ d.user_messages_count > 9 ? '9+' : d.user_messages_count }}</span>
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="font-semibold text-gray-800 text-sm truncate">{{ d.name }}</div>
                        <div class="text-xs text-gray-400 mt-0.5 truncate">
                            {{ d.email ?? 'Click to view conversation' }}
                        </div>
                    </div>

                    <!-- Arrow -->
                    <i class="fas fa-chevron-right text-xs text-gray-300 group-hover:text-green-500 transition-colors flex-shrink-0"></i>
                </div>
            </div>
        </div>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { router } from '@inertiajs/vue3';

const { data } = defineProps(['data']);

function onClick(id) {
    router.get(route('admin.message.index', id));
}
</script>
