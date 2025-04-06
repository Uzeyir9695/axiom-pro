<script setup>
import {router} from '@inertiajs/vue3'
import VideoPlayer from "./VideoPlayer.vue";
import {ref, watch} from "vue";

const props = defineProps({
    video: {
        type: Object,
        required: true,
    },
    name: {
        type: String,
        default: 'Crypto School'
    },
    showLogo:  {
        type: Boolean,
        default: true
    },
    moveLogoToRight: Boolean,
})

const rate = ref(0);

watch(rate, (rate) => {
    rateVideo(rate);
});

function rateVideo(rate) {
    router.post(route('rate.video', {video: props.video.id, rate: rate}))
}
</script>

<template>
    <div v-if="video" class="max-w-3xl mx-auto p-6">
        <div class="flex justify-between items-center mb-4" :class="{'justify-end': !showLogo}">
            <div v-if="showLogo" class="bg-slate-800 w-20 h-20 flex items-center rounded-full" :class="{'order-2': moveLogoToRight}">
                <img src="/logo/crypto_school_logo.png" alt="logo" class="object-cover rounded-full w-full h-full shadow-lg shadow-white/17">
            </div>
            <h2 class="text-xl font-bold p-2 rounded-full text-slate-300 shadow-lg shadow-white/17">{{ name }}</h2>
        </div>

    <!-- Video Player -->
        <slot name="video-player">
            <VideoPlayer
                :video-url="video.video_url"
                :logo-url="'/logo/crypto_school_logo.png'"
            />
        </slot>

    <!-- Title & Description Below Video -->
    <div class="mt-6">
        <h2 class="text-2xl font-bold text-slate-300">{{ video.title }}</h2>
        <p class="mt-2 text-slate-300">{{ video.description }}</p>
        <span class="text-sm">Total Reviews: {{ video.rates_sum_rate > 0 ? video.rates_sum_rate : 0 }}</span>
    </div>
</div>
</template>

<style scoped>

</style>
