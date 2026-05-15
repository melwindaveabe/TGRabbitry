<template>
    <AuthLayout>
        <!-- Page header -->
        <div class="flex items-center justify-between flex-wrap gap-3 mb-5">
            <div>
                <h1 class="text-xl font-bold text-gray-800">Products</h1>
                <p class="text-sm text-gray-500 mt-0.5">Manage your product catalogue</p>
            </div>
            <button
                @click="onNew"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm"
            >
                <i class="fas fa-plus text-xs"></i> Add Product
            </button>
        </div>

        <!-- Desktop table -->
        <div class="hidden md:block bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <el-table :data="data.value" row-key="id" header-row-class-name="thead-light">
                <el-table-column min-width="140px" label="Name">
                    <template v-slot="{ row }">
                        <span class="text-sm font-medium text-gray-800">{{ row.name }}{{ row.rabbit ? '/' + row.rabbit.name : '' }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="110px" label="Category">
                    <template v-slot="{ row }">
                        <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">{{ row.category }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="160px" label="Description">
                    <template v-slot="{ row }">
                        <span class="text-sm text-gray-500 line-clamp-1">{{ row.desc }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="100px" label="Price">
                    <template v-slot="{ row }">
                        <span class="font-bold text-green-700">₱{{ numberWithCommas(row.price) }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="80px" label="Stock">
                    <template v-slot="{ row }">
                        <span :class="`text-sm font-semibold ${parseInt(row.stock) < 5 ? 'text-red-600' : 'text-gray-700'}`">{{ numberWithCommas(row.stock) }}</span>
                    </template>
                </el-table-column>
                <el-table-column min-width="90px" label="Status">
                    <template v-slot="{ row }">
                        <span :class="`text-xs font-semibold px-2.5 py-1 rounded-full ${row.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`">
                            {{ row.active ? 'Active' : 'Inactive' }}
                        </span>
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
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex-1 min-w-0">
                        <p class="font-semibold text-gray-800 text-sm truncate">{{ row.name }}{{ row.rabbit ? '/' + row.rabbit.name : '' }}</p>
                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                            <span class="text-xs bg-gray-100 text-gray-600 px-2 py-0.5 rounded-full font-medium">{{ row.category }}</span>
                            <span :class="`text-xs font-semibold px-2 py-0.5 rounded-full ${row.active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}`">
                                {{ row.active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="font-bold text-green-600">₱{{ numberWithCommas(row.price) }}</p>
                        <p class="text-xs text-gray-400 mt-0.5">Stock: {{ numberWithCommas(row.stock) }}</p>
                    </div>
                </div>
                <p v-if="row.desc" class="text-xs text-gray-500 mb-3 line-clamp-2">{{ row.desc }}</p>
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
                    @current-change="fetchData"
                    small
                />
            </div>
        </div>
    </AuthLayout>

    <!-- Product form dialog -->
    <el-dialog
        width="min(560px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-cart-shopping text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">{{ form_data.id ? 'Update' : 'New' }} Product</h2>
                    <p class="text-xs text-gray-400">Fill in the product details below</p>
                </div>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Category -->
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Category <span class="text-red-500">*</span></label>
                <el-select class="w-full" v-model="form_data.category" filterable placeholder="Select category"
                    :disabled="form_data.id && form_data.category == 'Rabbit'">
                    <el-option label="Rabbit" value="Rabbit" />
                    <el-option label="Food" value="Food" />
                    <el-option label="Accesories" value="Accesories" />
                    <el-option label="Bundle Package" value="Bundle Package" />
                </el-select>
                <p v-if="form_errors.value.category" class="text-red-500 text-xs mt-1">{{ form_errors.value.category[0] }}</p>
            </div>

            <!-- Rabbit-specific fields -->
            <template v-if="form_data.category == 'Rabbit'">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Rabbit Breed</label>
                    <el-select class="w-full" v-model="form_data.name" placeholder="Select rabbit breed" filterable clearable
                        @change="fetchRabbits" :disabled="form_data.id && form_data.category == 'Rabbit'">
                        <el-option v-for="r in ['Californian','Lion Head','New Zealand','Teddy Rabbit']" :value="r" :label="r" :key="r" />
                    </el-select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Rabbit</label>
                    <el-select class="w-full" v-model="form_data.product_id" placeholder="Select rabbit" filterable clearable
                        :disabled="form_data.id && form_data.category == 'Rabbit'">
                        <el-option v-for="r in data.rabbits" :value="r.product_id" :label="r.name" :key="r.product_id" />
                    </el-select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Price</label>
                    <el-input v-model="form_data.price" placeholder="Enter price" type="number" clearable>
                        <template #prefix><span class="text-gray-400 text-sm">₱</span></template>
                    </el-input>
                    <p v-if="form_errors.value.price" class="text-red-500 text-xs mt-1">{{ form_errors.value.price[0] }}</p>
                </div>
            </template>

            <!-- Non-rabbit fields -->
            <template v-else>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Name <span class="text-red-500">*</span></label>
                    <el-input v-model="form_data.name" placeholder="Enter product name" clearable />
                    <p v-if="form_errors.value.name" class="text-red-500 text-xs mt-1">{{ form_errors.value.name[0] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Description</label>
                    <el-input v-model="form_data.desc" placeholder="Enter description" type="textarea" :rows="2" clearable />
                    <p v-if="form_errors.value.desc" class="text-red-500 text-xs mt-1">{{ form_errors.value.desc[0] }}</p>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Price</label>
                        <el-input v-model="form_data.price" placeholder="0" type="number" clearable>
                            <template #prefix><span class="text-gray-400 text-sm">₱</span></template>
                        </el-input>
                        <p v-if="form_errors.value.price" class="text-red-500 text-xs mt-1">{{ form_errors.value.price[0] }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Stock</label>
                        <el-input v-model="form_data.stock" placeholder="0" type="number" clearable />
                        <p v-if="form_errors.value.stock" class="text-red-500 text-xs mt-1">{{ form_errors.value.stock[0] }}</p>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Status <span class="text-red-500">*</span></label>
                    <el-select class="w-full" v-model="form_data.active" placeholder="Select status">
                        <el-option :value="1" label="Active" />
                        <el-option :value="0" label="Inactive" />
                    </el-select>
                    <p v-if="form_errors.value.active" class="text-red-500 text-xs mt-1">{{ form_errors.value.active[0] }}</p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Images</label>
                    <el-upload ref="imgUpload" action="#" :auto-upload="false" :on-change="onChangeImage"
                        accept="image/*" :on-remove="handleRemove" drag multiple list-type="picture">
                        <div class="flex flex-col items-center py-5">
                            <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                            <p class="text-sm text-gray-500">Drop images or <em class="text-green-600 not-italic font-medium">click to upload</em></p>
                            <p class="text-xs text-gray-400 mt-1">Max 2MB per image</p>
                        </div>
                    </el-upload>
                    <p v-if="form_errors.value['images.0']" class="text-red-500 text-xs mt-1">{{ form_errors.value['images.0'][0] }}</p>
                </div>
            </template>
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

const pagination = reactive({ search: null, page_size: 10, page: 1 });
const pagination_total = ref(0);
const showModal = ref(false);
const imgUpload = ref(null);
const data = reactive({ value: [], rabbits: [] });
const form_errors = reactive({ value: {} });
const form_data = reactive({ id: null, product_id: null, name: null, category: null, desc: null, price: null, stock: null, active: null, images: [] });

const numberWithCommas = (str) => {
    str += '';
    const n = parseFloat(str.replace(/,/g, ''));
    return !isNaN(n) ? n.toLocaleString() : '';
};

onBeforeMount(fetchData);

function fetchRabbits(is_edit) {
    axios.get(route('rabbit.dropdown', { breed: form_data.name, for_product: form_data.id ? null : 1 }))
        .then(res => { data.rabbits = res.data; if (is_edit) showModal.value = true; });
}

function onNew() {
    form_errors.value = {};
    Object.assign(form_data, { id: null, product_id: null, name: null, category: null, desc: null, price: null, stock: null, active: null, images: [] });
    if (imgUpload.value) imgUpload.value.clearFiles();
    showModal.value = true;
}

function onEdit(row) {
    form_errors.value = {};
    form_data.id = row.id;
    form_data.price = row.price;
    form_data.category = row.category;
    form_data.name = row.name;
    form_data.images = [];
    if (imgUpload.value) imgUpload.value.clearFiles();
    if (row.category === 'Rabbit') { form_data.product_id = row.id; fetchRabbits(true); return; }
    form_data.desc = row.desc;
    form_data.stock = row.stock;
    form_data.active = row.active;
    showModal.value = true;
}

function fetchData() {
    axios.get(route('product.get', pagination)).then(res => {
        data.value = res.data.data;
        pagination_total.value = res.data.total;
    });
}

function onChangeImage(img) { form_data.images.push(img.raw); }
function handleRemove(img) { form_data.images = form_data.images.filter(i => i !== img.raw); }

function onSubmit() {
    const url = form_data.id ? route('product.update', form_data.id) : route('product.store');
    axios.post(url, form_data, { headers: { 'content-type': 'multipart/form-data' } })
        .then(() => { fetchData(); showModal.value = false; })
        .catch(err => { form_errors.value = err.response.data.errors; });
}

function onDelete(id) {
    axios.post(route('product.delete', id)).then(fetchData);
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
:deep(.el-select) { width: 100%; }
</style>
