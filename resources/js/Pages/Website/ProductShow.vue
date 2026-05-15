<template>
    <input 
        type="file" 
        style="display: none;" 
        id="up-img" 
        accept="image/*" 
        multiple
        @change="onSendImg"
    >

    <GuestLayout>
        <div class="min-h-screen bg-gray-50 py-10 px-4">
            <div class="max-w-6xl mx-auto">

                <!-- Breadcrumb -->
                <nav class="text-sm text-gray-500 mb-6 flex items-center gap-2">
                    <Link :href="route('home')" class="hover:text-green-600 transition-colors">Home</Link>
                    <span>/</span>
                    <Link :href="route('shop')" class="hover:text-green-600 transition-colors">Shop</Link>
                    <span>/</span>
                    <span class="text-gray-800 font-medium">{{ product.name }}</span>
                </nav>

                <!-- Main Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="flex flex-col lg:flex-row">

                        <!-- ── Left: Image Gallery ── -->
                        <div class="lg:w-[52%] p-6 bg-gray-50 border-b lg:border-b-0 lg:border-r border-gray-100">
                            <!-- Main Image -->
                            <div class="relative rounded-xl overflow-hidden bg-white border border-gray-200 shadow-sm">
                                <img 
                                    loading="lazy" 
                                    class="w-full h-[420px] object-contain transition-all duration-300" 
                                    :src="$page.props.base_img_path + activeImg" 
                                    alt="Product image"
                                >
                                <!-- Out of stock overlay -->
                                <div 
                                    v-if="product.stock < 1"
                                    class="absolute inset-0 bg-black/40 flex items-center justify-center"
                                >
                                    <span class="bg-red-600 text-white text-lg font-bold px-6 py-2 rounded-full tracking-wide shadow-lg">
                                        Out of Stock
                                    </span>
                                </div>
                            </div>

                            <!-- Thumbnails -->
                            <div class="grid grid-cols-4 gap-2 mt-4">
                                <button 
                                    v-for="img in imgs" 
                                    :key="img"
                                    @click="activeImg = img"
                                    :class="[
                                        'rounded-lg overflow-hidden border-2 transition-all duration-200 focus:outline-none',
                                        img === activeImg 
                                            ? 'border-green-500 shadow-md scale-105' 
                                            : 'border-gray-200 hover:border-green-300 hover:scale-105'
                                    ]"
                                >
                                    <img 
                                        :src="$page.props.base_img_path + img" 
                                        class="w-full h-20 object-contain bg-white"
                                        alt="Thumbnail"
                                    >
                                </button>
                            </div>
                        </div>

                        <!-- ── Right: Product Info ── -->
                        <div class="lg:w-[48%] p-8 flex flex-col justify-between">
                            <div>
                                <!-- Name & Badges -->
                                <div class="flex items-start justify-between gap-3 flex-wrap">
                                    <h1 class="text-3xl font-bold text-gray-900 leading-tight">{{ product.name }}</h1>
                                    <span 
                                        :class="[
                                            'text-xs font-semibold px-3 py-1 rounded-full whitespace-nowrap',
                                            product.stock > 0 
                                                ? 'bg-green-100 text-green-700' 
                                                : 'bg-red-100 text-red-600'
                                        ]"
                                    >
                                        {{ product.stock > 0 ? 'In Stock' : 'Out of Stock' }}
                                    </span>
                                </div>

                                <!-- Rating Row -->
                                <div class="flex items-center gap-3 mt-3 flex-wrap">
                                    <el-rate
                                        :model-value="product.rating ?? 0"
                                        disabled
                                        show-score
                                        text-color="#ff9900"
                                    />
                                    <span class="text-sm text-gray-500">({{ product.rating_count }} review{{ product.rating_count !== 1 ? 's' : '' }})</span>
                                    <span class="text-sm text-gray-400">·</span>
                                    <span class="text-sm text-gray-500">{{ product.sold }} sold</span>
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-5"></div>

                                <!-- Price -->
                                <div class="flex items-baseline gap-2">
                                    <span class="text-4xl font-extrabold text-green-600">₱{{ Number(product.price).toLocaleString() }}</span>
                                    <span class="text-gray-400 text-sm">.00</span>
                                </div>

                                <!-- Stock count -->
                                <div class="mt-2 text-sm text-gray-500">
                                    <span class="font-medium text-gray-700">{{ product.stock ?? 0 }}</span> units available
                                </div>

                                <!-- Description -->
                                <div v-if="product.desc" class="mt-5 text-gray-600 text-sm leading-relaxed bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    {{ product.desc }}
                                </div>

                                <!-- Divider -->
                                <div class="border-t border-gray-100 my-5"></div>

                                <!-- Quantity Selector -->
                                <div class="flex items-center gap-4">
                                    <span class="text-sm font-medium text-gray-700">Quantity</span>
                                    <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden shadow-sm">
                                        <button 
                                            @click="decreaseQty"
                                            :disabled="qty <= 1 || product.stock < 1"
                                            class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-lg font-bold"
                                        >−</button>
                                        <span class="w-12 text-center text-sm font-semibold text-gray-800 border-x border-gray-200 h-10 flex items-center justify-center">
                                            {{ qty }}
                                        </span>
                                        <button 
                                            @click="increaseQty"
                                            :disabled="qty >= getMax() || product.stock < 1"
                                            class="w-10 h-10 flex items-center justify-center text-gray-600 hover:bg-gray-100 disabled:opacity-40 disabled:cursor-not-allowed transition-colors text-lg font-bold"
                                        >+</button>
                                    </div>
                                    <span class="text-xs text-gray-400">Max: {{ getMax() }}</span>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="mt-8 flex flex-col gap-3">
                                <div class="flex gap-3">
                                    <!-- Add to Cart -->
                                    <button 
                                        @click="onAdd"
                                        :disabled="product.stock < 1"
                                        :class="[
                                            'flex-1 flex items-center justify-center gap-2 py-3 px-5 rounded-xl font-semibold text-sm transition-all duration-200 border-2',
                                            product.stock < 1
                                                ? 'border-gray-200 text-gray-400 cursor-not-allowed bg-gray-50'
                                                : 'border-green-500 text-green-600 hover:bg-green-500 hover:text-white active:scale-95'
                                        ]"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        Add to Cart
                                    </button>

                                    <!-- Buy Now -->
                                    <button 
                                        v-if="$page.props.user"
                                        @click="onBuyNow"
                                        :disabled="product.stock < 1"
                                        :class="[
                                            'flex-1 flex items-center justify-center gap-2 py-3 px-5 rounded-xl font-semibold text-sm transition-all duration-200',
                                            product.stock < 1
                                                ? 'bg-gray-200 text-gray-400 cursor-not-allowed'
                                                : 'bg-green-500 text-white hover:bg-green-600 active:scale-95 shadow-md shadow-green-200'
                                        ]"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" />
                                        </svg>
                                        Buy Now
                                    </button>
                                </div>

                                <!-- Reviews Link -->
                                <Link :href="route('rating.index', product.id)" class="w-full">
                                    <button class="w-full py-3 px-5 rounded-xl font-semibold text-sm border border-gray-200 text-gray-600 hover:bg-gray-50 hover:border-gray-300 transition-all duration-200 flex items-center justify-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        See All Reviews
                                    </button>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </GuestLayout>

    <!-- ── Buy Now Modal ── -->
    <el-dialog 
        width="480px"
        v-model="showModal" 
        title="Complete Your Order"
        :close-on-click-modal="false"
        class="rounded-2xl"
    >
        <!-- Logo -->
        <div class="flex justify-center mb-4">
            <img 
                loading="lazy" 
                :src="`${$page.props.base_img_path}website/${$page.props.setting.logo}`" 
                class="h-12 object-contain" 
                alt="Logo"
            >
        </div>

        <!-- Payment Method -->
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Payment Method <span class="text-red-500">*</span>
            </label>
            <el-select class="w-full" v-model="form_data.payment_method" placeholder="Select payment method">
                <el-option label="Cash On Delivery" value="COD" />
                <el-option label="G-Cash" value="G-Cash" />
            </el-select>
            <span v-if="form_errors.value.payment_method" class="text-red-500 text-xs mt-1 block">
                {{ form_errors.value.payment_method[0] }}
            </span>
        </div>

        <!-- Order Summary -->
        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-sm text-gray-700 space-y-2">
            <div class="flex justify-between">
                <span class="text-gray-500">Delivery Address</span>
                <span class="font-medium text-right max-w-[60%]">{{ $page.props.user.city }}, {{ $page.props.user.address }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between">
                <span class="text-gray-500">Subtotal</span>
                <span class="font-medium">₱{{ Number(product.price).toLocaleString() }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Delivery Fee</span>
                <span class="font-medium">₱{{ $page.props.delivery_fee }}</span>
            </div>
            <div class="border-t border-gray-200 pt-2 flex justify-between text-base font-bold text-green-600">
                <span>Grand Total</span>
                <span>₱{{ ($page.props.delivery_fee + parseInt(product.price)).toLocaleString() }}</span>
            </div>
        </div>

        <!-- G-Cash Section -->
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
                    class="w-32 rounded-lg border border-gray-200 shadow-sm" 
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
                <el-button plain @click="showModal = false">Cancel</el-button>
                <el-button type="primary" @click="onSubmit" class="bg-green-500 border-green-500 hover:bg-green-600">
                    Confirm Order
                </el-button>
            </div>
        </template>
    </el-dialog>

</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import axios from 'axios';
import { ref, reactive } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { ElMessage } from 'element-plus';
import { refreshCartCount } from '../../Methods/CartCount.js';

const { product, user } = defineProps(['product', 'user']);

const imgs = product.image_names.split('|');
const activeImg = ref(imgs[0]);
const qty = ref(1);
const showModal = ref(false);
const form_errors = reactive({ value: [] });
const form_data = reactive({
    address: user?.address,
    payment_method: null,
    message_id: null,
    qty: 1,
    product_id: product.id,
});

function getMax() {
    const stock = parseInt(product.stock ?? '1');
    return stock > 0 ? stock : 1;
}

function decreaseQty() {
    if (qty.value > 1) qty.value--;
}

function increaseQty() {
    if (qty.value < getMax()) qty.value++;
}

function onBuyNow() {
    if (product.stock > 0) showModal.value = true;
}

function onAdd() {
    axios.post(route('cart.add'), {
        product_id: product.id,
        qty: qty.value,
    })
    .then(() => {
        refreshCartCount();
        qty.value = 1;
        ElMessage({ type: 'success', message: 'Added to cart successfully.' });
    })
    .catch(err => {
        if (err.response.status === 401) {
            ElMessage({ type: 'error', message: 'You need to login first.' });
            router.get(route('login'));
            return;
        }
        if (err.response.data?.message) {
            ElMessage({ type: 'error', message: err.response.data.message });
        }
    });
}

function onClickImg() {
    document.querySelector('#up-img').click();
}

function onSendImg(e) {
    axios.post(route('message.send-image'), {
        images: e.target.files,
        send_reference: 1,
    }, {
        headers: { 'content-type': 'multipart/form-data' },
    })
    .then(res => {
        form_data.message_id = res.data;
    })
    .finally(() => {
        const f = document.querySelector('#up-img');
        f.value = null;
        f.files = new DataTransfer().files;
        ElMessage.success('Image has been sent!');
    });
}

function onSubmit() {
    form_data.qty = qty.value;
    axios.post(route('cart.buy-now'), form_data)
        .then(() => {
            router.reload();
            showModal.value = false;
            ElMessage({ type: 'success', message: 'Order placed successfully.' });
        });
}
</script>
