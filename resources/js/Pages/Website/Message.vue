<template>
    <GuestLayout>
        <input
            type="file"
            style="display: none;"
            id="up-img"
            accept="image/*"
            multiple
            @change="onSendImg"
        >

        <div class="max-w-5xl mx-auto px-4 py-6">
            <!-- Chat container -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">

                <!-- Chat header -->
                <div class="bg-gradient-to-r from-green-600 to-green-500 px-5 py-4 flex items-center gap-3">
                    <div class="relative">
                        <img
                            class="rounded-full size-12 border-2 border-white shadow-md object-cover"
                            :src="`/images/website/${$page.props.setting.logo}`"
                            alt=""
                        >
                        <div
                            v-if="last_online_at === 'Online'"
                            class="absolute bottom-0 right-0 size-3 bg-green-400 border-2 border-white rounded-full"
                        ></div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-white text-base">
                            {{ $page.props.setting.name }} Seller
                        </div>
                        <div class="text-white/80 text-xs flex items-center gap-1.5">
                            <i v-if="last_online_at === 'Online'" class="fas fa-circle text-[6px] text-green-300"></i>
                            {{ last_online_at }}
                        </div>
                    </div>
                </div>

                <!-- Messages area -->
                <div ref="messageRef" class="h-[60vh] overflow-y-auto p-5 bg-gray-50 space-y-3">
                    <template v-for="mess in data.value" :key="mess.id">
                        <div :class="`flex ${mess.admin_id ? 'justify-start' : 'justify-end'}`">
                            <div :class="`max-w-[75%] ${mess.admin_id ? '' : 'flex flex-col items-end'}`">
                                
                                <!-- Message bubble -->
                                <div
                                    :class="`rounded-2xl px-4 py-2.5 shadow-sm
                                        ${mess.admin_id
                                            ? 'bg-white border border-gray-200 text-gray-800 rounded-tl-none'
                                            : 'bg-green-600 text-white rounded-tr-none'
                                        }
                                        ${!mess.body ? 'p-2' : ''}
                                    `"
                                >
                                    <!-- Text message -->
                                    <div v-if="mess.body" class="text-sm leading-relaxed break-words">
                                        {{ mess.body }}
                                    </div>

                                    <!-- Image grid -->
                                    <div
                                        v-else
                                        :class="`grid gap-2 ${getGridCount(mess.images.length)}`"
                                    >
                                        <img
                                            v-for="(img, idx) in mess.images"
                                            :key="idx"
                                            loading="lazy"
                                            @click="previewSrc = $page.props.base_img_path + 'messages/' + img"
                                            :src="$page.props.base_img_path + 'messages/' + img"
                                            class="rounded-lg w-full h-auto cursor-pointer hover:opacity-90 transition-opacity"
                                            alt=""
                                        >
                                    </div>
                                </div>

                                <!-- Timestamp -->
                                <div
                                    v-if="mess.created_at"
                                    class="text-[10px] text-gray-400 mt-1 px-1"
                                >
                                    {{ formatTime(mess.created_at) }}
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty state -->
                    <div v-if="data.value.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
                        <i class="fas fa-comments text-5xl mb-3 opacity-30"></i>
                        <p class="text-sm">No messages yet. Start the conversation!</p>
                    </div>
                </div>

                <!-- Input area -->
                <div class="bg-white border-t border-gray-200 px-4 py-3 flex items-end gap-3">
                    <button
                        @click="onClickImg"
                        class="flex-shrink-0 size-10 rounded-full bg-gray-100 hover:bg-gray-200 text-green-600 flex items-center justify-center transition-colors"
                        title="Attach image"
                    >
                        <i class="fas fa-image text-lg"></i>
                    </button>

                    <textarea
                        v-model="body"
                        placeholder="Type your message..."
                        @keydown.enter.exact.prevent="onSend"
                        rows="1"
                        class="flex-1 resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent max-h-32 overflow-y-auto"
                    ></textarea>

                    <button
                        v-if="body && body.trim() !== ''"
                        @click="onSend"
                        class="flex-shrink-0 size-10 rounded-full bg-green-600 hover:bg-green-700 text-white flex items-center justify-center transition-colors shadow-md"
                    >
                        <i class="fas fa-paper-plane text-sm"></i>
                    </button>
                </div>
            </div>
        </div>
    </GuestLayout>

    <ImagePreview v-if="previewSrc" :src="previewSrc" :on-close="() => previewSrc = null" />
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import axios from 'axios';
import { ref, reactive, onBeforeMount, watch, nextTick } from 'vue';
import ImagePreview from '@/Components/ImagePreview.vue';

const pusher = new Pusher('428b6a4d7d3fa0415c84', { cluster: 'ap1' });
const channel = pusher.subscribe('my-channel');
channel.bind('my-event', function () {
    fetchData();
});

const data = reactive({ value: [] });
const body = ref(null);
const previewSrc = ref(null);
const messageRef = ref(null);
const last_online_at = ref(null);

onBeforeMount(fetchData);

watch(data, () => {
    nextTick(scrollToBottom);
}, { deep: true });

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / (1000 * 60 * 60));

    if (hours < 24) {
        return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' ' + date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function getLastOnline(last_online) {
    const last_online_date = new Date(last_online);
    const current_date = new Date();
    const timediff = current_date - last_online_date;

    const years = Math.floor(timediff / (1000 * 60 * 60 * 24 * 30 * 365));
    if (years > 0) return `Active ${years} year${years > 1 ? 's' : ''} ago`;

    const months = Math.floor(timediff / (1000 * 60 * 60 * 24 * 30));
    if (months > 0) return `Active ${months} month${months > 1 ? 's' : ''} ago`;

    const days = Math.floor(timediff / (1000 * 60 * 60 * 24));
    if (days > 0) return `Active ${days} day${days > 1 ? 's' : ''} ago`;

    const hours = Math.floor(timediff / (1000 * 60 * 60));
    if (hours > 0) return `Active ${hours} hour${hours > 1 ? 's' : ''} ago`;

    const minutes = Math.floor(timediff / (1000 * 60));
    if (minutes > 0) return `Active ${minutes} minute${minutes > 1 ? 's' : ''} ago`;

    return 'Online';
}

function fetchData() {
    axios.get(route('message.get'))
        .then(res => {
            last_online_at.value = getLastOnline(res.data.last_online_at);
            data.value = res.data.messages.map(val => ({
                ...val,
                images: val.images ? val.images.split('|') : []
            }));
        });
}

function onClickImg() {
    document.querySelector('#up-img').click();
}

function onSendImg(e) {
    axios.post(route('message.send-image'), { images: e.target.files }, {
        headers: { 'content-type': 'multipart/form-data' }
    })
        .finally(() => {
            const f = document.querySelector('#up-img');
            f.value = null;
            f.files = new DataTransfer().files;
            body.value = null;
        });
}

function getGridCount(count) {
    if (count > 2) return 'grid-cols-3';
    if (count > 1) return 'grid-cols-2';
    return '';
}

function onSend() {
    if (!body.value || body.value.trim() === '') return;
    axios.post(route('message.send-message'), { body: body.value })
        .finally(() => {
            body.value = null;
        });
}

function scrollToBottom() {
    if (!messageRef.value) return;
    messageRef.value.scrollTop = messageRef.value.scrollHeight + 1500;
}
</script>
