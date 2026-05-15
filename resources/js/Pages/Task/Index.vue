<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Rabbit Care Tasks</h1>
                <p class="text-sm text-gray-500 mt-0.5">Schedule and track care activities</p>
            </div>
            <button
                @click="onNew"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i> Add Task
            </button>
        </div>

        <!-- Desktop table -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <el-table :data="data.value" row-key="id" header-row-class-name="thead-light">
                <el-table-column min-width="120px" label="Rabbit">
                    <template v-slot="{ row }"><span class="text-sm font-medium text-gray-800">{{ row.rabbit.name }}</span></template>
                </el-table-column>
                <el-table-column min-width="110px" label="Title">
                    <template v-slot="{ row }">
                        <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full ${getTitleClass(row.title)}`">{{ row.title }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="180px" label="Description">
                    <template v-slot="{ row }"><span class="text-sm text-gray-500">{{ row.desc }}</span></template>
                </el-table-column>
                <el-table-column min-width="140px" label="Priority">
                    <template v-slot="{ row }">
                        <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full ${getPriorityClass(row.priority)}`">{{ row.priority }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="110px" label="Due Date">
                    <template v-slot="{ row }"><span class="text-sm text-gray-600">{{ row.due_date.substring(0, 10) }}</span></template>
                </el-table-column>
                <el-table-column width="130px" label="Action" fixed="right">
                    <template v-slot="{ row }">
                        <div class="flex gap-1">
                            <button @click="onComplete(row.id)" class="size-8 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 flex items-center justify-center transition-colors" title="Complete">
                                <i class="fas fa-check text-xs"></i>
                            </button>
                            <button @click="onEdit(row)" class="size-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors" title="Edit">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button @click="onDelete(row.id)" class="size-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors" title="Delete">
                                <i class="fas fa-trash text-xs"></i>
                            </button>
                        </div>
                    </template>
                </el-table-column>
            </el-table>
            <div class="flex justify-end px-4 py-3 border-t border-gray-100">
                <el-pagination
                    v-model:currentPage="pagination.page"
                    v-model:page-size="pagination.page_size"
                    :page-sizes="[5, 10, 50, 100]"
                    background layout="total, sizes, prev, pager, next"
                    :total="pagination_total"
                    @size-change="fetchData" @current-change="fetchData"
                />
            </div>
        </div>

        <!-- Mobile cards -->
        <div class="md:hidden space-y-3">
            <div
                v-for="row in data.value"
                :key="row.id"
                class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4"
            >
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div>
                        <p class="font-semibold text-gray-800 text-sm">{{ row.rabbit.name }}</p>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span :class="`text-xs font-semibold px-2 py-0.5 rounded-full ${getTitleClass(row.title)}`">{{ row.title }}</span>
                            <span :class="`text-xs font-semibold px-2 py-0.5 rounded-full ${getPriorityClass(row.priority)}`">{{ row.priority }}</span>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="text-xs text-gray-400 flex items-center gap-1 justify-end">
                            <i class="fas fa-calendar text-[10px]"></i>
                            {{ row.due_date.substring(0, 10) }}
                        </p>
                    </div>
                </div>
                <p v-if="row.desc" class="text-xs text-gray-500 mb-3">{{ row.desc }}</p>
                <div class="flex justify-end gap-2">
                    <button @click="onComplete(row.id)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 hover:bg-green-100 text-green-600 text-xs font-medium transition-colors">
                        <i class="fas fa-check text-[10px]"></i> Done
                    </button>
                    <button @click="onEdit(row)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium transition-colors">
                        <i class="fas fa-pencil text-[10px]"></i> Edit
                    </button>
                    <button @click="onDelete(row.id)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-medium transition-colors">
                        <i class="fas fa-trash text-[10px]"></i>
                    </button>
                </div>
            </div>
            <div class="flex justify-center pt-2">
                <el-pagination
                    v-model:currentPage="pagination.page"
                    v-model:page-size="pagination.page_size"
                    background layout="prev, pager, next"
                    :total="pagination_total"
                    @current-change="fetchData" small
                />
            </div>
        </div>
    </AuthLayout>

    <!-- Task form dialog -->
    <el-dialog
        width="min(480px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-heart-pulse text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ form_data.id ? 'Update' : 'New' }} Task</h2>
                    <p class="text-xs text-gray-400">Schedule a rabbit care activity</p>
                </div>
            </div>
        </template>

        <div class="space-y-4">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Rabbit <span class="text-red-500">*</span></label>
                <el-select class="w-full" v-model="form_data.rabbit_id" placeholder="Select rabbit">
                    <el-option v-for="val in data.rabbits" :label="val.name" :value="val.id" :key="val.id" />
                </el-select>
                <p v-if="form_errors.value.rabbit_id" class="text-red-500 text-xs mt-1">{{ form_errors.value.rabbit_id[0] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Title</label>
                    <el-select class="w-full" v-model="form_data.title" placeholder="Select title">
                        <el-option v-for="v in ['Feeding','Grooming','Check Up']" :label="v" :value="v" :key="v" />
                    </el-select>
                    <p v-if="form_errors.value.title" class="text-red-500 text-xs mt-1">{{ form_errors.value.title[0] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Priority <span class="text-red-500">*</span></label>
                    <el-select class="w-full" v-model="form_data.priority" placeholder="Select priority">
                        <el-option v-for="v in ['Essential','Moderately Needed','Highly Needed']" :label="v" :value="v" :key="v" />
                    </el-select>
                    <p v-if="form_errors.value.priority" class="text-red-500 text-xs mt-1">{{ form_errors.value.priority[0] }}</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Description</label>
                <el-input v-model="form_data.desc" placeholder="Enter description" type="textarea" :rows="2" clearable />
                <p v-if="form_errors.value.desc" class="text-red-500 text-xs mt-1">{{ form_errors.value.desc[0] }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Due Date</label>
                <el-date-picker v-model="form_data.due_date" placeholder="Select due date" type="date" clearable style="width:100%" />
                <p v-if="form_errors.value.due_date" class="text-red-500 text-xs mt-1">{{ form_errors.value.due_date[0] }}</p>
            </div>
        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <button @click="showModal = false" class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">Cancel</button>
                <button @click="onSubmit" class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors flex items-center gap-2">
                    <i class="fas fa-check text-xs"></i> {{ form_data.id ? 'Update' : 'Save' }}
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { onBeforeMount, reactive, ref } from 'vue';
import axios from 'axios';
import { ElMessage } from 'element-plus';
import { router } from '@inertiajs/vue3';

const pagination = reactive({ page_size: 10, page: 1 });
const pagination_total = ref(0);
const showModal = ref(false);
const data = reactive({ value: [], rabbits: [] });
const form_errors = reactive({ value: {} });
const form_data = reactive({ id: null, rabbit_id: null, title: null, desc: null, priority: null, due_date: null });

onBeforeMount(() => {
    axios.get(route('rabbit.dropdown')).then(res => { data.rabbits = res.data; });
    fetchData();
});

function getTitleClass(title) {
    if (title === 'Feeding') return 'bg-orange-100 text-orange-700';
    if (title === 'Grooming') return 'bg-purple-100 text-purple-700';
    return 'bg-blue-100 text-blue-700';
}

function getPriorityClass(priority) {
    if (priority === 'Essential') return 'bg-red-100 text-red-600';
    if (priority === 'Highly Needed') return 'bg-orange-100 text-orange-700';
    return 'bg-yellow-100 text-yellow-700';
}

function onNew() {
    form_errors.value = {};
    Object.assign(form_data, { id: null, rabbit_id: null, title: null, desc: null, priority: null, due_date: null });
    showModal.value = true;
}

function onEdit(row) {
    form_errors.value = {};
    Object.assign(form_data, { id: row.id, title: row.title, desc: row.desc, priority: row.priority, due_date: row.due_date });
    showModal.value = true;
}

function fetchData() {
    axios.get(route('task.get', pagination)).then(res => {
        data.value = res.data.data;
        pagination_total.value = res.data.total;
    });
}

function onSubmit() {
    const url = form_data.id ? route('task.update', form_data.id) : route('task.store');
    axios.post(url, form_data)
        .then(() => { ElMessage.success('Task saved successfully.'); router.visit(window.location.href, { preserveScroll: true }); })
        .catch(err => { form_errors.value = err.response.data.errors; });
}

function onComplete(id) {
    axios.post(route('task.update-completed', id))
        .then(() => { ElMessage.success('Task completed.'); router.visit(window.location.href, { preserveScroll: true }); });
}

function onDelete(id) {
    axios.post(route('task.delete', id))
        .then(() => { ElMessage.success('Task deleted.'); router.visit(window.location.href, { preserveScroll: true }); });
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
:deep(.el-select) { width: 100%; }
</style>
