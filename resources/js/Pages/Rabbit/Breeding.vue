<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Rabbit Breeding</h1>
                <p class="text-sm text-gray-500 mt-0.5">Track and manage breeding records</p>
            </div>
            <button
                @click="onNew"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i> New Breeding
            </button>
        </div>

        <!-- Desktop table -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <el-table :data="data.value" row-key="id" header-row-class-name="thead-light">
                <el-table-column min-width="110px" label="Buck">
                    <template v-slot="{ row }">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-mars text-blue-600 text-[10px]"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ row.buck?.name ?? '—' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column min-width="110px" label="Doe">
                    <template v-slot="{ row }">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-pink-100 flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-venus text-pink-500 text-[10px]"></i>
                            </div>
                            <span class="text-sm font-medium text-gray-800">{{ row.doe?.name ?? '—' }}</span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column min-width="110px" label="Date Bred">
                    <template v-slot="{ row }">
                        <span class="text-sm text-gray-600">{{ formatDate(row.bred) ?? '—' }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="110px" label="Date Tested">
                    <template v-slot="{ row }">
                        <span class="text-sm text-gray-600">{{ formatDate(row.tested) ?? '—' }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="140px" label="Expected Kindle">
                    <template v-slot="{ row }">
                        <span class="text-sm text-gray-600">{{ formatDate(row.expected_kindle) ?? '—' }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="90px" label="Born">
                    <template v-slot="{ row }">
                        <div class="flex gap-2 text-xs">
                            <span class="bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">
                                {{ row.alive ?? 0 }} alive
                            </span>
                            <span v-if="row.dead" class="bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full">
                                {{ row.dead }} dead
                            </span>
                        </div>
                    </template>
                </el-table-column>
                <el-table-column width="100px" label="Action" fixed="right">
                    <template v-slot="{ row }">
                        <div class="flex gap-1">
                            <button @click="onEdit(row)" class="size-8 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 flex items-center justify-center transition-colors">
                                <i class="fas fa-pencil text-xs"></i>
                            </button>
                            <button @click="onDelete(row.id)" class="size-8 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors">
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
                    background
                    layout="total, sizes, prev, pager, next"
                    :total="pagination_total"
                    @size-change="fetchData"
                    @current-change="fetchData"
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
                <!-- Pair -->
                <div class="flex items-center gap-3 mb-3">
                    <div class="flex items-center gap-1.5 bg-blue-50 rounded-xl px-3 py-1.5">
                        <i class="fas fa-mars text-blue-500 text-xs"></i>
                        <span class="text-sm font-semibold text-blue-700">{{ row.buck?.name ?? '—' }}</span>
                    </div>
                    <i class="fas fa-heart text-pink-400 text-xs"></i>
                    <div class="flex items-center gap-1.5 bg-pink-50 rounded-xl px-3 py-1.5">
                        <i class="fas fa-venus text-pink-500 text-xs"></i>
                        <span class="text-sm font-semibold text-pink-700">{{ row.doe?.name ?? '—' }}</span>
                    </div>
                </div>

                <!-- Dates grid -->
                <div class="grid grid-cols-3 gap-2 mb-3">
                    <div class="bg-gray-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Bred</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ formatDate(row.bred) ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Tested</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ formatDate(row.tested) ?? '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-2 text-center">
                        <p class="text-[10px] text-gray-400 uppercase tracking-wide">Kindle</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ formatDate(row.expected_kindle) ?? '—' }}</p>
                    </div>
                </div>

                <!-- Born stats + actions -->
                <div class="flex items-center justify-between">
                    <div class="flex gap-2">
                        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">
                            {{ row.alive ?? 0 }} alive
                        </span>
                        <span v-if="row.dead" class="text-xs bg-red-100 text-red-600 font-semibold px-2 py-0.5 rounded-full">
                            {{ row.dead }} dead
                        </span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="onEdit(row)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium transition-colors">
                            <i class="fas fa-pencil text-[10px]"></i> Edit
                        </button>
                        <button @click="onDelete(row.id)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-medium transition-colors">
                            <i class="fas fa-trash text-[10px]"></i>
                        </button>
                    </div>
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

    <!-- Breeding form dialog -->
    <el-dialog
        width="min(580px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-pink-100 flex items-center justify-center">
                    <i class="fas fa-heart text-pink-500 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ form_data.id ? 'Update' : 'New' }} Breeding Record</h2>
                    <p class="text-xs text-gray-400">Fill in the breeding details below</p>
                </div>
            </div>
        </template>

        <div class="space-y-4">

            <!-- Buck & Doe -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        <i class="fas fa-mars text-blue-500 mr-1"></i> Buck
                    </label>
                    <el-select class="w-full" v-model="form_data.buck_id" placeholder="Select buck" clearable filterable>
                        <el-option
                            v-for="v in data.bucks"
                            :key="v.id"
                            :value="v.id"
                            :label="v.name"
                        />
                    </el-select>
                    <p v-if="form_errors.value.buck_id" class="text-red-500 text-xs mt-1">{{ form_errors.value.buck_id[0] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        <i class="fas fa-venus text-pink-500 mr-1"></i> Doe
                    </label>
                    <el-select class="w-full" v-model="form_data.doe_id" placeholder="Select doe" clearable filterable>
                        <el-option
                            v-for="v in data.does"
                            :key="v.id"
                            :value="v.id"
                            :label="v.name"
                        />
                    </el-select>
                    <p v-if="form_errors.value.doe_id" class="text-red-500 text-xs mt-1">{{ form_errors.value.doe_id[0] }}</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-2">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Dates</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            <!-- Date Bred & Date Tested -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Date Bred</label>
                    <el-date-picker v-model="form_data.bred" placeholder="Select date bred" type="date" clearable style="width:100%" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Date Tested</label>
                    <el-date-picker v-model="form_data.tested" placeholder="Select date tested" type="date" clearable style="width:100%" />
                </div>
            </div>

            <!-- Expected Kindle Date -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Expected Kindle Date</label>
                <el-date-picker v-model="form_data.expected_kindle" placeholder="Select expected kindle date" type="date" clearable style="width:100%" />
                <p v-if="form_errors.value.expected_kindle" class="text-red-500 text-xs mt-1">{{ form_errors.value.expected_kindle[0] }}</p>
            </div>

            <!-- Kindled & Weaning -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Kindled Date</label>
                    <el-date-picker v-model="form_data.kindled" placeholder="Select kindled date" type="date" clearable style="width:100%" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Weaning Date</label>
                    <el-date-picker v-model="form_data.weaning" placeholder="Select weaning date" type="date" clearable style="width:100%" />
                </div>
            </div>

            <!-- Divider -->
            <div class="flex items-center gap-2">
                <div class="flex-1 h-px bg-gray-100"></div>
                <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest">Outcomes</span>
                <div class="flex-1 h-px bg-gray-100"></div>
            </div>

            <!-- Fall Out Count & Breeding Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Fall Out Count</label>
                    <el-input v-model="form_data.out_count" placeholder="e.g. 2" type="number" clearable>
                        <template #prefix><i class="fas fa-arrow-down text-gray-400 text-xs"></i></template>
                    </el-input>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Breeding Duration</label>
                    <el-input v-model="form_data.breed_duration" placeholder="e.g. 30 days" clearable>
                        <template #prefix><i class="fas fa-clock text-gray-400 text-xs"></i></template>
                    </el-input>
                </div>
            </div>

            <!-- Born Alive & Born Dead -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        <i class="fas fa-circle text-green-500 text-[8px] mr-1"></i> Born Alive
                    </label>
                    <el-input v-model="form_data.alive" placeholder="e.g. 6" type="number" clearable>
                        <template #prefix><i class="fas fa-heart text-green-400 text-xs"></i></template>
                    </el-input>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                        <i class="fas fa-circle text-red-400 text-[8px] mr-1"></i> Born Dead
                    </label>
                    <el-input v-model="form_data.dead" placeholder="e.g. 0" type="number" clearable>
                        <template #prefix><i class="fas fa-heart-crack text-red-400 text-xs"></i></template>
                    </el-input>
                </div>
            </div>

        </div>

        <template #footer>
            <div class="flex justify-end gap-2">
                <button
                    @click="showModal = false"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors"
                >Cancel</button>
                <button
                    @click="onSubmit"
                    class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors flex items-center gap-2"
                >
                    <i class="fas fa-check text-xs"></i>
                    {{ form_data.id ? 'Update' : 'Save' }}
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { onBeforeMount, reactive, ref } from 'vue';
import axios from 'axios';

const pagination = reactive({ search: null, page_size: 10, page: 1 });
const pagination_total = ref(0);
const showModal = ref(false);
const data = reactive({ value: [], bucks: [], does: [] });
const form_errors = reactive({ value: {} });
const form_data = reactive({
    id: null, bred: null, breed_duration: null, buck_id: null,
    doe_id: null, tested: null, out_count: null, kindled: null,
    weaning: null, alive: null, dead: null, expected_kindle: null,
});

onBeforeMount(() => {
    fetchData();
    axios.get(route('rabbit.get-bucks')).then(res => { data.bucks = res.data; });
    axios.get(route('rabbit.get-does')).then(res => { data.does = res.data; });
});

function formatDate(date) {
    if (!date) return null;
    return date.substring(0, 10);
}

function onNew() {
    form_errors.value = {};
    Object.assign(form_data, {
        id: null, bred: null, breed_duration: null, expected_kindle: null,
        buck_id: null, doe_id: null, tested: null, out_count: null,
        kindled: null, weaning: null, alive: null, dead: null,
    });
    showModal.value = true;
}

function onEdit(row) {
    form_errors.value = {};
    Object.assign(form_data, {
        id: row.id, bred: row.bred, breed_duration: row.breed_duration,
        buck_id: row.buck_id, doe_id: row.doe_id, tested: row.tested,
        out_count: row.out_count, kindled: row.kindled,
        expected_kindle: row.expected_kindle, weaning: row.weaning,
        alive: row.alive, dead: row.dead,
    });
    showModal.value = true;
}

function fetchData() {
    axios.get(route('rabbit.breeding.get', pagination)).then(res => {
        data.value = res.data.data;
        pagination_total.value = res.data.total;
    });
}

function onSubmit() {
    const url = form_data.id
        ? route('rabbit.breeding.update', form_data.id)
        : route('rabbit.breeding.store');
    axios.post(url, form_data)
        .then(() => { fetchData(); showModal.value = false; })
        .catch(err => { form_errors.value = err.response.data.errors; });
}

function onDelete(id) {
    axios.post(route('rabbit.breeding.delete', id))
        .then(fetchData)
        .catch(err => console.log(err));
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
:deep(.el-select) { width: 100%; }
</style>
