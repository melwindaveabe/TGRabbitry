<template>
    <input type="file" style="display:none" id="up-img" accept="image/*" multiple @change="onSendImg">

    <GuestLayout>
        <ImageBlg title="My Cart" desc="" image="cart.jpg" />

        <div class="max-w-3xl mx-auto px-4 py-8">

            <!-- Cart items -->
            <template v-if="data.carts.length > 0">
                <div class="space-y-3">
                    <div
                        v-for="cart in data.carts"
                        :key="cart.id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm p-4"
                    >
                        <div class="flex items-start gap-3">
                            <!-- Checkbox -->
                            <div class="flex flex-col items-center gap-3 pt-1 flex-shrink-0">
                                <el-checkbox
                                    v-model="selectedProducts[cart.id]"
                                    @change="getGrandTotal"
                                />
                                <button
                                    @click="onRemove(cart.id)"
                                    class="w-7 h-7 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors"
                                >
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>

                            <!-- Product image -->
                            <Link :href="route('product.show', cart.product.id)" class="flex-shrink-0">
                                <img
                                    :src="$page.props.base_img_path + (cart.product.images
                                        ? cart.product.images.split('|')[0]
                                        : 'rabbit breeds/' + cart.product.name + '.jpg')"
                                    loading="lazy"
                                    class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover border border-gray-100 hover:scale-105 transition-transform"
                                    alt=""
                                >
                            </Link>

                            <!-- Product info -->
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-800 text-sm truncate">{{ cart.product.name }}</div>
                                <div class="text-green-600 font-bold text-sm mt-0.5">
                                    ₱{{ Number(cart.product.price).toLocaleString() }}
                                </div>

                                <!-- Qty + line total row -->
                                <div class="flex items-center justify-between gap-2 mt-3 flex-wrap">
                                    <!-- Custom qty control -->
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                        <button
                                            @click="decreaseQty(cart.id)"
                                            class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors font-bold text-base"
                                        >−</button>
                                        <span class="w-9 text-center text-sm font-semibold text-gray-800 border-x border-gray-200 h-8 flex items-center justify-center">
                                            {{ qty[cart.id] }}
                                        </span>
                                        <button
                                            @click="increaseQty(cart.id)"
                                            class="w-8 h-8 flex items-center justify-center text-gray-600 hover:bg-gray-100 transition-colors font-bold text-base"
                                        >+</button>
                                    </div>
                                    <div class="text-sm font-bold text-gray-800">
                                        ₱{{ computeTotal(qty[cart.id], cart.product.price).toLocaleString() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary bar -->
                <div class="mt-6 bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-2">
                        <span>Selected items subtotal</span>
                        <span class="font-bold text-gray-800 text-base">₱{{ subTotal.toLocaleString() }}</span>
                    </div>
                    <div class="text-xs text-gray-400 mb-4">Select items above to include in checkout</div>
                    <button
                        @click="onCheckOut"
                        class="w-full py-3.5 bg-green-600 hover:bg-green-700 active:scale-95 text-white font-semibold rounded-xl transition-all shadow-md shadow-green-200 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-bag-shopping text-sm"></i>
                        Checkout
                    </button>
                </div>
            </template>

            <!-- Empty state -->
            <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 flex flex-col items-center text-center">
                <div class="w-16 h-16 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                    <i class="fas fa-cart-shopping text-2xl text-gray-300"></i>
                </div>
                <p class="font-semibold text-gray-700">Your cart is empty</p>
                <p class="text-sm text-gray-400 mt-1">Add some products to get started.</p>
                <Link :href="route('shop')" class="mt-5 px-6 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors">
                    Browse Shop
                </Link>
            </div>
        </div>
    </GuestLayout>

    <!-- Checkout modal -->
    <el-dialog
        width="min(480px, 95%)"
        v-model="showModal"
        :close-on-click-modal="false"
    >
        <template #header>
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-green-100 flex items-center justify-center">
                    <i class="fas fa-bag-shopping text-green-600 text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-bold text-gray-900">Checkout</h2>
                    <p class="text-xs text-gray-400">Review your order before placing</p>
                </div>
            </div>
        </template>

        <!-- Payment method -->
        <div class="mb-4">
            <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                Payment Method <span class="text-red-500">*</span>
            </label>
            <el-select class="w-full" v-model="form_data.payment_method" placeholder="Select payment method">
                <el-option label="Cash On Delivery" value="COD" />
                <el-option label="G-Cash" value="G-Cash" />
            </el-select>
            <p v-if="form_errors.value.payment_method" class="text-red-500 text-xs mt-1">
                {{ form_errors.value.payment_method[0] }}
            </p>
        </div>

        <!-- Order summary -->
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-sm space-y-2">
            <div class="flex justify-between text-gray-500">
                <span>Delivery Address</span>
                <span class="font-medium text-gray-700 text-right max-w-[55%]">
                    {{ $page.props.user.city }}, {{ $page.props.user.address }}
                </span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between text-gray-500">
                <span>Subtotal</span>
                <span class="font-medium text-gray-700">₱{{ subTotal.toLocaleString() }}</span>
            </div>
            <div class="flex justify-between text-gray-500">
                <span>Delivery Fee</span>
                <span class="font-medium text-gray-700">₱{{ $page.props.delivery_fee }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between font-bold text-green-600 text-base">
                <span>Grand Total</span>
                <span>₱{{ ($page.props.delivery_fee + subTotal).toLocaleString() }}</span>
            </div>
        </div>

        <!-- G-Cash section -->
        <div v-if="form_data.payment_method === 'G-Cash'" class="mt-4 flex gap-4 flex-wrap">
            <div class="flex-1 text-sm text-gray-600">
                <p class="font-semibold text-gray-800 mb-1">G-Cash Payment</p>
                <p class="text-xs text-gray-400 italic mb-2">Note: You can only cancel an order within one hour.</p>
                <p>G-Cash Number: <strong class="text-gray-800">{{ $page.props.setting.number }}</strong></p>
            </div>
            <div class="flex flex-col items-center gap-2">
                <img
                    loading="lazy"
                    :src="`${$page.props.base_img_path}/website/${$page.props.setting.gcash_qr}`"
                    class="w-28 rounded-xl border border-gray-200 shadow-sm"
                    alt="GCash QR"
                >
                <button
                    @click="onClickImg"
                    class="text-xs bg-green-500 hover:bg-green-600 text-white px-3 py-1.5 rounded-lg transition-colors"
                >
                    Send Reference
                </button>
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
                    class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors shadow-sm flex items-center gap-2"
                >
                    <i class="fas fa-check text-xs"></i> Place Order
                </button>
            </div>
        </template>
    </el-dialog>
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import axios from 'axios';
import { ref, reactive, onBeforeMount } from 'vue';
import { Link } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { refreshCartCount } from '../../Methods/CartCount.js';
import ImageBlg from '@/Components/ImageBlg.vue';

const { user } = defineProps(['user']);

const qty = reactive({});
const subTotal = ref(0);
const selectedProducts = reactive({});
const data = reactive({ carts: [] });
const showModal = ref(false);
const form_errors = reactive({ value: [] });
const form_data = reactive({
    address: user.address,
    payment_method: null,
    message_id: null,
    products: [],
});

onBeforeMount(fetchData);

function fetchData() {
    axios.get(route('cart.get')).then(res => {
        data.carts = res.data;
        for (let i in data.carts) {
            qty[data.carts[i].id] = parseInt(data.carts[i].qty);
            selectedProducts[data.carts[i].id] = false;
        }
    });
}

function decreaseQty(id) {
    if (qty[id] > 1) {
        qty[id]--;
        onUpdateQty(id);
    }
}

function increaseQty(id) {
    qty[id]++;
    onUpdateQty(id);
}

function onClickImg() { document.querySelector('#up-img').click(); }

function onSendImg(e) {
    axios.post(route('message.send-image'), { images: e.target.files, send_reference: 1 }, {
        headers: { 'content-type': 'multipart/form-data' },
    }).then(res => {
        form_data.message_id = res.data;
    }).finally(() => {
        const f = document.querySelector('#up-img');
        f.value = null;
        f.files = new DataTransfer().files;
        ElMessage.success('Image has been sent!');
    });
}

function getGrandTotal() {
    let total = 0;
    for (let i in selectedProducts) {
        if (!selectedProducts[i]) continue;
        total += qty[i] * getPrice(i);
    }
    subTotal.value = total;
}

function getPrice(id) {
    for (const i in data.carts) {
        if (data.carts[i].id == id) return parseInt(data.carts[i].product.price);
    }
    return 0;
}

function computeTotal(q, price) { return q * parseInt(price); }

function onRemove(id) {
    axios.post(route('cart.delete', id)).then(() => {
        refreshCartCount();
        fetchData();
    });
}

function onUpdateQty(id) {
    axios.post(route('cart.update-qty', id), { qty: qty[id] })
        .then(fetchData)
        .catch(err => ElMessage({ type: 'error', message: err.response.data?.message }));
}

function onCheckOut() {
    if (!hasSelected()) {
        ElMessage({ type: 'error', message: 'No product selected to checkout' });
        return;
    }
    showModal.value = true;
}

function hasSelected() {
    form_data.products = [];
    for (let i in selectedProducts) {
        if (selectedProducts[i]) form_data.products.push(i);
    }
    return form_data.products.length > 0;
}

function onSubmit() {
    axios.post(route('cart.checkout'), form_data)
        .then(() => {
            refreshCartCount();
            fetchData();
            showModal.value = false;
            ElMessage({ type: 'success', message: 'Checked out successfully.' });
        })
        .catch(err => ElMessage({ type: 'error', message: err.response.data?.message }));
}
</script>

<style scoped>
:deep(.el-dialog) { border-radius: 1rem; }
:deep(.el-dialog__header) { padding: 20px 20px 12px; border-bottom: 1px solid #f3f4f6; margin-right: 0; }
:deep(.el-dialog__body) { padding: 20px; }
:deep(.el-dialog__footer) { padding: 12px 20px 20px; border-top: 1px solid #f3f4f6; }
:deep(.el-select) { width: 100%; }
</style>
