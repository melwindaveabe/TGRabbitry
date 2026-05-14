<template>
    <el-dialog 
        :width="isPC() ? '50%' : '70%'" 
        v-model="showProfileModal" 
        :title="''"
        :close-on-click-modal="false">

        <img loading="lazy" :src="`${$page.props.base_img_path}website/${$page.props.setting.logo}`" class="w-[70%] lg:w-[300px]" alt="">


        <center>
            <h1 class="mb-2 font-bold text-[20px] text-black">UPDATE PROFILE</h1>
            
            <div class="my-2">
                <img loading="lazy" :src="`${$page.props.base_img_path}${$page.props.user.image_name}`" class="w-[70%] lg:w-[100px] mb-2" alt="">

                <input @change="onChangeImage" type="file" accept="image/*" class="w-fit" >
                <!-- <el-button 
                    type="primary" 
                    @click="browseImg" 
                >Browse</el-button> -->
            </div>
        </center>
        <div>
            <label for="">Name<span class="text-red-600">*</span></label>
            <el-input
                v-model="form_data.name"
                placeholder="Enter Name"
                type="text"
                clearable
            />
            <span
                v-if="form_errors.value.name"
                class="text-red-600"
            >{{form_errors.value.name[0]}}</span>
        </div>

        <div class="pt-2">
            <label for="">City<span class="text-red-600">*</span></label>
            <el-select v-model="form_data.city" placeholder="Select city">
                <el-option value="Mansalay" label="Mansalay" />
                <el-option value="Roxas" label="Roxas" />
                <el-option value="Bongabong" label="Bongabong" />
                <el-option value="Bansud" label="Bansud" />
            </el-select>
            <span
                v-if="form_errors.value.city"
                class="text-red-600"
            >{{form_errors.value.city[0]}}</span>
        </div>

        <div class="pt-2">
            <label for="">Address<span class="text-red-600">*</span></label>
            <el-input
                v-model="form_data.address"
                placeholder="Enter Address."
                type="text"
                clearable
                autosize
            />
            <span
                v-if="form_errors.value.address"
                class="text-red-600"
            >{{form_errors.value.address[0]}}</span>
        </div>

        <div class="pt-2">
            <label for="">Email <span class="text-red-600">*</span></label>
            <el-input
                v-model="form_data.email"
                placeholder="Enter Email."
                type="text"
                clearable
                autosize
            />
            <span
                v-if="form_errors.value.email"
                class="text-red-600"
            >{{form_errors.value.email[0]}}</span>
        </div>

        <div class="pt-2" v-if="$page.props.user.admin == 0">
            <label for="">Phone No. <span class="text-red-600">*</span></label>
            <el-input
                v-model="form_data.phone_no"
                placeholder="Enter phone number"
                type="text"
                clearable
                autosize
            />
            <span
                v-if="form_errors.value.phone_no"
                class="text-red-600"
            >{{form_errors.value.phone_no[0]}}</span>
        </div>

        <template #footer>
            <span class="flex justify-end">
                <el-button 
                    type="warning" 
                    @click="showProfileModal = false" 
                    plain
                >Cancel</el-button>
                <el-button 
                    type="primary" 
                    @click="onSubmitUpdate"
                >Update</el-button>
            </span>
        </template>
    </el-dialog>
</template>

<script setup>
import { usePage, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref, reactive, watch } from 'vue';

    const user = usePage().props.user;

    const props = defineProps({
        onClose: {
            type: Function,
            default: () => {},
        },
    })

    const  showProfileModal = ref(true);
    const form_errors = reactive({value:{}})
    const form_data = reactive({
        name:user.name,
        address:user.address,
        city:user.city,
        email:user.email,
        phone_no:user.phone_no,
        image: null,
    })

    watch(showProfileModal, (val, _) => {
        if (!val){
            props.onClose()
        }
    })

    function onChangeImage(e){
        form_data.image = e.target.files[0]
    }

    function isPC(){
        return window.innerWidth > 960
    }

    function onSubmitUpdate(){
        axios.post(route('profile.update'),form_data,{
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
        .then(()=>{
            // window.location.reload()
            router.visit(window.location.href, {preserveScroll: true})
        })
        .catch(function(error){
            form_errors.value = error.response.data.errors
        })
    }
</script>