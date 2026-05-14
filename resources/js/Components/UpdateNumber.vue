<template>
    <el-dialog 
        :width="isPC() ? '50%' : '70%'" 
        v-model="showModal" 
        :title="'Update Number'"
        :close-on-click-modal="false">

        <img loading="lazy" :src="`${$page.props.base_img_path}website/${$page.props.setting.logo}`" class="w-[70%] lg:w-[300px]" alt="">


        <div>
            <label for="">Number</label>
            <el-input
                v-model="form_data"
                placeholder="Enter number"
                type="text"
            />
        </div>
        
        <div class="mt-4">
            <label for="">GCash QR Code</label> <br>
            <input @change="onChangeImage" type="file" accept="image/*" class="w-fit mt-1" >
        </div>

        <template #footer>
            <span class="flex justify-end">
                <el-button 
                    type="warning" 
                    @click="showModal = false" 
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
import { ref, watch } from 'vue';

    const setting = usePage().props.setting;

    const props = defineProps({
        onClose: {
            type: Function,
            default: () => {},
        },
    })

    const showModal = ref(true);
    const form_errors = ref(null)
    const form_data = ref(setting.number)
    const image = ref(null)

    watch(showModal, (val, _) => {
        if (!val){
            props.onClose()
        }
    })

    function isPC(){
        return window.innerWidth > 960
    }

    function onChangeImage(e){
        image.value = e.target.files[0]
    }

    function onSubmitUpdate(){
        axios.post(route('setting.update-number'),{
            number:form_data.value,
            image: image.value
        },{
            headers: {
                'content-type': 'multipart/form-data'
            }
        })
            .then(()=>{
                router.visit(window.location.href, {preserveScroll: true})
            })
            .catch(function(error){
                form_errors.value = error.response.data.errors
            })
    }
</script>