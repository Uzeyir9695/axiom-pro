<script setup>
import { Link, router } from '@inertiajs/vue3'

function showVideos() {
    router.visit(route('videos.index'));
}
</script>

<template>
    <header class="relative sticky top-0 container mx-auto w-full max-sm:flex-col max-sm:h-auto h-24 rounded-lg bg-slate-800 text-white font-bold flex items-center justify-center gap-x-6 z-50">
        <Link @click="showVideos" href="#">Our Lessons</Link>
        <Link href="#" >Buy Crypto</Link>
        <Link href="#" >Markets</Link>
        <Link href="#" >Trade</Link>
        <Link href="#" >Futures</Link>
        <Link href="#" >Launch</Link>
        <Link href="#" >Earn</Link>
        <Link href="#" >About Us</Link>
    </header>
</template>

<style scoped>

</style>
