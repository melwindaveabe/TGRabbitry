<script setup>
import SideBarItem from '@/Components/SideBarItem.vue';
import UpdateProfile from '@/Components/UpdateProfile.vue';
import UpdatePassword from '@/Components/UpdatePassword.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import UpdateDeal from '@/Components/UpdateDeal.vue';
import UpdateNumber from '@/Components/UpdateNumber.vue';
import UpdateQuote from '@/Components/UpdateQuote.vue';
import UpdateSettings from '@/Components/UpdateSettings.vue';

const isDesktop = ref(window.innerWidth > 960);
const showNav   = ref(window.innerWidth > 960);

const showProfileModal  = ref(false);
const showNotif         = ref(false);
const showPasswordModal = ref(false);
const showDealModal     = ref(false);
const showNumberModal   = ref(false);
const showQuoteModal    = ref(false);
const showSettingsModal = ref(false);

const notifRef = ref(null);
const tasks = usePage().props.tasks;

function onResize() {
    isDesktop.value = window.innerWidth > 960;
    if (isDesktop.value) showNav.value = true;
    else showNav.value = false;
}

function onClickOutsideNotif(e) {
    if (showNotif.value && notifRef.value && !notifRef.value.contains(e.target)) {
        showNotif.value = false;
    }
}

window.addEventListener('resize', onResize);
window.addEventListener('mousedown', onClickOutsideNotif);
onBeforeUnmount(() => {
    window.removeEventListener('resize', onResize);
    window.removeEventListener('mousedown', onClickOutsideNotif);
});

function closeIfMobile() {
    if (!isDesktop.value) showNav.value = false;
}

function onNav(ev) {
    showNav.value = !showNav.value;
    ev.stopPropagation();
}

function logout()   { router.post(route('logout')); }
function onDeal()   { closeIfMobile(); showDealModal.value    = true; }
function onNumber() { closeIfMobile(); showNumberModal.value  = true; }
function onSettings(){ closeIfMobile(); showSettingsModal.value = true; }
function onQuote()  { closeIfMobile(); showQuoteModal.value   = true; }

