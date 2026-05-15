<template>
    <!-- Admin: create blog -->
    <template v-if="admin">
        <div class="max-w-4xl mx-auto px-4 mt-6">
            <button
                v-if="!showBlogForm"
                @click="showBlogForm = true"
                class="flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors"
            >
                <i class="fas fa-plus text-xs"></i> Create Blog
            </button>

            <div v-if="showBlogForm" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 mt-4">
                <h3 class="font-bold text-gray-800 text-lg mb-4">New Blog Post</h3>

                <div class="space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                            Title <span class="text-red-500">*</span>
                        </label>
                        <el-input v-model="form_data.title" placeholder="Enter title" clearable />
                        <p v-if="form_errors.value.title" class="text-red-500 text-xs mt-1">{{ form_errors.value.title[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">
                            Body <span class="text-red-500">*</span>
                        </label>
                        <el-input v-model="form_data.body" placeholder="Write your blog content..." type="textarea" :rows="5" clearable />
                        <p v-if="form_errors.value.body" class="text-red-500 text-xs mt-1">{{ form_errors.value.body[0] }}</p>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-600 mb-1.5 uppercase tracking-wide">Cover Image</label>
                        <el-upload
                            ref="imgUpload"
                            action="#"
                            :auto-upload="false"
                            :on-change="onChangeImage"
                            accept="image/*"
                            :on-remove="handleRemove"
                            drag
                            list-type="picture"
                        >
                            <div class="flex flex-col items-center py-6">
                                <i class="fas fa-cloud-arrow-up text-3xl text-gray-300 mb-2"></i>
                                <p class="text-sm text-gray-500">Drop image or <em class="text-green-600 not-italic font-medium">click to upload</em></p>
                                <p class="text-xs text-gray-400 mt-1">Max 2MB</p>
                            </div>
                        </el-upload>
                        <p v-if="form_errors.value.image" class="text-red-500 text-xs mt-1">{{ form_errors.value.image[0] }}</p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <button @click="onCancel" class="px-5 py-2 rounded-xl text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 transition-colors">
                        Cancel
                    </button>
                    <button @click="onSubmit" class="px-5 py-2 rounded-xl text-sm font-medium text-white bg-green-600 hover:bg-green-700 transition-colors flex items-center gap-2">
                        <i class="fas fa-paper-plane text-xs"></i> Post
                    </button>
                </div>
            </div>
        </div>
    </template>

    <!-- Main layout: blog list + sidebar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex flex-col lg:flex-row gap-8">

        <!-- Blog list -->
        <div class="w-full lg:w-[65%]">
            <InfScroll
                v-if="blogs.value.length > 0"
                :loading="loading"
                :isDisabled="isDisabled"
                :on-scroll="onLimit"
            >
                <div class="space-y-6">
                    <article
                        v-for="item in blogs.value"
                        :key="item.id"
                        class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-md transition-shadow"
                    >
                        <!-- Cover image -->
                        <img
                            class="w-full h-52 sm:h-64 object-cover"
                            :src="`${$page.props.base_img_path}blog/${item.image}`"
                            alt=""
                            loading="lazy"
                        >

                        <div class="p-5">
                            <!-- Meta -->
                            <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-calendar-days text-[10px]"></i>
                                    {{ item.created_at.replace('T', ' ').substring(0, 10) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-comment text-[10px]"></i>
                                    {{ item.comments ?? 0 }}
                                </span>
                            </div>

                            <!-- Title -->
                            <h2 class="text-xl font-bold text-gray-800 hover:text-green-600 transition-colors leading-snug mb-2">
                                {{ item.title }}
                            </h2>

                            <!-- Excerpt -->
                            <div v-html="item.body" class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4"></div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between">
                                <Link :href="route(admin ? 'blog.show' : 'sblog', item.id)">
                                    <button class="flex items-center gap-2 px-5 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors">
                                        Read More <i class="fas fa-arrow-right text-xs"></i>
                                    </button>
                                </Link>
                                <button
                                    v-if="admin"
                                    @click="onDelete(item.id)"
                                    class="w-9 h-9 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 flex items-center justify-center transition-colors"
                                    title="Delete"
                                >
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </div>
                        </div>
                    </article>
                </div>
            </InfScroll>

            <div v-else class="bg-white rounded-2xl border border-gray-100 shadow-sm p-16 flex flex-col items-center text-center text-gray-400">
                <i class="fas fa-newspaper text-4xl mb-3 opacity-30"></i>
                <p class="font-medium">No blogs yet</p>
            </div>
        </div>

        <!-- Sidebar -->
        <aside class="w-full lg:w-[32%] space-y-6">

            <!-- Categories -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-tags text-green-500 text-sm"></i> Categories
                </h3>
                <div class="space-y-2">
                    <div
                        v-for="product in products"
                        :key="product.name"
                        class="flex justify-between items-center py-2 border-b border-gray-50 last:border-0"
                    >
                        <span class="text-sm text-gray-700">{{ product.name }}</span>
                        <span class="text-xs bg-green-100 text-green-700 font-semibold px-2 py-0.5 rounded-full">{{ product.stock }}</span>
                    </div>
                </div>
            </div>

            <!-- Recent blogs -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-5">
                <h3 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-clock-rotate-left text-green-500 text-sm"></i> Recent Posts
                </h3>
                <div class="space-y-4">
                    <div v-for="recent_blog in recent_blogs" :key="recent_blog.id" class="flex gap-3">
                        <img
                            :src="`${$page.props.base_img_path}blog/${recent_blog.image}`"
                            class="w-16 h-16 rounded-xl object-cover flex-shrink-0"
                            alt=""
                            loading="lazy"
                        >
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 hover:text-green-600 transition-colors leading-snug line-clamp-2">
                                {{ recent_blog.title }}
                            </p>
                            <div class="flex items-center gap-2 mt-1 text-xs text-gray-400 flex-wrap">
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-calendar-days text-[10px]"></i>
                                    {{ recent_blog.created_at.replace('T', ' ').substring(0, 10) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <i class="fas fa-user text-[10px]"></i>
                                    {{ recent_blog.owner_name }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- About section -->
            <div class="bg-green-50 rounded-2xl border border-green-100 p-5">
                <h3 class="font-bold text-green-800 mb-2 text-sm uppercase tracking-wide">Blog Section</h3>
                <p class="text-green-700 text-xs leading-relaxed">
                    Your go-to source for insightful articles about everything rabbit-related. From caring for your furry companions to tips on breeding and health — curated for rabbit enthusiasts of all levels.
                </p>
            </div>
        </aside>
    </div>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref, reactive, onBeforeMount } from 'vue';
import { ElMessage } from 'element-plus';
import InfScroll from '@/Components/InfScroll.vue';
import axios from 'axios';

const { admin, products, recent_blogs } = defineProps(['admin', 'products', 'recent_blogs']);

const imgUpload = ref(null);
const showBlogForm = ref(false);
const form_errors = reactive({ value: {} });
const form_data = reactive({ body: null, title: null, image: null });
const limit = ref(10);
const loading = ref(false);
const isDisabled = ref(false);
const blogs = reactive({ value: [] });

onBeforeMount(fetchData);

function onDelete(id) {
    axios.post(route('blog.delete', id))
        .then(fetchData)
        .catch(() => ElMessage({ type: 'error', message: 'Error deleting blog' }));
}

async function onLimit(lmt) {
    limit.value = lmt;
    return await fetchData();
}

async function fetchData() {
    loading.value = true;
    let d = false;
    await axios.get(route('blog.get-scroll', { limit: limit.value }))
        .then(res => {
            if (blogs.value.length >= res.data.length) d = true;
            blogs.value = res.data;
        })
        .finally(() => { loading.value = false; });
    return d;
}

function onCancel() {
    form_errors.value = {};
    if (imgUpload.value) imgUpload.value.clearFiles();
    form_data.image = null;
    form_data.title = null;
    form_data.body = null;
    showBlogForm.value = false;
}

function onChangeImage(img) { form_data.image = img.raw; }
function handleRemove() { form_data.image = null; }

function onSubmit() {
    if (form_data.body) form_data.body = form_data.body.replaceAll('\n', '<br>');
    axios.post(route('blog.store'), form_data, {
        headers: { 'content-type': 'multipart/form-data' },
    })
    .then(() => {
        fetchData();
        ElMessage({ type: 'success', message: 'Posted a blog successfully.' });
        onCancel();
    })
    .catch(err => {
        if (err.response.status === 401) {
            ElMessage({ type: 'error', message: 'You need to login first' });
            router.get(route('login'));
            return;
        }
        form_errors.value = err.response.data.errors;
    });
}
</script>
