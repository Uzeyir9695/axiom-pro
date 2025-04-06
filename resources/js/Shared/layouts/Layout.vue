<script setup>
import Navbar from "../Navbar.vue";
import Footer from "../Footer.vue";
</script>

<template>
<div class="bg-slate-900 text-white/75">
    <Navbar></Navbar>
    <slot />
    <Footer></Footer>
</div>
</template>

<style scoped>

</style>
