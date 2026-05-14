<script setup>
import SideBarItem from '@/Components/SideBarItem.vue';
import UpdateProfile from '@/Components/UpdateProfile.vue';
import UpdatePassword from '@/Components/UpdatePassword.vue';
import { ref, onMounted } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import UpdateDeal from '@/Components/UpdateDeal.vue';
import UpdateNumber from '@/Components/UpdateNumber.vue';
import UpdateQuote from '@/Components/UpdateQuote.vue';
import UpdateSettings from '@/Components/UpdateSettings.vue';

    const showNav = ref(window.innerWidth  > 960);
    const  showProfileModal = ref(false);
    const  showNotif = ref(false);
    const  hid = ref(window.innerWidth  < 961);
    const  animate = ref(false);
    const  showPasswordModal = ref(false);
    const  showDealModal = ref(false);
    const  showNumberModal = ref(false);
    const  showQuoteModal = ref(false);
    const showSettingsModal= ref(false)

    const tasks = usePage().props.tasks

    window.addEventListener('resize', () => showNav.value =  window.innerWidth > 960)

    onMounted(() => {
        document
            .getElementById('tg-side')
            .addEventListener('animationend', () => {
                hid.value= !hid.value
                animate.value = false
            })
    })

    function isPC (){
        return window.innerWidth > 960
    }

    function closeIfMobile(){
        if (window.innerWidth < 961 && showNav.value){
            animate.value = true
            showNav.value = false
        }
    }

    function onNav(ev){
        animate.value = true
        showNav.value = !showNav.value
        ev.stopPropagation();
    }

    function logout(){
        router.post(route('logout'))
    }

    function onDeal(){
        closeIfMobile()
        showDealModal.value = true
    }

    function onNumber(){
        closeIfMobile()
        showNumberModal.value = true
    }

    function onSettings(){
        closeIfMobile()
        showSettingsModal.value = true
    }

    function onQuote(){
        closeIfMobile()
        showQuoteModal.value = true
    }

    function getName(name){
        if (isPC() || name.length < 18) return name
        return name.substring(0, 17) + '...'
    }
</script>