function getName(name) {
    if (isDesktop.value || name.length < 18) return name;
    return name.substring(0, 17) + '…';
}
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-gray-50">

        <!-- ── Mobile backdrop ── -->
        <transition name="fade">
            <div
                v-if="!isDesktop && showNav"
                class="fixed inset-0 bg-black/40 z-[998]"
                @click="showNav = false"
            ></div>
        </transition>

        <!-- ── Sidebar ── -->
        <transition name="slide-sidebar">
            <div
                v-show="showNav"
                id="tg-side"
                :class="[
                    'flex flex-col h-screen bg-white border-r border-gray-200 shadow-sm z-[999]',
                    'w-[var(--sidebar-width)]',
                    isDesktop ? 'relative flex-shrink-0' : 'fixed left-0 top-0'
                ]"
            >
                <!-- Logo -->
                <div class="flex justify-center items-center h-[64px] bg-green-600 flex-shrink-0">
                    <img class="h-[40px] w-auto object-contain" :src="`/images/website/${$page.props.setting.logo}`" alt="Logo">
                </div>

                <!-- Nav items -->
                <div class="flex-1 overflow-y-auto py-3 px-2">

                    <SideBarItem :href="route('dashboard')"          icon="fas fa-chart-line"          label="Dashboard"       :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('admin.message.list')" icon="fas fa-comment-dots"         label="Messages"        :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('order.index')"        icon="fas fa-shop"                 label="Order List"      :closeIfMobile="closeIfMobile" />

                    <!-- Product -->
                    <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Product</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <SideBarItem :href="route('product.index')"     icon="fas fa-cart-shopping"        label="Product List"    :closeIfMobile="closeIfMobile" :exact="true" />
                    <SideBarItem :href="route('product.inventory')" icon="fas fa-boxes-stacked"        label="Inventory List"  :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('sales-report')"      icon="fas fa-chart-bar"            label="Sales Report"    :closeIfMobile="closeIfMobile" />

                    <!-- Rabbit -->
                    <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Rabbit</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <SideBarItem :href="route('rabbit.dashboard')" icon="fas fa-clone"         label="Summary"          :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('rabbit.index')"     icon="fas fa-paw"           label="Rabbit"           :closeIfMobile="closeIfMobile" :exact="true" />
                    <SideBarItem :href="route('rabbit.breeding')"  icon="fas fa-heart"         label="Rabbit Breeding"  :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('rabbit.weight')"    icon="fas fa-weight-scale"  label="Weight Tracker"   :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('rabbit.illness')"   icon="fa-solid fa-syringe"  label="Illness History"  :closeIfMobile="closeIfMobile" />

                    <!-- Budget -->
                    <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Budget</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <SideBarItem :href="route('category.index')"       icon="fas fa-table"                label="Category"        :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('budget.index')"         icon="fas fa-landmark"             label="Budget List"     :closeIfMobile="closeIfMobile" :exact="true" />
                    <SideBarItem :href="route('budget.expense')"       icon="fas fa-money-bill-trend-up"  label="Expense List"    :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('budget.report')"        icon="fas fa-file-invoice-dollar"  label="Budget Report"   :closeIfMobile="closeIfMobile" />
                    <SideBarItem :href="route('budget.expense-report')" icon="fas fa-file-invoice"        label="Expense Report"  :closeIfMobile="closeIfMobile" />

                    <!-- Task -->
                    <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Task</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <SideBarItem :href="route('task.index')"     icon="fas fa-heart-pulse"  label="Rabbit Care"     :closeIfMobile="closeIfMobile" :exact="true" />
                    <SideBarItem :href="route('task.completed')" icon="fas fa-check-square" label="Completed Task"  :closeIfMobile="closeIfMobile" />

                    <!-- Blog -->
                    <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Blog</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <SideBarItem :href="route('blog.index')" icon="fas fa-pen-to-square" label="Blog" :closeIfMobile="closeIfMobile" :exact="true" :inactive="showQuoteModal" />
                    <button
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-2.5 font-medium text-sm transition-all rounded-lg my-0.5',
                            showQuoteModal
                                ? 'bg-green-600 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
                        ]"
                        style="width: calc(100% - 8px)"
                        @click="onQuote"
                    >
                        <i class="fas fa-book-open-reader w-4 text-center text-[14px]"></i>
                        <span>Update Blog Quote</span>
                    </button>

                    <!-- Settings -->
                    <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                        <div class="flex-1 h-px bg-gray-200"></div>
                        <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Settings</span>
                        <div class="flex-1 h-px bg-gray-200"></div>
                    </div>
                    <button
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-2.5 font-medium text-sm transition-all rounded-lg my-0.5',
                            showSettingsModal
                                ? 'bg-green-600 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
                        ]"
                        style="width: calc(100% - 8px)"
                        @click="onSettings"
                    >
                        <i class="fas fa-cog w-4 text-center text-[14px]"></i>
                        <span>Update Settings</span>
                    </button>
                    <button
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-2.5 font-medium text-sm transition-all rounded-lg my-0.5',
                            showNumberModal
                                ? 'bg-green-600 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
                        ]"
                        style="width: calc(100% - 8px)"
                        @click="onNumber"
                    >
                        <i class="fas fa-phone w-4 text-center text-[14px]"></i>
                        <span>Update Number</span>
                    </button>
                    <button
                        :class="[
                            'w-full flex items-center gap-3 px-4 py-2.5 font-medium text-sm transition-all rounded-lg my-0.5',
                            showDealModal
                                ? 'bg-green-600 text-white shadow-sm'
                                : 'text-gray-600 hover:bg-green-50 hover:text-green-700'
                        ]"
                        style="width: calc(100% - 8px)"
                        @click="onDeal"
                    >                        <i class="fas fa-square-check w-4 text-center text-[14px]"></i>
                        <span>Update Deal</span>
                    </button>
                    <SideBarItem :href="route('log')" icon="fas fa-note-sticky" label="Logs" :closeIfMobile="closeIfMobile" />

                    <div class="h-4"></div>
                </div>
            </div>
        </transition>

        <!-- ── Main content — always full width on mobile ── -->
        <div class="flex-1 flex flex-col min-w-0 w-0">

            <!-- Top navbar -->
            <div class="flex pr-4 py-0 pl-3 h-[var(--nav-height)] items-center bg-green-600 text-white shadow-md justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <button class="cursor-pointer p-1.5 rounded-lg hover:bg-green-700 transition-colors" @click="onNav">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
                            <path fill="currentColor" d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z"/>
                        </svg>
                    </button>
                    <span class="font-semibold text-sm tracking-wide">
                        {{ getName($page.props.setting.name) }}
                    </span>
                </div>

                <div class="flex items-center gap-3">
                    <!-- Bell -->
                    <div class="relative" ref="notifRef">
                        <button
                            class="relative p-1.5 rounded-full hover:bg-green-700 transition-colors"
                            @click="showNotif = !showNotif"
                        >
                            <i class="fas fa-bell text-base"></i>
                            <span v-if="tasks.length > 0" class="absolute top-0.5 right-0.5 size-4 bg-red-500 text-white text-[9px] font-bold rounded-full flex items-center justify-center">
                                {{ tasks.length > 9 ? '9+' : tasks.length }}
                            </span>
                        </button>

                        <transition name="notif-drop">
                            <div
                                v-if="showNotif"
                                class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 text-gray-800 z-50 overflow-hidden"
                            >
                                <!-- Header -->
                                <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-bell text-green-500 text-sm"></i>
                                        <span class="font-bold text-sm text-gray-800">Notifications</span>
                                    </div>
                                    <span v-if="tasks.length > 0" class="bg-green-100 text-green-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                                        {{ tasks.length }} pending
                                    </span>
                                </div>

                                <!-- Task list -->
                                <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                    <template v-if="tasks.length > 0">
                                        <div
                                            v-for="task in tasks"
                                            :key="task.id"
                                            class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors"
                                        >
                                            <div class="w-8 h-8 rounded-xl bg-green-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                <i class="fas fa-heart-pulse text-green-600 text-xs"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="font-semibold text-sm text-gray-800 truncate">{{ task.title }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ task.desc }}</p>
                                                <p v-if="task.due_date" class="text-[10px] text-gray-400 mt-1 flex items-center gap-1">
                                                    <i class="fas fa-calendar text-[9px]"></i>
                                                    {{ task.due_date?.substring(0, 10) }}
                                                </p>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                                            <i class="fas fa-bell-slash text-3xl mb-2 opacity-30"></i>
                                            <p class="text-sm font-medium">No tasks scheduled yet.</p>
                                        </div>
                                    </template>
                                </div>

                                <!-- Footer link -->
                                <div v-if="tasks.length > 0" class="border-t border-gray-100 px-4 py-2.5">
                                    <a :href="route('task.index')" class="text-xs text-green-600 hover:text-green-700 font-semibold flex items-center gap-1 transition-colors">
                                        View all tasks <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </transition>
                    </div>

                    <!-- User menu -->
                    <el-dropdown trigger="click" placement="bottom-end">
                        <button class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-green-700 transition-colors text-white">
                            <div class="size-8 rounded-full bg-white/20 border-2 border-white/40 flex items-center justify-center text-xs font-bold text-white">
                                {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() ?? 'A' }}
                            </div>
                            <i class="fas fa-chevron-down text-xs opacity-70"></i>
                        </button>
                        <template #dropdown>
                            <div class="py-1 min-w-[200px]">
                                <!-- User info header -->
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="size-9 rounded-xl bg-green-100 flex items-center justify-center text-green-700 font-bold text-sm flex-shrink-0">
                                            {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() ?? 'A' }}
                                        </div>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-sm text-gray-800 truncate">{{ $page.props.auth?.user?.name }}</p>
                                            <p class="text-xs text-gray-400 truncate">{{ $page.props.auth?.user?.email }}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Menu items -->
                                <div class="py-1">
                                    <button
                                        @click="showProfileModal = true"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-green-700 transition-colors text-left"
                                    >
                                        <div class="size-7 rounded-lg bg-green-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-user text-green-600 text-xs"></i>
                                        </div>
                                        <span class="font-medium">Update Profile</span>
                                    </button>
                                    <button
                                        @click="showPasswordModal = true"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-blue-700 transition-colors text-left"
                                    >
                                        <div class="size-7 rounded-lg bg-blue-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-lock text-blue-600 text-xs"></i>
                                        </div>
                                        <span class="font-medium">Update Password</span>
                                    </button>
                                </div>

                                <!-- Logout -->
                                <div class="border-t border-gray-100 py-1">
                                    <button
                                        @click="logout"
                                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors text-left"
                                    >
                                        <div class="size-7 rounded-lg bg-red-50 flex items-center justify-center flex-shrink-0">
                                            <i class="fas fa-power-off text-red-500 text-xs"></i>
                                        </div>
                                        <span class="font-medium">Logout</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </el-dropdown>
                </div>
            </div>

            <!-- Page content -->
            <main class="flex-1 overflow-auto p-4 sm:p-5 bg-gray-50">
                <slot />
            </main>
        </div>
    </div>

    <UpdateProfile   v-if="showProfileModal"  :on-close="() => showProfileModal  = false" />
    <UpdatePassword  v-if="showPasswordModal" :on-close="() => showPasswordModal = false" />
    <UpdateDeal      v-if="showDealModal"     :on-close="() => showDealModal     = false" />
    <UpdateNumber    v-if="showNumberModal"   :on-close="() => showNumberModal   = false" />
    <UpdateQuote     v-if="showQuoteModal"    :on-close="() => showQuoteModal    = false" />
    <UpdateSettings  v-if="showSettingsModal" :on-close="() => showSettingsModal = false" />
</template>

<style scoped>
/* Sidebar slide-in/out */
.slide-sidebar-enter-active,
.slide-sidebar-leave-active {
    transition: transform 280ms ease;
}
.slide-sidebar-enter-from,
.slide-sidebar-leave-to {
    transform: translateX(-100%);
}
.slide-sidebar-enter-to,
.slide-sidebar-leave-from {
    transform: translateX(0);
}

/* Backdrop fade */
.fade-enter-active,
.fade-leave-active {
    transition: opacity 250ms ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

/* Notification dropdown */
.notif-drop-enter-active,
.notif-drop-leave-active {
    transition: opacity 180ms ease, transform 180ms ease;
}
.notif-drop-enter-from,
.notif-drop-leave-to {
    opacity: 0;
    transform: translateY(-6px) scale(0.97);
}
.notif-drop-enter-to,
.notif-drop-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>
