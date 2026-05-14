<template>
    <AuthLayout>
        <div class="flex justify-between flex-wrap">
            <div class="text-lg font-semibold">Sales Report</div>
        </div>

        <div class="flex gap-2 flex-wrap">
            <el-date-picker
                v-model="range.from"
                type="date"
                placeholder="Enter date from"
                :clearable="false"
                @change="fetchData"
            />
            <el-date-picker
                v-model="range.until"
                type="date"
                placeholder="Enter date until"
                :clearable="false"
                @change="fetchData"
            />
            <el-button type="primary" @click="onPrint">
                <i class="fas fa-print mr-2"></i>
                Print
            </el-button>
        </div>

        <el-card class="mt-4" shadow="always">
            <el-table
                :data="data.value"
                row-key="id"
                header-row-class-name="thead-light"
            >
                <el-table-column
                    width="120px"
                    label="Date"
                >
                    <template v-slot="{ row }">
                        {{ row.order.created_at ? row.order.created_at.substring(0, 10) : ''}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="130px" 
                    label="Customer"
                >
                    <template v-slot="{ row }">
                        {{row.order.user.name}}
                    </template>
                </el-table-column>

                <el-table-column 
                    width="130px" 
                    label="Category"
                >
                    <template v-slot="{ row }">
                        {{row.product.category}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="200px" 
                    label="Item Name"
                >
                    <template v-slot="{ row }">
                        {{row.product.name}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="200px" 
                    label="Amount"
                >
                    <template v-slot="{ row }">
                        ₱{{ numberWithCommas((row.product.price * row.qty) + "")}}
                    </template>
                </el-table-column>

                <!-- <el-table-column 
                    min-width="200px" 
                    label="Remarks"
                >
                    <template v-slot="{ row }">
                        {{row.remarks}}
                    </template>
                </el-table-column> -->
            </el-table>
            <div class="w-full p-1 text-center text-gray-600 text-sm border-t border-gray-2 00">
                Total: ₱{{ numberWithCommas(total) }}
            </div>
        </el-card>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { onBeforeMount, reactive, ref } from 'vue';
import axios from 'axios';

    const {header,logo, setting,base_img_path} = defineProps(['header','logo', 'setting','base_img_path'])
    
    const today = new Date()
    const total = ref('0')

    const range = reactive({
        from: today,
        until: today
    })
    const data = reactive({value:[]})

    onBeforeMount(fetchData)

    const numberWithCommas = (str) => {
        const number = parseFloat(str.replace(/,/g, ''));
        
        return !isNaN(number)
            ? number.toLocaleString()
            : ''
    };

    function get(){
        let v = ''
        data.value.forEach(row => {
            v += `<tr>\
                    <td>${row.order.created_at ? row.order.created_at.substring(0, 10) : ''}</td>\
                    <td>${row.order.user.name}</td>\
                    <td>${row.product.category}</td>\
                    <td>${row.product.name}</td>\
                    <td>₱${numberWithCommas((row.product.price * row.qty) + "")}</td>\
                </tr>`
        });

        return v;
    }
    
    function onPrint(){
        const tab = window.open('', '_blank')

        tab.document.open()
        tab.document.write(
            `<!DOCTYPE html>
            <html>
                <head>
                    <meta charset="utf-8">
                    <meta name="viewport" content="width=device-width, initial-scale=1">
                    <title>Print</title>
                    <style>
                        table{
                            border-collapse: collapse;
                        }
                        table, th, td{
                            border: 1px solid darkgray;
                            padding: 5px;
                        }
                        td{
                            text-align: center;
                        }
                        @media print {
                            @page {
                                size: auto;  
                                margin: 0;   
                            }
                            @page :first {
                                margin-top: 0 !important;
                                margin-bottom: 0 !important;
                                size: auto; /* Reset size for first page */
                            }

                            @page :left {
                                margin-top: 0 !important;
                                margin-bottom: 0 !important;
                                size: auto; /* Reset size for left pages */
                            }

                            @page :right {
                                margin-top: 0 !important;
                                margin-bottom: 0 !important;
                                size: auto; /* Reset size for right pages */
                            }
                            * {
                                -webkit-print-color-adjust:exact;
                                print-color-adjust:exact;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div style="padding: 10px;">
                        <div style="position: relative; display: flex; justify-content: space-between">
                            <img style="width:200px" src="${base_img_path}website/${setting.logo}">
                            <img src="${logo}" style="width: 70px; top: 7px; border-width: 1px; border-color: black; right: 20px; position: absolute; border-radius: 9999px;" alt="">
                        </div>
                        <div style="background-color: black; height: 3px; width: 100%; margin-top: 4px;">.</div>

                        <div style="height:calc(100vh - 250px)">
                            <div style="font-size: 20px; text-align: center; margin-top: 10px;">${setting.name}</div>
                            <div style="font-size: 20px; font-weight: 700; text-align: center;">Sales Report </div>
                            <div style="font-size: 15px; text-align: center; margin-top: 30px; margin-bottom: 5px;">
                                Date ${range.from.toString().substring(0, 15)} and ${range.until.toString().substring(0, 15)}
                            </div>
                            <hr>
                            <table style="width: 100%;">
                                <thead>
                                    <th>Date</th>
                                    <th>Customer</th>
                                    <th>Category</th>
                                    <th>Item Name</th>
                                    <th>Amount</th>
                                </thead>
                                <tbody>
                                    ${get()}
                                    <tr><td colspan="5" style="font-weight: 600">Total: ₱${total.value}</td></tr>
                                </tbody>
                            </table>
                        </div>

                        <div style="display: flex; justify-content: space-between; gap:10px;">
                            <div style="display: block; flex: 1 1 0%">
                                <div style="font-weight: bold; margin-bottom: 10px;">
                                    ${setting.name}
                                </div>
                                <div>
                                    The goal of ${setting.name} is to share our lineage with people who also want to start a Rabbitan either in the yard or on a large scale. You can be sure that every rabbit in our Rabbitan is good and quality.
                                </div>
                            </div>

                            <div style="height: 100px; width: 3px; background-color: black; margin:5px"></div>

                            <div style="display: block; text-align: end; ">
                                <div style="font-weight: bold; margin-bottom: 10px;">
                                    CONTACT US
                                </div>

                                <div>      
                                    Roxas, San Mariano, Orkidyas Street.
                                </div>
                                <div>
                                    0995 885 3635
                                </div>
                                <div>
                                    ${setting.name}
                                </div>
                            </div>
                        </div>
                    </div>
                </body>
            </html>`
        )

        tab.document.close()

        tab.onload = () => {
            tab.print();
            tab.close();
        };
    }

    function fetchData(){
        let t = 0
        axios.get(route('product.get-range', {
            from: (range.from === today ? null : range.from),
            until: (range.until === today ? null : range.until),
        }))
            .then(res => {
                data.value = res.data
                for(const i in res.data){
                    const row = res.data[i]
                    t += row.product.price * row.qty
                }
                total.value = t + ""
            })
    }
    
</script>


