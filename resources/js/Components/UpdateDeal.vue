<template>
    <el-dialog
        width="min(560px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-tag text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Update Deal</h2>
                    <p class="text-xs text-gray-400">Edit the homepage deal section</p>
                </div>
            </div>
        </template>

        <div class="space-y-4">

            <!-- Rich text editor -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Description
                </label>

                <!-- Toolbar -->
                <div class="flex flex-wrap items-center gap-1 px-2 py-1.5 bg-gray-50 border border-gray-200 rounded-t-xl border-b-0">
                    <!-- Headings -->
                    <select
                        @change="onFormat('formatBlock', $event.target.value); $event.target.value = ''"
                        class="text-xs border border-gray-200 rounded-lg px-2 py-1 bg-white text-gray-600 focus:outline-none focus:ring-1 focus:ring-green-500 cursor-pointer"
                    >
                        <option value="">Paragraph</option>
                        <option value="h1">Heading 1</option>
                        <option value="h2">Heading 2</option>
                        <option value="h3">Heading 3</option>
                    </select>

                    <div class="w-px h-5 bg-gray-200 mx-0.5"></div>

                    <!-- Bold -->
                    <button type="button" @mousedown.prevent="onFormat('bold')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors font-bold text-sm">
                        B
                    </button>
                    <!-- Italic -->
                    <button type="button" @mousedown.prevent="onFormat('italic')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors italic text-sm">
                        I
                    </button>
                    <!-- Underline -->
                    <button type="button" @mousedown.prevent="onFormat('underline')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors underline text-sm">
                        U
                    </button>

                    <div class="w-px h-5 bg-gray-200 mx-0.5"></div>

                    <!-- Text color presets -->
                    <div class="flex items-center gap-1">
                        <span class="text-[10px] text-gray-400 mr-0.5">Color:</span>
                        <button
                            v-for="c in colors" :key="c.value"
                            type="button"
                            @mousedown.prevent="onFormat('foreColor', c.value)"
                            :title="c.label"
                            :style="{ backgroundColor: c.value }"
                            class="w-5 h-5 rounded-full border-2 border-white shadow-sm hover:scale-110 transition-transform"
                        ></button>
                    </div>

                    <div class="w-px h-5 bg-gray-200 mx-0.5"></div>

                    <!-- Lists -->
                    <button type="button" @mousedown.prevent="onFormat('insertUnorderedList')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors text-xs">
                        <i class="fas fa-list-ul"></i>
                    </button>
                    <button type="button" @mousedown.prevent="onFormat('insertOrderedList')"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-600 hover:bg-gray-200 transition-colors text-xs">
                        <i class="fas fa-list-ol"></i>
                    </button>

                    <div class="w-px h-5 bg-gray-200 mx-0.5"></div>

                    <!-- Clear formatting -->
                    <button type="button" @mousedown.prevent="onFormat('removeFormat')"
                        title="Clear formatting"
                        class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition-colors text-xs">
                        <i class="fas fa-eraser"></i>
                    </button>
                </div>

                <!-- Editable area -->
                <div
                    ref="editorRef"
                    contenteditable="true"
                    @input="onEditorInput"
                    class="min-h-[160px] max-h-[280px] overflow-y-auto border border-gray-200 rounded-b-xl px-4 py-3 text-sm text-gray-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white leading-relaxed"
                ></div>

                <!-- Live preview -->
                <div class="mt-2">
                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Preview</p>
                    <div
                        v-html="form_data.desc"
                        class="bg-gray-50 border border-gray-100 rounded-xl px-4 py-3 text-sm min-h-[48px] leading-relaxed"
                    ></div>
                </div>
            </div>

            <!-- Date -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                    Deal Expiry Date
                </label>
                <el-date-picker
                    v-model="form_data.date"
                    placeholder="Select expiry date"
                    type="date"
                    clearable
                    style="width: 100%"
                />
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <button
                    @click="showModal = false"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                >Cancel</button>
                <button
                    @click="onSubmitUpdate"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors flex items-center gap-2"
                >
                    <i class="fas fa-check text-xs"></i> Update
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, reactive, watch, onMounted, nextTick } from 'vue';

const setting = usePage().props.setting;

const props = defineProps({
    onClose: { type: Function, default: () => {} },
});

const showModal = ref(true);
const editorRef = ref(null);
const form_errors = reactive({ value: {} });
const form_data = reactive({
    desc: setting.deal_desc ?? '',
    date: setting.deal_date_until,
});

const colors = [
    { label: 'Black',  value: '#111827' },
    { label: 'Green',  value: '#16a34a' },
    { label: 'Lime',   value: '#65a30d' },
    { label: 'Blue',   value: '#2563eb' },
    { label: 'Red',    value: '#dc2626' },
    { label: 'Orange', value: '#ea580c' },
    { label: 'Gray',   value: '#6b7280' },
    { label: 'White',  value: '#ffffff' },
];

// Populate editor with existing HTML on mount
onMounted(() => {
    nextTick(() => {
        if (editorRef.value) {
            editorRef.value.innerHTML = form_data.desc ?? '';
        }
    });
});

watch(showModal, val => { if (!val) props.onClose(); });

function onEditorInput() {
    form_data.desc = editorRef.value?.innerHTML ?? '';
}

function onFormat(command, value = null) {
    document.execCommand(command, false, value);
    editorRef.value?.focus();
    // sync after formatting
    nextTick(() => {
        form_data.desc = editorRef.value?.innerHTML ?? '';
    });
}

function onSubmitUpdate() {
    axios.post(route('setting.update-deal'), {
        desc: form_data.desc,
        date: form_data.date,
    })
    .then(() => {
        router.visit(window.location.href, { preserveScroll: true });
    })
    .catch(error => {
        form_errors.value = error.response.data.errors;
    });
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }

/* Editor content styles */
[contenteditable] h1 { font-size: 1.5rem; font-weight: 800; line-height: 1.2; }
[contenteditable] h2 { font-size: 1.25rem; font-weight: 700; line-height: 1.3; }
[contenteditable] h3 { font-size: 1.1rem; font-weight: 600; line-height: 1.4; }
[contenteditable] ul { list-style: disc; padding-left: 1.25rem; }
[contenteditable] ol { list-style: decimal; padding-left: 1.25rem; }
[contenteditable]:empty:before {
    content: 'Write your deal description here...';
    color: #9ca3af;
    pointer-events: none;
}
</style>
