<template>
    <input
        type="file"
        style="display: none;"
        id="up-img"
        accept="image/*"
        multiple
        @change="onSendImg"
    >

    <AuthLayout>
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                <!-- Chat header -->
                <div class="bg-gradient-to-r from-green-600 to-green-500 px-5 py-4 flex items-center gap-3">
                    <div class="relative">
                        <div class="size-11 rounded-full bg-white/20 border-2 border-white/40 flex items-center justify-center text-white font-bold text-sm">
                            {{ getUserProfileAbbr() }}
                        </div>
                        <div
                            v-if="last_online_at === 'Online'"
                            class="absolute bottom-0 right-0 size-3 bg-green-300 border-2 border-white rounded-full"
                        ></div>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-white">{{ _user.name }}</div>
                        <div class="text-white/70 text-xs flex items-center gap-1.5 mt-0.5">
                            <i v-if="last_online_at === 'Online'" class="fas fa-circle text-[6px] text-green-300"></i>
                            {{ last_online_at }}
                        </div>
                    </div>
                </div>

                <!-- Messages area -->
                <div ref="messageRef" class="h-[60vh] overflow-y-auto p-5 bg-gray-50 space-y-3">
                    <template v-for="mess in data.value" :key="mess.id">
                        <div :class="`flex ${mess.admin_id ? 'justify-end' : 'justify-start'}`">
                            <div :class="`max-w-[75%] ${mess.admin_id ? 'flex flex-col items-end' : ''}`">

                                <!-- Bubble -->
                                <div
                                    :class="`rounded-2xl px-4 py-2.5 shadow-sm
                                        ${mess.admin_id
                                            ? 'bg-green-600 text-white rounded-tr-none'
                                            : 'bg-white border border-gray-200 text-gray-800 rounded-tl-none'
                                        }
                                        ${!mess.body ? 'p-2' : ''}
                                    `"
                                >
                                    <div v-if="mess.body" class="text-sm leading-relaxed break-words">
                                        {{ mess.body }}
                                    </div>

                                    <div v-else :class="`grid gap-2 ${getGridCount(mess.images.length)}`">
                                        <img
                                            v-for="(img, idx) in mess.images"
                                            :key="idx"
                                            loading="lazy"
                                            @click="previewSrc = $page.props.base_img_path + 'messages/' + img"
                                            :src="$page.props.base_img_path + 'messages/' + img"
                                            class="rounded-lg w-full cursor-pointer hover:opacity-90 transition-opacity"
                                            alt=""
                                        >
                                    </div>

                                    <Link v-if="mess.order_id" :href="route('order.details.show', mess.order_id)">
                                        <div class="text-xs mt-2 underline cursor-pointer"
                                            :class="mess.admin_id ? 'text-white/80 hover:text-white' : 'text-blue-600 hover:text-blue-400'">
                                            <i class="fas fa-box text-[10px] mr-1"></i> View Order
                                        </div>
                                    </Link>
                                </div>

                                <!-- Timestamp -->
                                <div v-if="mess.created_at" class="text-[10px] text-gray-400 mt-1 px-1">
                                    {{ formatTime(mess.created_at) }}
                                </div>
                            </div>
                        </div>
                    </template>

                    <!-- Empty state -->
                    <div v-if="data.value.length === 0" class="flex flex-col items-center justify-center h-full text-gray-400">
                        <i class="fas fa-comments text-5xl mb-3 opacity-30"></i>
                        <p class="text-sm">No messages yet.</p>
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
                        placeholder="Type a message..."
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
    </AuthLayout>

    <ImagePreview v-if="previewSrc" :src="previewSrc" :on-close="() => previewSrc = null" />
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import axios from 'axios';
import { ref, reactive, onBeforeMount, watch, nextTick, onBeforeUnmount } from 'vue';
import ImagePreview from '@/Components/ImagePreview.vue';
import { Link } from '@inertiajs/vue3';

const { _user } = defineProps(['_user']);

const pusher = new Pusher('428b6a4d7d3fa0415c84', { cluster: 'ap1' });
const channel = pusher.subscribe('my-channel');
channel.bind('my-event', function () { fetchData(); });

const data = reactive({ value: [] });
const body = ref(null);
const messageRef = ref(null);
const previewSrc = ref(null);
const last_online_at = ref(null);

onBeforeMount(fetchData);
onBeforeUnmount(() => { channel.unsubscribe('my-channel'); });

watch(data, () => { nextTick(scrollToBottom); }, { deep: true });

function getUserProfileAbbr() {
    return _user.name.trim().split(' ').map(n => n[0]).join('').toUpperCase();
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    const now = new Date();
    const diff = now - date;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    if (hours < 24) return date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric' }) + ' ' +
        date.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
}

function getLastOnline(last_online) {
    const diff = new Date() - new Date(last_online);
    const years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365));
    if (years > 0) return `Active ${years}y ago`;
    const months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30));
    if (months > 0) return `Active ${months}mo ago`;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    if (days > 0) return `Active ${days}d ago`;
    const hours = Math.floor(diff / (1000 * 60 * 60));
    if (hours > 0) return `Active ${hours}h ago`;
    const minutes = Math.floor(diff / (1000 * 60));
    if (minutes > 0) return `Active ${minutes}m ago`;
    return 'Online';
}

function fetchData() {
    axios.get(route('admin.message.get', _user.id))
        .then(res => {
            last_online_at.value = getLastOnline(res.data.last_online_at);
            data.value = res.data.messages.map(val => ({
                ...val,
                images: val.images ? val.images.split('|') : []
            }));
        });
}

function onClickImg() { document.querySelector('#up-img').click(); }

function onSendImg(e) {
    axios.post(route('admin.message.send-image'), { images: e.target.files, user_id: _user.id }, {
        headers: { 'content-type': 'multipart/form-data' }
    }).finally(() => {
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
    axios.post(route('admin.message.send-message'), { body: body.value, user_id: _user.id })
        .finally(() => { body.value = null; });
}

function scrollToBottom() {
    if (!messageRef.value) return;
    messageRef.value.scrollTop = messageRef.value.scrollHeight;
}
</script>
