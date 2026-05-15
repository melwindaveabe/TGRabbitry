<template>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-8 flex flex-col lg:flex-row gap-8">

        <!-- Main article -->
        <article class="w-full lg:w-[65%]">

            <!-- Title -->
            <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight mb-5">
                {{ blog.title }}
            </h1>

            <!-- Cover image -->
            <div class="rounded-2xl overflow-hidden border border-gray-100 shadow-sm mb-6">
                <img
                    loading="lazy"
                    class="w-full max-h-[60vh] object-cover"
                    :src="`${$page.props.base_img_path}blog/${blog.image}`"
                    alt=""
                >
            </div>

            <!-- Body -->
            <div v-html="blog.body" class="prose prose-sm max-w-none text-gray-600 leading-relaxed mb-8"></div>

            <!-- Quote card -->
            <div class="bg-green-50 border border-green-100 rounded-2xl p-5 flex gap-4 items-start mb-8">
                <img
                    loading="lazy"
                    class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl object-cover flex-shrink-0"
                    :src="`${$page.props.base_img_path}website/${$page.props.setting.quote_image}`"
                    alt=""
                >
                <div>
                    <p class="font-bold text-green-800 text-base mb-1">{{ $page.props.setting.quote_title }}</p>
                    <p class="text-green-700 text-sm leading-relaxed">{{ $page.props.setting.quote_body }}</p>
                </div>
            </div>

            <!-- Comments section -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <!-- Header -->
                <div class="px-5 py-4 border-b border-gray-100 flex items-center gap-2">
                    <i class="fas fa-comments text-green-500 text-sm"></i>
                    <h2 class="font-bold text-gray-800">{{ blog.comments.length }} Comment{{ blog.comments.length !== 1 ? 's' : '' }}</h2>
                </div>

                <!-- Comment list -->
                <div class="divide-y divide-gray-50">
                    <template v-for="comment in blog.comments" :key="comment.id">
                        <Comment
                            :reply_to_id="comment.id"
                            :date="comment.created_at"
                            :body="comment.body"
                            :owner_name="comment.owner.name"
                        />
                        <template v-for="reply1 in comment.replies" :key="reply1.id">
                            <Comment
                                :reply_to_id="reply1.id"
                                :date="reply1.created_at"
                                :body="reply1.body"
                                :owner_name="reply1.owner.name"
                                class-name="ml-6 sm:ml-10"
                            />
                            <template v-for="reply2 in reply1.replies" :key="reply2.id">
                                <Comment
                                    :reply_to_id="reply2.id"
                                    :date="reply2.created_at"
                                    :body="reply2.body"
                                    :owner_name="reply2.owner.name"
                                    class-name="ml-12 sm:ml-20"
                                />
                                <template v-for="reply3 in reply2.replies" :key="reply3.id">
                                    <Comment
                                        :reply_to_id="reply3.id"
                                        :date="reply3.created_at"
                                        :body="reply3.body"
                                        :owner_name="reply3.owner.name"
                                        class-name="ml-16 sm:ml-28"
                                    />
                                </template>
                            </template>
                        </template>
                    </template>

                    <!-- Empty comments -->
                    <div v-if="blog.comments.length === 0" class="px-5 py-10 text-center text-gray-400">
                        <i class="fas fa-comment-slash text-3xl mb-2 opacity-30"></i>
                        <p class="text-sm">No comments yet. Be the first!</p>
                    </div>
                </div>

                <!-- Leave a comment -->
                <div class="bg-gray-50 border-t border-gray-100 px-5 py-5">
                    <h3 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                        <i class="fas fa-pen text-green-500 text-sm"></i> Leave a Comment
                    </h3>
                    <textarea
                        v-model="body"
                        @keyup.enter.exact="onSubmit"
                        rows="4"
                        placeholder="Write your comment here..."
                        class="w-full text-sm border border-gray-200 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent resize-none bg-white"
                    ></textarea>
                    <div class="flex justify-end mt-3">
                        <button
                            @click="onSubmit"
                            class="flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-xl transition-colors"
                        >
                            <i class="fas fa-paper-plane text-xs"></i> Post Comment
                        </button>
                    </div>
                </div>
            </div>
        </article>

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

            <!-- About -->
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
import { ref, reactive } from 'vue';
import { ElMessage } from 'element-plus';
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import Comment from '@/Components/Comment.vue';

const { products, blog, recent_blogs } = defineProps(['products', 'blog', 'recent_blogs']);

const form_errors = reactive({ value: {} });
const body = ref(null);

function onSubmit() {
    axios.post(route('comment.store'), { body: body.value, blog_id: blog.id })
        .then(() => {
            ElMessage({ type: 'success', message: 'Commented successfully.' });
            form_errors.value = {};
            body.value = null;
            router.reload();
        })
        .catch(err => { form_errors.value = err.response.data.errors; });
}
</script>
