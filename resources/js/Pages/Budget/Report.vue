<template>
    <AuthLayout>
        <div class="flex justify-between flex-wrap">
            <div class="text-lg font-semibold">{{is_budget ? 'Budget' : 'Expense'}} Report</div>
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
                    min-width="220px"
                    label="Date"
                >
                    <template v-slot="{ row }">
                        {{ row.created_at ? row.created_at.substring(0, 10) : ''}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="130px" 
                    label="Category"
                >
                    <template v-slot="{ row }">
                        {{row.category.name}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="200px" 
                    label="Amount"
                >
                    <template v-slot="{ row }">
                        {{numberWithCommas(row.amount)}}
                    </template>
                </el-table-column>

                <el-table-column 
                    min-width="200px" 
                    label="Remarks"
                >
                    <template v-slot="{ row }">
                        {{row.remarks}}
                    </template>
                </el-table-column>
            </el-table>
        </el-card>
    </AuthLayout>
</template>

<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { onBeforeMount, reactive, ref } from 'vue';
import axios from 'axios';

    const {is_budget,header,logo, setting ,base_img_path} = defineProps(['is_budget', 'header','logo', 'setting','base_img_path'])

    const today = new Date()

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
        let total = 0
        data.value.forEach(val => {
            v += `<tr>\
                    <td>${val.created_at ? val.created_at.replace('T', ' ').substring(0, 19) : ''}</td>\
                    <td>${val.category.name}</td>\
                    <td>${val.amount}</td>\
                    <td>${val.remarks}</td>\
                </tr>`
            total += parseInt(val.amount)
        });

        return {g: v, t: total};
    }
    
    function onPrint(){
        const tab = window.open('', '_blank')

        const  h = get()

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
                            *{
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
                            <div style="font-size: 20px; font-weight: 700; text-align: center;"> ${is_budget ? 'Budget' : 'Expense'} Report </div>
                            <div style="font-size: 15px; text-align: center; margin-top: 30px; margin-bottom: 5px;">
                                Date ${range.from.toString().substring(0, 15)} and ${range.until.toString().substring(0, 15)}
                            </div>
                            <hr>
                            <table style="width: 100%;">
                                <thead>
                                    <th>Date</th>
                                    <th>Category</th>
                                    <th>Amount</th>
                                    <th>Remarks</th>
                                </thead>
                                <tbody>
                                    ${h.g}
                                    <tr><td colspan="4" style="font-weight: 600">Total: ${h.t}</td></tr>
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
        axios.get(route('budget.get-range', {
            is_budget: is_budget,
            from: (range.from === today ? null : range.from),
            until: (range.until === today ? null : range.until),
        }))
            .then(res => {
                data.value = res.data
            })
    }
    
</script>


