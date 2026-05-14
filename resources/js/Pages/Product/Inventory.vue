<template>
    <AuthLayout>
        <div class="flex justify-between flex-wrap">
            <div class="text-lg font-semibold"></div>

        </div>
        <el-card shadow="always" class="mt-3">
                <div class="flex gap-2 flex-wrap justify-center sm:justify-start bg-white">                    
                    <div class="font-semibold text-lg w-full">
                        Inventory Summary
                    </div>
                    
                    <DashDetails
                        class-name="w-full md:w-[32%] sm:w-[49%]" 
                        icon="fas fa-coins" 
                        title="Total Number of Products" 
                        :value="data.value.length" 
                        dark="true" 
                        icon-color="green"
                    />
                    
                    <DashDetails
                        class-name="w-full md:w-[32%] sm:w-[49%]" 
                        icon="fas fa-coins" 
                        title="Total Quantity in Stocks"
                        :value="data.value.reduce((last, val) => parseInt(val.stock) + last, 0)" 
                        dark="true" 
                        icon-color="green"
                    />
                    
                    <DashDetails
                        class-name="w-full md:w-[32%] sm:w-[49%]" 
                        icon="fas fa-coins" 
                        title="Total Stock Value"
                        :value="data.value.reduce((last, val) => (parseInt(val.stock) * parseInt(val.price)) + last, 0).toLocaleString()" 
                        dark="true" 
                        icon-color="green"
                    />

                    
                </div>
            </el-card>

        <el-card class="mt-4" shadow="always">
            <h1 class="text-left text-[20px] font-semibold">Inventory</h1>
            <el-table
                :data="data.value"
                row-key="id"
                header-row-class-name="thead-light"
            >
                <el-table-column
                    min-width="120px"
                    label="Name"
                >
                    <template v-slot="{ row }">
                        {{row.name}}{{  row.rabbit ? '/' + row.rabbit.name : '' }}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="130px" 
                    label="Category"
                >
                    <template v-slot="{ row }">
                        {{row.category}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="150px" 
                    label="Description"
                >
                    <template v-slot="{ row }">
                        {{row.desc}}
                    </template>
                </el-table-column>

                <!-- <el-table-column 
                    min-width="150px" 
                    label="Stocks"
                >
                    <template v-slot="{ row }">
                        {{row.stock}}
                    </template>
                </el-table-column> -->

                <el-table-column 
                    min-width="150px" 
                    label="Unit Price"
                >
                    <template v-slot="{ row }">
                        {{ numberWithCommas(row.price)}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="150px" 
                    label="Total Value"
                >
                    <template v-slot="{ row }">
                        {{ numberWithCommas(parseInt(row.price) * parseInt(row.stock))}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="150px" 
                    label=" Remaining Stocks"
                >
                    <template v-slot="{ row }">
                        {{row.stock}}
                    </template>
                </el-table-column>

                <!-- <el-table-column 
                    min-width="150px" 
                    label="Date of Last Stock Update"
                >
                    <template v-slot="{ row }">
                        {{row.date}}
                    </template>
                </el-table-column> -->

                
            </el-table>
        </el-card>


<div class="grid grid-cols-1 xl:grid-cols-2 lg:grid-cols-2 md:grid-cols-1 sm:grid-cols-1 gap-4 mt-5 w-full">
    <div class="xl:col-span-2 lg:col-span-2 md:col-span-1 sm:col-span-1">
        <el-card class="h-full">
        <h1 class="text-center text-[20px] font-semibold">Highest Sale</h1>
        <el-table
            :data="highest_sale_data.value"
            row-key="id"
            header-row-class-name="thead-light"
        >
        <el-table-column
            min-width="120px"
            label="Category"
        >
            <template v-slot="{ row }">
                {{ row.category }}
            </template>
        </el-table-column>
        <el-table-column
            min-width="120px"
            label="Product Name"
        >
            <template v-slot="{ row }">
                {{ row.name }}
            </template>
        </el-table-column>
        <el-table-column
            min-width="120px"
            label="Total Sales"
        >
            <template v-slot="{ row }">
                {{ row.total_sale }}
            </template>
        </el-table-column>
        </el-table>
        </el-card>
    </div>

    <div class="xl:col-span-1 lg:col-span-1 md:col-span-1 sm:col-span-1">
        <el-card class="h-full">
        <h1 class="text-center text-[20px] font-semibold">New Added Product</h1>
        <el-table
            :data="new_added_prod.value"
            row-key="id"
            header-row-class-name="thead-light"
        >
        <el-table-column 
                label="Image"
                min-width="100px" 
            >
                <template v-slot="{ row }">
                    <img 
                        loading="lazy" 
                        class="w-full aspect-[1/1] object-center object-cover transition duration-300 transform hover:scale-10" 
                        :src="$page.props.base_img_path + row.image_names"
                        alt=""
                    >
                </template>
            </el-table-column>

            <el-table-column
                min-width="120px"
                label="Category"
            >
                <template v-slot="{row}">
                    {{ row.category }}
                </template>
            </el-table-column>
            <el-table-column
                min-width="120px"
                label="Product Name"
            >
                <template v-slot="{row}">
                    {{ row.name }}
                </template>
            </el-table-column>
            <el-table-column
                min-width="120px"
                label="Price"
            >
                <template v-slot="{row}">
                    {{ numberWithCommas(row.price ) }}
                </template>
            </el-table-column>
        </el-table>
        </el-card>
    </div>

    <div class="xl:col-span-1 lg:col-span-1 md:col-span-1 sm:col-span-1">
        <el-card class="h-full">
            <h1 class="text-center text-[20px] font-semibold">Low Stock Alert</h1>
            <el-table
                :data="low_stocks"
                row-key="id"
                header-row-class-name="thead-light"
            >
                <el-table-column
                    min-width="120px"
                    label="Name"
                >
                    <template v-slot="{row}">
                        {{ row.name }}
                    </template>
                </el-table-column>
                <el-table-column
                    min-width="120px"
                    label="Current Stock"
                >
                    <template v-slot="{row}">
                        {{ row.stock }}
                    </template>
                </el-table-column>

            </el-table>
        </el-card>
    </div>
</div>
        

    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import DashDetails from '@/Components/DashDetails.vue';
import { onBeforeMount, reactive, ref, computed } from 'vue';
import axios from 'axios';

    const {low_stocks} = defineProps(['low_stocks'])

    const pagination = reactive({
        search: null,
        page_size:10,
        page:1,
    })

    const new_added_prod = reactive({
        value:[]
    })
    const highest_sale_data = reactive({
        value:[]
    })
    const pagination_total = ref(0)
    const showModal = ref(false)
    // const breed = ref(null)
    const imgUpload = ref(null)
    const data = reactive({value:[], rabbits: []})
    // const form_errors = reactive({value:{}})
    const form_data = reactive({
        id:null,
        product_id:null,
        name:null,
        category:null,
        desc:null,
        price:null,
        stock:null,
        active:null,
        images:[],
    })
    const numberWithCommas = (str) => {
        str += ''
        const number = parseFloat(str.replace(/,/g, ''));
        
        return !isNaN(number)
            ? number.toLocaleString()
            : ''
    };

    onBeforeMount(()=>{
        fetchData()
        get_highest_sale()
        fetchNewAddedProduct()
    })

    // function isPC(){
    //     return window.innerWidth > 960
    // }

    // function fetchRabbits(is_edit){
    //     axios.get(route('rabbit.dropdown', {
    //         breed: form_data.name,
    //         for_product: form_data.id ? null : 1,
    //     }))
    //         .then(res => {
    //             data.rabbits = res.data
    //             if (is_edit){
    //                 showModal.value = true
    //             }
    //         })
    // }

    function fetchNewAddedProduct(){
        axios.get(route('get.newProduct'))
        .then(function(data){
            new_added_prod.value = data.data
        })
    }

    function get_highest_sale(){
        axios.get(route('highest.sale'))
        .then(function(data){
            let total=0;
            highest_sale_data.value = data.data.sort(function(a, b){
                return b.total_sale - a.total_sale;
            })
            console.log(data.data)
            
        }).catch(function(error){
            console.log(error)
        })
    }

    // function onNew(){
    //     form_errors.value ={}
    //     form_data.id = null
    //     form_data.product_id = null
    //     form_data.name = null
    //     form_data.category = null
    //     form_data.desc = null
    //     form_data.price = null
    //     form_data.stock = null
    //     form_data.active = null
    //     form_data.images = []
    //     if (imgUpload.value)
    //         imgUpload.value.clearFiles();
    //     showModal.value = true
    // }

    // function onEdit(row){
    //     form_errors.value ={}

    //     form_data.id = row.id
    //     form_data.price = row.price
    //     form_data.category = row.category
    //     form_data.name = row.name
    //     form_data.images = []
    //     if (imgUpload.value)
    //         imgUpload.value.clearFiles();

    //     if (row.category == 'Rabbit'){
    //         form_data.product_id = row.id
    //         fetchRabbits(true)
    //         return
    //     }

    //     form_data.desc = row.desc
    //     form_data.stock = row.stock
    //     form_data.active = row.active
    //     showModal.value = true
    // }

    function fetchData(){
        axios.get(route('product.get', pagination))
            .then(res => {
                data.value = res.data.data
                pagination_total.value = res.data.total
            })
    }

    // function onChangeImage(img){
    //     form_data.images.push(img.raw)
    // }

    // function handleRemove(img){
    //     form_data.images = form_data.images.filter(i=> i != img.raw)
    // }

    // function onSubmit(){
    //     const url = form_data.id ? route('product.update', form_data.id) : route('product.store')
    //     axios.post(url, form_data,{
    //         headers: {
    //             'content-type': 'multipart/form-data'
    //         }
    //     })
    //         .then(() => {
    //             fetchData()
    //             showModal.value = false
    //         })
    //         .catch(err => {
    //             form_errors.value = err.response.data.errors
    //         })
    // }

    // function onDelete(id){
    //     axios.post(route('product.delete', id))
    //         .then(() => {
    //             fetchData()
    //         })
    //         .catch(err => {
    //             console.log(err);
    //         })
    // }
    </script>
