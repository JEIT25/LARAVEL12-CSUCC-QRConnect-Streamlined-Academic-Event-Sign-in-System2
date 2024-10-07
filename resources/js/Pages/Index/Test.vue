<template>
    <h1>{{ total}}</h1>
    <p>{{ page.props.logoUrl }}</p>
 <button v-on:click="increament">Add</button>
</template>

<script setup>
import { ref,computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const page = usePage()


let number1 = ref(0)
let number2 = ref(0)
let total = computed(() => number1.value + number2.value)

const increament = () => {
    number1.value++
    number2.value++

    console.log(number1.value,number2.value,total.value)
}


</script>