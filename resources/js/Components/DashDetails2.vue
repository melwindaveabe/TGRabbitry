<template>
    <div style="width: 300px;">
        <div class="border border-black p-5 flex justify-between rounded-md items-center" :style="`background-color: ${color ?? 'white'};`">
            <div class="">
                <div class="text-[20px]">
                    {{ title }}
                </div>
                <div class="text-[15px]">Total</div>
            </div>
            <div class="text-[30px]">
                {{ value }}
            </div>
        </div>
    </div>
</template>

<script setup>

    const {title, value, color} = defineProps(['title', 'value', 'color'])

</script>