<template>
    <div class="flex h-screen overflow-hidden bg-gray-50">

        <!-- Sidebar -->
        <div
            :id="`tg-side`"
            :class="`
                ${isPC() ? 'sticky' : 'fixed'}
                ${animate ? `tg-sidebar${showNav ? '' : '-gone'}` : ''}
                ${hid ? 'hidden' : 'flex flex-col'}
                left-0 w-[var(--sidebar-width)] z-[999] h-screen
                bg-white border-r border-gray-200 shadow-sm
            `"
        >
            <!-- Logo -->
            <div class="flex justify-center items-center h-[64px] bg-green-600 flex-shrink-0">
                <img class="h-[40px] w-auto object-contain" :src="`/images/website/${$page.props.setting.logo}`">
            </div>

            <!-- Nav items -->
            <div class="flex-1 overflow-y-auto py-3 px-2">

                <SideBarItem
                    :href="route('dashboard')"
                    icon="fas fa-chart-line"
                    label="Dashboard"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('admin.message.list')"
                    icon="fas fa-comment-dots"
                    label="Messages"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('order.index')"
                    icon="fas fa-shop"
                    label="Order List"
                    :closeIfMobile="closeIfMobile"
                />

                <!-- Section: Product -->
                <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Product</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <SideBarItem
                    :href="route('product.index')"
                    icon="fas fa-cart-shopping"
                    label="Product List"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('product.inventory')"
                    icon="fas fa-boxes-stacked"
                    label="Inventory List"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('sales-report')"
                    icon="fas fa-chart-bar"
                    label="Sales Report"
                    :closeIfMobile="closeIfMobile"
                />

                <!-- Section: Rabbit Management -->
                <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Rabbit</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <SideBarItem
                    :href="route('rabbit.dashboard')"
                    icon="fas fa-clone"
                    label="Summary"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('rabbit.index')"
                    icon="fas fa-paw"
                    label="Rabbit"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('rabbit.breeding')"
                    icon="fas fa-heart"
                    label="Rabbit Breeding"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('rabbit.weight')"
                    icon="fas fa-weight-scale"
                    label="Weight Tracker"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('rabbit.illness')"
                    icon="fa-solid fa-syringe"
                    label="Illness History"
                    :closeIfMobile="closeIfMobile"
                />

                <!-- Section: Budget -->
                <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Budget</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <SideBarItem
                    :href="route('category.index')"
                    icon="fas fa-table"
                    label="Category"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('budget.index')"
                    icon="fas fa-landmark"
                    label="Budget List"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('budget.expense')"
                    icon="fas fa-money-bill-trend-up"
                    label="Expense List"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('budget.report')"
                    icon="fas fa-file-invoice-dollar"
                    label="Budget Report"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('budget.expense-report')"
                    icon="fas fa-file-invoice"
                    label="Expense Report"
                    :closeIfMobile="closeIfMobile"
                />

                <!-- Section: Task -->
                <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Task</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <SideBarItem
                    :href="route('task.index')"
                    icon="fas fa-heart-pulse"
                    label="Rabbit Care"
                    :closeIfMobile="closeIfMobile"
                />
                <SideBarItem
                    :href="route('task.completed')"
                    icon="fas fa-check-square"
                    label="Completed Task"
                    :closeIfMobile="closeIfMobile"
                />

                <!-- Section: Blog -->
                <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Blog</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <SideBarItem
                    :href="route('blog.index')"
                    icon="fas fa-pen-to-square"
                    label="Blog"
                    :closeIfMobile="closeIfMobile"
                />
                <button
                    class="w-full flex items-center gap-3 px-4 py-2.5 cursor-pointer font-medium text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-150 rounded-lg my-0.5"
                    @click="onQuote"
                >
                    <i class="fas fa-book-open-reader w-4 text-center text-[14px]"></i>
                    <span>Update Blog Quote</span>
                </button>

                <!-- Section: Settings -->
                <div class="flex items-center gap-2 px-2 mt-4 mb-1">
                    <div class="flex-1 h-px bg-gray-200"></div>
                    <span class="text-[10px] font-semibold text-gray-400 uppercase tracking-widest whitespace-nowrap">Settings</span>
                    <div class="flex-1 h-px bg-gray-200"></div>
                </div>

                <button
                    class="w-full flex items-center gap-3 px-4 py-2.5 cursor-pointer font-medium text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-150 rounded-lg my-0.5"
                    @click="onSettings"
                >
                    <i class="fas fa-cog w-4 text-center text-[14px]"></i>
                    <span>Update Settings</span>
                </button>
                <button
                    class="w-full flex items-center gap-3 px-4 py-2.5 cursor-pointer font-medium text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-150 rounded-lg my-0.5"
                    @click="onNumber"
                >
                    <i class="fas fa-phone w-4 text-center text-[14px]"></i>
                    <span>Update Number</span>
                </button>
                <button
                    class="w-full flex items-center gap-3 px-4 py-2.5 cursor-pointer font-medium text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 transition-all duration-150 rounded-lg my-0.5"
                    @click="onDeal"
                >
                    <i class="fas fa-square-check w-4 text-center text-[14px]"></i>
                    <span>Update Deal</span>
                </button>

                <SideBarItem
                    :href="route('log')"
                    icon="fas fa-note-sticky"
                    label="Logs"
                    :closeIfMobile="closeIfMobile"
                />

                <div class="h-4"></div>
            </div>
        </div>

        <!-- Main content -->
        <div class="flex-1 flex flex-col min-w-0" @click="closeIfMobile">

            <!-- Top navbar -->
            <div class="flex pr-4 py-0 pl-3 h-[var(--nav-height)] items-center bg-green-600 text-white shadow-md justify-between flex-shrink-0">
                <div class="flex items-center gap-3">
                    <button class="cursor-pointer p-1 rounded hover:bg-green-700 transition-colors" @click="onNav">
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
                    <div class="relative">
                        <button
                            class="relative p-1.5 rounded-full hover:bg-green-700 transition-colors"
                            @click="showNotif = !showNotif"
                        >
                            <i class="fas fa-bell text-base"></i>
                            <span
                                v-if="tasks.length > 0"
                                class="absolute top-0.5 right-0.5 size-2 bg-red-500 rounded-full"
                            ></span>
                        </button>

                        <div
                            v-if="showNotif"
                            class="absolute right-0 mt-2 w-72 bg-white rounded-xl shadow-xl border border-gray-100 text-gray-800 z-50 overflow-hidden"
                        >
                            <div class="px-4 py-3 border-b border-gray-100 font-semibold text-sm text-gray-700">
                                Notifications
                            </div>
                            <div class="max-h-64 overflow-y-auto">
                                <template v-if="tasks.length > 0">
                                    <div v-for="task in tasks" :key="task.id" class="px-4 py-3 border-b border-gray-50 hover:bg-gray-50">
                                        <div class="font-medium text-sm">{{ task.title }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ task.desc }}</div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="px-4 py-6 text-center text-sm text-gray-400">No tasks scheduled yet.</div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- User menu -->
                    <el-dropdown>
                        <button class="flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-green-700 transition-colors text-white">
                            <div class="size-7 rounded-full bg-green-400 flex items-center justify-center text-xs font-bold">
                                {{ $page.props.auth?.user?.name?.charAt(0)?.toUpperCase() ?? 'A' }}
                            </div>
                            <i class="fas fa-chevron-down text-xs opacity-70"></i>
                        </button>
                        <template #dropdown>
                            <el-dropdown-menu>
                                <el-dropdown-item @click="showProfileModal = true">
                                    <i class="fas fa-user mr-2"></i> Update Profile
                                </el-dropdown-item>
                                <el-dropdown-item @click="showPasswordModal = true">
                                    <i class="fas fa-lock mr-2"></i> Update Password
                                </el-dropdown-item>
                                <el-dropdown-item @click="logout" divided>
                                    <i class="fas fa-power-off mr-2 text-red-500"></i>
                                    <span class="text-red-500">Logout</span>
                                </el-dropdown-item>
                            </el-dropdown-menu>
                        </template>
                    </el-dropdown>
                </div>
            </div>

            <!-- Page content -->
            <main
                :class="`flex-1 overflow-auto p-5 bg-gray-50 ${isPC() ? 'max-w-[calc(100vw-var(--sidebar-width))]' : 'max-w-[100vw]'}`"
            >
                <slot />
            </main>
        </div>
    </div>

    <UpdateProfile v-if="showProfileModal" :on-close="() => showProfileModal = false" />
    <UpdatePassword v-if="showPasswordModal" :on-close="() => showPasswordModal = false" />
    <UpdateDeal v-if="showDealModal" :on-close="() => showDealModal = false" />
    <UpdateNumber v-if="showNumberModal" :on-close="() => showNumberModal = false" />
    <UpdateQuote v-if="showQuoteModal" :on-close="() => showQuoteModal = false" />
    <UpdateSettings v-if="showSettingsModal" :on-close="() => showSettingsModal = false" />
</template>

<style scoped>
.tg-sidebar {
    animation: slide 300ms ease-out;
    width: var(--sidebar-width);
    display: flex;
}
.tg-sidebar-gone {
    animation: slide-gone 300ms ease-in;
    width: 0px;
}

@keyframes slide {
    from { width: 0px; }
    to   { width: var(--sidebar-width); }
}
@keyframes slide-gone {
    from { width: var(--sidebar-width); }
    to   { width: 0px; }
}
</style>
