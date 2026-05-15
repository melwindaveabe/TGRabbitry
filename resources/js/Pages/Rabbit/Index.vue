<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-4">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Rabbitry Management</h1>
                <p class="text-sm text-gray-500 mt-0.5">Track and manage all your rabbits</p>
            </div>
            <button
                @click="onNew"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i> Add Rabbit
            </button>
        </div>

        <!-- Search bar -->
        <div class="mb-4">
            <el-input
                v-model="pagination.rfid"
                @keyup.enter="fetchData"
                placeholder="Search by RFID..."
                type="text"
                clearable
            >
                <template #prefix><i class="fas fa-search text-gray-400 text-xs"></i></template>
            </el-input>
        </div>

        <!-- Desktop table -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <el-table :data="data.value" row-key="id" header-row-class-name="thead-light">
                <el-table-column label="Image" min-width="80px">
                    <template v-slot="{ row }">
                        <img loading="lazy" class="w-12 h-12 rounded-xl object-cover"
                            :src="$page.props.base_img_path + row.images" alt="">
                    </template>
                </el-table-column>
                <el-table-column min-width="100px" label="Name">
                    <template v-slot="{ row }"><span class="font-medium text-sm text-gray-800">{{ row.name }}</span></template>
                </el-table-column>
                <el-table-column min-width="110px" label="Breed">
                    <template v-slot="{ row }"><span class="text-sm text-gray-600">{{ row.product.name }}</span></template>
                </el-table-column>
                <el-table-column width="75px" label="Gender">
                    <template v-slot="{ row }">
                        <span :class="`text-xs font-semibold px-2 py-0.5 rounded-full ${row.gender === 'Buck' ? 'bg-blue-100 text-blue-700' : row.gender === 'Doe' ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-600'}`">
                            {{ row.gender }}
                        </span>
                    </template>
                </el-table-column>
                <el-table-column width="110px" label="Age">
                    <template v-slot="{ row }"><span class="text-sm text-gray-600">{{ row.birth ? calculateAge(row.birth) : '—' }}</span></template>
                </el-table-column>
                <el-table-column min-width="90px" label="Weight">
                    <template v-slot="{ row }"><span class="text-sm font-medium text-gray-700">{{ row.latest_weight?.weight ?? 0 }}kg</span></template>
                </el-table-column>
                <el-table-column min-width="110px" label="Disease">
                    <template v-slot="{ row }"><span class="text-sm text-gray-500">{{ row.illness?.illness ?? '—' }}</span></template>
                </el-table-column>
                <el-table-column min-width="100px" label="Status">
                    <template v-slot="{ row }">
                        <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full ${getStatusClass(row.status)}`">{{ row.status }}</span>
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
                <div class="flex items-start gap-3 mb-3">
                    <img loading="lazy" class="w-16 h-16 rounded-xl object-cover flex-shrink-0"
                        :src="$page.props.base_img_path + row.images" alt="">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm">{{ row.name }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ row.product.name }}</p>
                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                            <span :class="`text-xs font-semibold px-2 py-0.5 rounded-full ${row.gender === 'Buck' ? 'bg-blue-100 text-blue-700' : row.gender === 'Doe' ? 'bg-pink-100 text-pink-700' : 'bg-gray-100 text-gray-600'}`">
                                {{ row.gender }}
                            </span>
                            <span :class="`text-xs font-semibold px-2 py-0.5 rounded-full ${getStatusClass(row.status)}`">{{ row.status }}</span>
                        </div>
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-2 text-center mb-3">
                    <div class="bg-gray-50 rounded-xl py-2">
                        <p class="text-xs text-gray-400">Age</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ row.birth ? calculateAge(row.birth) : '—' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2">
                        <p class="text-xs text-gray-400">Weight</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5">{{ row.latest_weight?.weight ?? 0 }}kg</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl py-2">
                        <p class="text-xs text-gray-400">Disease</p>
                        <p class="text-xs font-semibold text-gray-700 mt-0.5 truncate px-1">{{ row.illness?.illness ?? '—' }}</p>
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <button @click="onEdit(row)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 hover:bg-blue-100 text-blue-600 text-xs font-medium transition-colors">
                        <i class="fas fa-pencil text-[10px]"></i> Edit
                    </button>
                    <button @click="onDelete(row.id)" class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 text-xs font-medium transition-colors">
                        <i class="fas fa-trash text-[10px]"></i> Delete
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

    <!-- Rabbit form dialog -->
    <el-dialog
        width="min(540px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-paw text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ form_data.id ? 'Update' : 'New' }} Rabbit</h2>
                    <p class="text-xs text-gray-400">Fill in the rabbit details below</p>
                </div>
            </div>
        </template>

        <div class="space-y-4">
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Status</label>
                    <el-select class="w-full" v-model="form_data.status" placeholder="Select status">
                        <el-option v-for="s in ['For Breeding','For Bundle','For Sale','For Resting','Died','Kits']" :value="s" :label="s" :key="s" />
                    </el-select>
                    <p v-if="form_errors.value.status" class="text-red-500 text-xs mt-1">{{ form_errors.value.status[0] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Gender</label>
                    <el-select class="w-full" v-model="form_data.gender" placeholder="Select gender">
                        <el-option value="Buck" label="Buck" />
                        <el-option value="Doe" label="Doe" />
                        <el-option value="Unknown" label="Unknown" />
                    </el-select>
                    <p v-if="form_errors.value.gender" class="text-red-500 text-xs mt-1">{{ form_errors.value.gender[0] }}</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Rabbit Breed</label>
                <el-select class="w-full" v-model="form_data.breed" placeholder="Select breed" filterable>
                    <el-option v-for="v in ['Californian','Lion Head','New Zealand','Teddy Rabbit']" :value="v" :label="v" :key="v" />
                </el-select>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">RFID <span class="text-red-500">*</span></label>
                    <el-input v-model="form_data.rfid" placeholder="Enter RFID" clearable />
                    <p v-if="form_errors.value.rfid" class="text-red-500 text-xs mt-1">{{ form_errors.value.rfid[0] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Name <span class="text-red-500">*</span></label>
                    <el-input v-model="form_data.name" placeholder="Enter name" clearable />
                    <p v-if="form_errors.value.name" class="text-red-500 text-xs mt-1">{{ form_errors.value.name[0] }}</p>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Birthday</label>
                <el-date-picker v-model="form_data.birth" placeholder="Select birthday" type="date" clearable style="width:100%" />
                <p v-if="form_errors.value.birth" class="text-red-500 text-xs mt-1">{{ form_errors.value.birth[0] }}</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Note</label>
                <el-input v-model="form_data.note" placeholder="Enter note" type="textarea" :rows="2" clearable />
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Illness</label>
                    <el-input v-model="form_data.illness" placeholder="Enter illness" clearable />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Illness Date</label>
                    <el-date-picker v-model="form_data.date" placeholder="Select date" type="date" clearable style="width:100%" />
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Photo</label>
                <el-upload ref="imgUpload" action="#" :auto-upload="false" :on-change="onChangeImage"
                    accept="image/*" :on-remove="handleRemove" drag list-type="picture">
                    <div class="flex flex-col items-center py-5">
                        <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                        <p class="text-sm text-gray-500">Drop image or <em class="text-green-600 not-italic font-medium">click to upload</em></p>
                    </div>
                </el-upload>
                <p v-if="form_errors.value.images" class="text-red-500 text-xs mt-1">{{ form_errors.value.images[0] }}</p>
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

const imgUpload = ref(null);
const pagination = reactive({ rfid: null, page_size: 10, page: 1 });
const pagination_total = ref(0);
const showModal = ref(false);
const data = reactive({ value: [], rabbits: [], bucks: [], does: [] });
const form_errors = reactive({ value: {} });
const form_data = reactive({ id: null, breed: null, name: null, gender: null, images: null, birth: null, note: null, status: null, rfid: null, illness: null, date: null });

onBeforeMount(() => {
    fetchData();
    axios.get(route('product.rabbits')).then(res => { data.rabbits = res.data; });
});

function onNew() {
    form_errors.value = {};
    Object.assign(form_data, { id: null, breed: null, name: null, gender: null, images: null, birth: null, note: null, status: null, rfid: null, illness: null, date: null });
    if (imgUpload.value) imgUpload.value.clearFiles();
    showModal.value = true;
}

function onEdit(row) {
    form_errors.value = {};
    Object.assign(form_data, { id: row.id, breed: row.product?.name, name: row.name, gender: row.gender, birth: row.birth, note: row.note, status: row.status, rfid: row.rfid, images: null, illness: null, date: null });
    if (imgUpload.value) imgUpload.value.clearFiles();
    showModal.value = true;
}

function getStatusClass(status) {
    if (status === 'For Sale') return 'bg-green-100 text-green-700';
    if (status === 'For Breeding') return 'bg-blue-100 text-blue-700';
    if (status === 'Died') return 'bg-red-100 text-red-600';
    return 'bg-yellow-100 text-yellow-700';
}

function onChangeImage(img) { form_data.images = img.raw; }
function handleRemove() { form_data.images = null; }

function fetchData() {
    axios.get(route('rabbit.get', pagination)).then(res => {
        data.value = res.data.data;
        pagination_total.value = res.data.total;
        pagination.rfid = null;
    });
    axios.get(route('rabbit.get-bucks')).then(res => { data.bucks = res.data; });
    axios.get(route('rabbit.get-does')).then(res => { data.does = res.data; });
}

function onSubmit() {
    const url = form_data.id ? route('rabbit.update', form_data.id) : route('rabbit.store');
    axios.post(url, form_data, { headers: { 'content-type': 'multipart/form-data' } })
        .then(() => { fetchData(); showModal.value = false; })
        .catch(err => { form_errors.value = err.response.data.errors; });
}

function onDelete(id) {
    axios.post(route('rabbit.delete', id)).then(fetchData);
}

function calculateAge(birthday) {
    const diff = new Date() - new Date(birthday);
    const years = Math.floor(diff / (1000 * 60 * 60 * 24 * 365));
    if (years > 0) return `${years}y old`;
    const months = Math.floor(diff / (1000 * 60 * 60 * 24 * 30));
    if (months > 0) return `${months}mo old`;
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));
    return `${days}d old`;
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
:deep(.el-select) { width: 100%; }
:deep(.el-table__body-wrapper) { overflow: auto; max-height: 55svh; }
</style>
